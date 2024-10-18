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
import type Fylke from '../../objects/Fylke'; 
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
        MultiBarChart : MultiBarChart,
        LoadingComponent : LoadingComponent,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            fylkeData: {} as any,
            dataFetched: false,
            alleSjangere: [] as any,
            fetchingStarted: false,
        }
    },
    methods: {
        async init() {
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.fylkeData = [];

            for(let year of this.selectedYears) {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'fylke/sjangerfordeling',
                    fylkeId: this.selectedFylke,
                    season: year,
                    unike: true
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
                
                for(let sjanger in results) {
                    if(this.alleSjangere.indexOf(sjanger) == -1) {
                        this.alleSjangere[sjanger] = '';
                    }
                }

                var arr = {
                    fylke: this.selectedFylke,
                    year: year,
                    data: results
                }

                if(this.fylkeData[year] == undefined) {
                    this.fylkeData[year] = [];
                }

                this.fylkeData[year].push(arr);
            }

            this.fetchingStarted = false;
            this.dataFetched = true;

        },
        getLabels() : Array<string> {
            let retArr = [];
            for(let sjangerKey in this.alleSjangere) {
                retArr.push(sjangerKey);
            }

            return retArr;
        },
        getDataset() : any {

            var dataArr = [] as any;

            for(let kData in this.fylkeData) {
                for(let d of this.fylkeData[kData]) {
                    let year = d.year;

                    dataArr['' + year] = [];

                    for(let sjanger in this.alleSjangere) {
                        dataArr['' + year][sjanger] = [];
                    }

                    for(let key in d.data) {
                        let value = d.data[key];

                        dataArr['' + year][key] = parseInt(value.antall);;
                    }
                }
            }

            var retArr = [] as any;

            for(let key in dataArr) {
                let kData = dataArr[key];
                
                let opacityColor = 1;
                let color = this.getRandomColor(opacityColor);
                retArr.push(
                    {
                        label: key,
                        borderColor: color,
                        backgroundColor: color,
                        data: (<any>Object).values(kData),
                        fill: true,
                    }
                )
            }

            return retArr;

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


