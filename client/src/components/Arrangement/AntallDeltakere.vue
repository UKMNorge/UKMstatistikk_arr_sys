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
import type Arrangement from '../../objects/Arrangement'; // Ensure Arrangement is imported correctly
import LoadingComponent from '../Other/LoadingComponent.vue';
import { getRandomColor } from '../../utils/Colors';  

export default {
    props: {
        selectedArrangement: {
            type: Object as () => any,
            required: true
        },
        // selectedYears: {
        //     type: Array as () => number[],
        //     required: true
        // }
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
            kommunerData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
            colors : ['#FF6384', '#36A2EB', '#FFCE56'],
            fetchingStarted: false,
            alleKommuner: {} as any,
            season: null as any,
        }
    },
    methods: {
        async init() {
            // Empty old data
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.kommunerData = {};

            var data = {
                action: 'UKMstatistikk_ajax',
                controller: 'arrangement/antallDeltakere',
                plId: this.selectedArrangement,
                unike: true
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            this.season = results.season;

            if (!this.alleKommuner[this.selectedArrangement]) {
                this.alleKommuner[this.selectedArrangement] = {};
            }
            if (!this.alleKommuner[this.selectedArrangement]) {
                this.alleKommuner[this.selectedArrangement] = {};
            }
            for(let oldKomKey in results.kommuner) {
                this.alleKommuner[this.selectedArrangement][results.kommuner[oldKomKey]] = results.kommuner[oldKomKey];
            }

            var arr = {
                kommune: this.selectedArrangement,
                year: this.season,
                antall: results.antall
            }

            if(this.kommunerData[this.season] == undefined) {
                this.kommunerData[this.season] = [];
            }

            this.kommunerData[this.season].push(arr);

            this.fetchingStarted = false;
            this.dataFetched = true;

        },
        getLabels() : any {
            let retArr = [];

            // for(let year of this.getAllSelectedYears()) {
            //     retArr.push(year.toString());
            // }

            return [this.season];
        },
        getDataset() : any { 
            var retArr = [] as any;
            var singleRetArr = [] as any;
            
            var count = 0;
            let colorId = 0;
            // for(let year of this.selectedYears) {
                var dataKomm = [];
                for(let data of this.kommunerData[this.season]) {
                    dataKomm.push(data.antall);
                    singleRetArr.push(data.antall);

                }
            // }

                retArr.push({
                    label: 'Antall deltakere ',
                    data: singleRetArr, 
                    backgroundColor: getRandomColor(1, 0),
                });

            return retArr;
        },
        titleCallbackFunction(tooltipItem : any) {
            return tooltipItem[0].dataset.label;
        }
    }
}
</script>


<style scoped>

</style>


