<template>
    <div>
        <div v-if="dataFetched == true && startYear == selectedYears[0] && endYear == selectedYears[selectedYears.length-1]">
            <div class="as-margin-bottom-space-4 as-margin-top-space-2">
                <h4>
                    <template v-if="selectedYears.length > 1">
                        {{ selectedKommune.title }} kjønnsfordeling fra {{ selectedYears[0] }} til {{ selectedYears[selectedYears.length-1] }}
                    </template>
                    <template v-else>
                        {{ selectedKommune.title }} kjønnsfordeling for {{ selectedYears[0] }}
                    </template>
                </h4>
            </div>
            <Pie ref="chart"
                :labels="getYearsRange()" 
                :dataset="getDataset()"
            />
        </div>
        <div v-else-if="fetchingStarted">
            <div class="as-margin-bottom-space-4 as-margin-top-space-2">
                <h4>
                    <template v-if="selectedYears.length > 1">
                        {{ selectedKommune.title }} kjønnsfordeling fra {{ selectedYears[0] }} til {{ selectedYears[selectedYears.length-1] }}
                    </template>
                    <template v-else>
                        {{ selectedKommune.title }} kjønnsfordeling for {{ selectedYears[0] }}
                    </template>
                </h4>
            </div>
            <LoadingComponent />
        </div>
    </div>
</template>

<script lang="ts">
import Pie from '../charts/Pie.vue';
import type Kommune from '../../objects/Kommune'; // Ensure Kommune is imported correctly
import type { PropType } from 'vue';  // Use type-only import for PropType
import LoadingComponent from '../Other/LoadingComponent.vue';


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
        LoadingComponent : LoadingComponent,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommuneData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
            startYear: 0,
            endYear: 0,
            fetchingStarted: false,
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
            }

            this.fetchingStarted = false;
            this.dataFetched = true;

        },
        getYearsRange() : Array<string> {
            return ['Jenter', 'Gutter', 'Ukjent'];
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
        getRandomColor(opacity = 1): string {
            // Random hue value between 0 and 360 (full spectrum of colors)
            const hue = Math.floor(Math.random() * 360);
            
            // Medium saturation for balanced colors (between 40% and 70%)
            const saturation = Math.floor(Math.random() * 10) + 40; // Range: [40, 70]
            
            // Medium lightness for slightly vibrant colors (between 50% and 70%)
            const lightness = Math.floor(Math.random() * 21) + 50; // Range: [50, 70]

            return `hsla(${hue}, ${saturation}%, ${lightness}%, ${opacity})`;
        }
    }
}
</script>


<style scoped>

</style>

