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
import LoadingComponent from '../Other/LoadingComponent.vue';
import type { PropType } from 'vue';  // Use type-only import for PropType
import type Fylke from '../../objects/Fylke';
import { getRandomColor } from '../../utils/Colors';


export default {
    props: {
        selectedFylkeId: {
            type: Object as any,
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
            fylkeData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
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
            this.fylkeData = [];

            for(let year of this.selectedYears) {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'fylke/antallDeltakereFylke',
                    fylkeId: this.selectedFylkeId,
                    season: year,
                    unike: true
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                var arr = {
                    fylkeNavn: results.fylkeNavn,
                    year: year,
                    antall: results.antall
                }

                if(this.fylkeData[year] == undefined) {
                    this.fylkeData[year] = [];
                }

                this.fylkeData[year].push(arr);
            }

            this.fetchingStarted = false;
            this.dataFetched = true;

        },
        getLabels() : any {
            let retArr = [];
            
            for(let year of this.selectedYears) {
                retArr.push(year.toString());
            }

            return retArr;
        },
        getDataset() : any { 
            var retArr = [] as any;
            
            let fylkeAntallData = [];
            let fylkeNavn = 'Unike deltakere fra fylkesarrangementer i ' + this.fylkeData[this.selectedYears[0]][0].fylkeNavn;
            for(let year of this.selectedYears) {
                fylkeAntallData.push(this.fylkeData[year][0].antall);
            }
            retArr.push({
                label: fylkeNavn,
                data: fylkeAntallData, 
                backgroundColor: getRandomColor(1, 0),
            });

            return retArr;
        }
    }
}
</script>


<style scoped>

</style>