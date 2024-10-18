<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <MultiBarChart ref="chart"
                :labels="getLabels()" 
                :dataset="getDataset()"
            />
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

    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommunerData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
            colors : ['#FF6384', '#36A2EB', '#FFCE56'],
            fetchingStarted: false,
        }
    },
    methods: {
        async init() {
            // Empty old data
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.kommunerData = [];

            for(let kommune of this.selectedKommuner) {
                for(let year of this.selectedYears) {
                    console.warn("AJAX...");
                    var data = {
                        action: 'UKMstatistikk_ajax',
                        controller: 'kommune/antallDeltakere',
                        kommuneId: kommune.id,
                        season: year,
                        unike: true
                    };

                    var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

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

            this.fetchingStarted = false;
            this.dataFetched = true;

        },
        getLabels() : any {
            let retArr = [];
            for(let kommune of this.selectedKommuner) {
                retArr.push(kommune.title);
            }

            return retArr;
        },
        getDataset() : any { 
            var retArr = [] as any;
            
            var count = 0;
            let colorId = 0;
            for(let year of this.selectedYears) {
                var dataKomm = [];
                for(let data of this.kommunerData[year]) {
                    dataKomm.push(data.antall);
                }

                retArr.push({
                    label: year.toString(),
                    data: dataKomm, 
                    backgroundColor: getRandomColor(1, colorId),
                });
                count++;
                colorId++;
            }

            return retArr;
        }
    }
}
</script>


<style scoped>

</style>


