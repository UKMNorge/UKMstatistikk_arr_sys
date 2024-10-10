<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true">
            <div class="as-margin-bottom-space-2 as-margin-top-space-2">
                <h4>{{ selectedKommune.title }}</h4>
            </div>
            <MultiBarChart ref="chart"
                :labels="getLabels()" 
                :dataset="getDataset()"
            />
        </div>
    </div>
</template>

<script lang="ts">
import MultiBarChart from '../charts/MultiBarChart.vue';
import type Kommune from '../../objects/Kommune'; // Ensure Kommune is imported correctly
import type { PropType } from 'vue';  // Use type-only import for PropType


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
        MultiBarChart : MultiBarChart,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommunerData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
            alleSjangere: [] as any
        }
    },
    methods: {
        async init() {
            // Empty old data
            this.dataFetched = false;
            this.kommunerData = [];

            for(let year of this.selectedYears) {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'kommune/sjangerfordeling',
                    kommuneId: this.selectedKommune.id,
                    season: year,
                    unike: true
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
                
                console.log(results);
                for(let sjanger in results) {
                    if(this.alleSjangere.indexOf(sjanger) == -1) {
                        this.alleSjangere[sjanger] = '';
                    }
                }

                var arr = {
                    kommune: this.selectedKommune,
                    year: year,
                    data: results
                }

                if(this.kommunerData[year] == undefined) {
                    this.kommunerData[year] = [];
                }

                this.kommunerData[year].push(arr);
            }

            console.log('1918');
            console.log(this.kommunerData);

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

            for(let kData in this.kommunerData) {
                for(let d of this.kommunerData[kData]) {
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


