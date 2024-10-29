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

            <div>
                <FlereKommunerMessage :alleKommuner="alleKommuner" :selectedKommuner="selectedKommuner" />
            </div>


        </div>
        <div v-else-if="fetchingStarted">
            <LoadingComponent />
        </div>
        
    </div>
</template>

<script lang="ts">
import MultiBarChart from '../charts/MultiBarChart.vue';
import type Kommune from '../../objects/Kommune'; // Ensure Kommune is imported correctly
import LoadingComponent from '../Other/LoadingComponent.vue';
import { getRandomColor } from '../../utils/Colors';  
import FlereKommunerMessage from '../Other/FlereKommunerMessage.vue';

export default {
    props: {
        selectedKommuner: {
            type: Array as () => Kommune[],
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
        FlereKommunerMessage : FlereKommunerMessage,

    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommunerData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
            colors : ['#FF6384', '#36A2EB', '#FFCE56'],
            fetchingStarted: false,
            alleKommuner: {} as any,
        }
    },
    methods: {
        async init() {
            // Empty old data
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.kommunerData = {};

            for(let kommune of this.selectedKommuner) {
                for(let year of this.selectedYears) {
                    var data = {
                        action: 'UKMstatistikk_ajax',
                        controller: 'kommune/antallDeltakere',
                        kommuneId: kommune.id,
                        season: year,
                        unike: true
                    };

                    var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                    if (!this.alleKommuner[kommune.id]) {
                        this.alleKommuner[kommune.id] = {};
                    }
                    if (!this.alleKommuner[kommune.id][year]) {
                        this.alleKommuner[kommune.id][year] = {};
                    }
                    for(let oldKomKey in results.kommuner) {
                        this.alleKommuner[kommune.id][year][results.kommuner[oldKomKey]] = results.kommuner[oldKomKey];
                    }

                    var arr = {
                        kommune: kommune,
                        year: year,
                        antall: results.antall
                    }

                    if(this.kommunerData[year] == undefined) {
                        this.kommunerData[year] = [];
                    }

                    this.kommunerData[year].push(arr);
                }
            }

            console.log(1246);
            console.log(this.alleKommuner);

            this.fetchingStarted = false;
            this.dataFetched = true;

        },
        getLabels() : any {
            let retArr = [];

            // If it is 1 kommune selected, show years as labels
            if(this.selectedKommuner.length == 1) {
                for(let year of this.getAllSelectedYears()) {
                    retArr.push(year.toString());
                }
            } else {
                for(let kommune of this.selectedKommuner) {
                    retArr.push(kommune.title);
                }
            }

            return retArr;
        },
        getDataset() : any { 
            var retArr = [] as any;
            var singleRetArr = [] as any;
            
            var count = 0;
            let colorId = 0;
            for(let year of this.selectedYears) {
                var dataKomm = [];
                for(let data of this.kommunerData[year]) {
                    dataKomm.push(data.antall);
                    singleRetArr.push(data.antall);

                }
                
                if(this.selectedKommuner.length > 1) {
                    retArr.push({
                        label: year.toString(),
                        data: dataKomm, 
                        backgroundColor: getRandomColor(1, colorId),
                    });
                }
                count++;
                colorId++;
            }

            if(this.selectedKommuner.length == 1) {
                retArr.push({
                    label: 'Antall deltakere i ' + this.selectedKommuner[0].title,
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


