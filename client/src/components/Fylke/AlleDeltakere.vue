<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <MultiBarChart ref="chart"
                :labels="getLabels()" 
                :dataset="getDataset()"
                :labelCallbackFunction="(tooltipItem) => `${tooltipItem.raw} deltaker${tooltipItem.raw > 1 ? 'e' : ''}`"
                :titleCallbackFunction="titleCallbackFunction"
            />
        </div>
        <div v-else-if="fetchingStarted">
            <LoadingComponent />
        </div>
        
    </div>
</template>

<script lang="ts">
import MultiBarChart from '../charts/MultiBarChart.vue';
import LoadingComponent from '../Other/LoadingComponent.vue';
import { getRandomColor } from '../../utils/Colors';  

export default {
    props: {
        selectedFylker: {
            type: Array as () => number[],
            required: true
        },
        selectedYears: {
            type: Array as () => number[],
            required: true
        }
    },
    mounted() {

    },
    components: {
        MultiBarChart : MultiBarChart,
        LoadingComponent : LoadingComponent,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            fylkerData: {} as any,
            dataFetched: false,
            colors : ['#FF6384', '#36A2EB', '#FFCE56'],
            fetchingStarted: false,
            alleFylker: {} as any,
        }
    },
    methods: {
        async init() {
            // Empty old data
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.fylkerData = {};

            for(let fylke of this.selectedFylker) {
                for(let year of this.selectedYears) {
                    var data = {
                        action: 'UKMstatistikk_ajax',
                        controller: 'fylke/antallDeltakere',
                        fylkeId: fylke,
                        season: year,
                        unike: true
                    };

                    var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                    if (!this.alleFylker[fylke]) {
                        this.alleFylker[fylke] = {};
                    }
                    if (!this.alleFylker[fylke][year]) {
                        this.alleFylker[fylke][year] = {};
                    }

                    var arr = {
                        fylke: fylke,
                        year: year,
                        antall: results.antall
                    }

                    if(this.fylkerData[year] == undefined) {
                        this.fylkerData[year] = [];
                    }

                    this.fylkerData[year].push(arr);
                }
            }

            console.log(1246);
            console.log(this.alleFylker);

            this.fetchingStarted = false;
            this.dataFetched = true;

        },
        getLabels() : any {
            let retArr = [];

            if(this.selectedFylker.length == 1) {
                for(let year of this.getAllSelectedYears()) {
                    retArr.push(year.toString());
                }
            } else {
                let counter = 0;
                for(let fylke of this.selectedFylker) {
                    retArr.push(this._getFylkeById(parseInt((<any>fylke))));
                    counter++;
                }
            }

            return retArr;
        },
        _getFylkeById(fylkeId : number) : string {
            let omrader = (<any>window).ukm_statistikk_klient.omrade
            for(let omrade of omrader) {
                if(omrade.type == 'fylke' && omrade.id == fylkeId) {
                    return omrade.name;
                }
            }
            return 'Ukjent';
        },
        getDataset() : any { 
            var retArr = [] as any;
            var singleRetArr = [] as any;
                        
            var count = 0;
            let colorId = 0;
            for(let year of this.selectedYears) {
                var dataKomm = [];
                for(let data of this.fylkerData[year]) {
                    dataKomm.push(data.antall);
                    singleRetArr.push(data.antall);

                }
                
                if(this.selectedFylker.length > 1) {
                    retArr.push({
                        label: year.toString(),
                        data: dataKomm, 
                        backgroundColor: getRandomColor(1, colorId),
                    });
                }
                count++;
                colorId++;
            }

            console.log(retArr);

            if(this.selectedFylker.length == 1) {
                retArr.push({
                    label: 'Antall deltakere i ' + this._getFylkeById(this.selectedFylker[0]),
                    data: singleRetArr, 
                    backgroundColor: getRandomColor(1, 0),
                });
            }

            return retArr;
        },
        getAllSelectedYears(): number[] {
            var firstYear = this.selectedYears[0];
            var lastYear = this.selectedYears[this.selectedYears.length - 1];
            var years = [];
            for (let i = firstYear; i <= lastYear; i++) {
                years.push(i);
            }

            return years;
        },
        titleCallbackFunction(tooltipItem : any) {
            return tooltipItem[0].dataset.label;
        }
    }
}
</script>


<style scoped>

</style>


