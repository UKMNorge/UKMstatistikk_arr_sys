<template>
    <div>
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <Pie ref="chart"
                :labels="getYearsRange()" 
                :dataset="getDataset()"
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
import LoadingComponent from '../Other/LoadingComponent.vue';


export default {
    props: {
        selectedFylke: {
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
        Pie : Pie,
        LoadingComponent : LoadingComponent,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            fylkeData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
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

            this.fylkeData = [];
            this.fylkeData['female'] = 0;
            this.fylkeData['male'] = 0;
            this.fylkeData['unknown'] = 0;

            for(let year of this.selectedYears) {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'fylke/kjonnsfordeling',
                    fylkeId: this.selectedFylke,
                    season: year,
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                this.fylkeData['female'] += results['female'] ? results['female'] : 0;
                this.fylkeData['male'] += results['male'] ? results['male'] : 0;
                this.fylkeData['unknown'] += results['unknown'] ? results['unknown'] : 0;
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
                data: [this.fylkeData['female'], this.fylkeData['male'], this.fylkeData['unknown']], // Data for pie slices
                backgroundColor: [
                    '#fbcce4',
                    '#afd7ff',
                    '#ededed'
                ]
            }];
            
        }
    }
}
</script>


<style scoped>

</style>

