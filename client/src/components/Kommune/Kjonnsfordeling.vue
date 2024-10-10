<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true">
            <div class="as-margin-bottom-space-2 as-margin-top-space-2">
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
    </div>
</template>

<script lang="ts">
import Pie from '../charts/Pie.vue';
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
        Pie : Pie,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommuneData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
        }
    },
    methods: {
        async init() {
            // Empty old data
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

            // var dataArr = [] as any;

            // for(let kData in this.kommuneData) {
            //     for(let d of this.kommuneData[kData]) {
            //         let year = d.year;

            //         dataArr['' + year] = [];
            //         dataArr['' + year]['< 10'] = 0;
            //         dataArr['' + year]['10-11'] = 0;
            //         dataArr['' + year]['12-13'] = 0;
            //         dataArr['' + year]['14-15'] = 0;
            //         dataArr['' + year]['16-17'] = 0;
            //         dataArr['' + year]['18-19'] = 0;
            //         dataArr['' + year]['20+'] = 0;
                    

            //         for(let alder of d.data) {
            //             var idAlder = '';

            //             if(alder.age < 10) {
            //                 idAlder = '< 10'
            //             } else if(alder.age > 19) {
            //                 idAlder = '20+'
            //             } else {
            //                 idAlder = alder.age % 2 !== 0 ? (parseInt(alder.age) - 1) + '-' + alder.age : alder.age + '-' + (parseInt(alder.age) + 1);
            //             }

            //             dataArr['' + year][idAlder] = dataArr['' + year][idAlder] ? dataArr['' + year][idAlder] + parseInt(alder.antall) : parseInt(alder.antall);;
            //         }
            //     }
            // }

            // var retArr = [] as any;

            // for(let key in dataArr) {
            //     let kData = dataArr[key];
                
            //     let opacityColor = 1; //kData.kommune.id == 0 ? 1 : .4;
            //     let color = this.getRandomColor(opacityColor);
            //     retArr.push(
            //         {
            //             label: key,
            //             borderColor: color,
            //             backgroundColor: color,
            //             data: (<any>Object).values(kData),
            //             fill: true,
            //         }
            //     )
            // }

            // return retArr;
            
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

