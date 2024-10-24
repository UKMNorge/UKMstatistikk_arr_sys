<template>
    <div>
        <div class="as-card-1 as-padding-space-3 as-margin-top-space-4" v-if="dataFetched == true && startYear == selectedYears[0] && endYear == selectedYears[selectedYears.length-1]">
            <div class="as-display-flex">
                <div class="as-margin-auto as-margin-right-none">
                    <v-switch 
                        inset 
                        color="primary" 
                        v-model="isBarChart" 
                        label="Stolpediagram">
                    </v-switch>
                </div>
            </div>
            <div class="as-margin-bottom-space-4">
                <h4>
                    <template v-if="selectedYears.length > 1">
                        {{ selectedKommune.title }} kjønnsfordeling fra {{ selectedYears[0] }} til {{ selectedYears[selectedYears.length-1] }}
                    </template>
                    <template v-else>
                        {{ selectedKommune.title }} kjønnsfordeling for {{ selectedYears[0] }}
                    </template>
                </h4>
            </div>
            <Pie v-if="!isBarChart" ref="chart"
                :labels="getLabels()" 
                :dataset="getDataset()"
            />
            <MultiBarChart v-else ref="chart"
                :labels="getLabels()" 
                :dataset="getDatasetMultibar()"
                :titleCallbackFunction="(tooltipItems) => `${tooltipItems[0].dataset.label} ${tooltipItems[0].label}`"
                :stacked="true"
            />
        </div>

        <div v-else-if="fetchingStarted">
            <LoadingComponent />
        </div>
    </div>
</template>

<script lang="ts">
import Pie from '../charts/Pie.vue';
import type Kommune from '../../objects/Kommune'; // Ensure Kommune is imported correctly
import type { PropType } from 'vue';  // Use type-only import for PropType
import { toRaw } from 'vue';  // Use type-only import for PropType
import LoadingComponent from '../Other/LoadingComponent.vue';
import { getRandomColor } from '../../utils/Colors';
import MultiBarChart from '../charts/MultiBarChart.vue';


export default {
    props: {
        selectedKommune: {
            type: Object as PropType<Kommune>,
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
        Pie : Pie,
        MultiBarChart : MultiBarChart,
        LoadingComponent : LoadingComponent,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommuneData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            kommuneDataYear : {} as any,
            dataFetched: false,
            startYear: 0,
            endYear: 0,
            fetchingStarted: false,
            isBarChart: false,
        }
    },
    methods: {
        async init() {
            
            this.startYear = this.selectedYears[0];
            this.endYear = this.selectedYears[this.selectedYears.length-1];
            
            this.fetchingStarted = true;
            this.dataFetched = false;

            this.kommuneData = [];
            this.kommuneData['female'] = 0;
            this.kommuneData['male'] = 0;
            this.kommuneData['unknown'] = 0;

            this.kommuneDataYear = {} as any;

            for(let year of this.selectedYears) {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'kommune/kjonnsfordeling',
                    kommuneId: this.selectedKommune.id,
                    season: year,
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                this.kommuneData['female'] += results['female'] ? results['female'] : 0;
                this.kommuneData['male'] += results['male'] ? results['male'] : 0;
                this.kommuneData['unknown'] += results['unknown'] ? results['unknown'] : 0;

                this.kommuneDataYear[year] = {} as any;
                this.kommuneDataYear[year]['female'] = results['female'] ?? 0;
                this.kommuneDataYear[year]['male'] = results['male'] ?? 0;
                this.kommuneDataYear[year]['unknown'] = results['unknown'] ?? 0;
            }

            this.fetchingStarted = false;
            this.dataFetched = true;

        },
        getLabels() : Array<string> {
            if(this.isBarChart) {
                return ['Jenter', 'Gutter', 'Ukjent'];
            }
            
            return this.getAllSelectedYears().map(year => year.toString()); 
        },
        getDataset() : any {
            return [{
                label: 'Dataset',
                data: [this.kommuneData['female'], this.kommuneData['male'], this.kommuneData['unknown']], // Data for pie slices
                backgroundColor: [
                    '#fbcce4',
                    '#afd7ff',
                    '#ededed'
                ]
            }];
        },
        getDatasetMultibar() : any {
            let retArr = new Array();

            let arrF = new Array();
            let arrM = new Array();
            let arrU = new Array();

            let colorId = 0;
            for (let year of this.selectedYears) {
                let kjData = this.kommuneDataYear[year];
                
                let dataArr = new Array();
                let f = kjData['female'];
                let m = kjData['male'];
                let u = kjData['unknown'];

                arrF.push(f);
                arrM.push(m);
                arrU.push(u);
            }

            retArr.push({
                label: 'Jenter',
                data: arrF,
                backgroundColor: '#fbcce4',
            });

            retArr.push({
                label: 'Gutter',
                data: arrM,
                backgroundColor: '#afd7ff',
            });

            retArr.push({
                label: 'Ukjent',
                data: arrU,
                backgroundColor: '#ededed',
            });

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
    },
}
</script>


<style scoped>

</style>

