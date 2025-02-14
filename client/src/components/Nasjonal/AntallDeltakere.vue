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
        selectedYears: {
            type: Array as () => number[],
            required: true
        }
    },
    components: {
        MultiBarChart : MultiBarChart,
        LoadingComponent : LoadingComponent,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            antallDeltakereData: {} as any, //{year : number, antall : number}[]
            dataFetched: false,
            fetchingStarted: false,
        }
    },
    methods: {
        async init() {
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.antallDeltakereData = {};

            const requests = this.selectedYears.map(async (year) => {
                const data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'nasjonalt/antallDeltakere',
                    season: year,
                    unike: true
                };
                const results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
                this.antallDeltakereData[year] = [{ year, antall: results.antall }];
            });

            await Promise.all(requests);

            this.fetchingStarted = false;
            this.dataFetched = true;
        },
        getLabels() : any {
            let retArr = [];

            for(let year of this.getAllSelectedYears()) {
                retArr.push(year.toString());
            }
        
            return retArr;
        },
        getDataset() : any { 
            var retArr = [] as any;
            var singleRetArr = [] as any;
            
            for(let year of this.selectedYears) {
                for(let data of this.antallDeltakereData[year]) {
                    singleRetArr.push(data.antall);
                }
            }

            retArr.push({
                label: 'Antall deltakere nasjonalt',
                data: singleRetArr, 
                backgroundColor: getRandomColor(1, 0),
            });

            return retArr;
        },
        getAllSelectedYears(): number[] {
            const minYear = Math.min(...this.selectedYears);
            const maxYear = Math.max(...this.selectedYears);
            return Array.from({ length: maxYear - minYear + 1 }, (_, i) => minYear + i);
        },
        titleCallbackFunction(tooltipItem : any) {
            return tooltipItem[0].dataset.label;
        }
    }
}
</script>


<style scoped>

</style>


