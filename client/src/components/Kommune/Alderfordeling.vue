<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <div class="as-margin-bottom-space-2">
                <h4>{{ selectedKommune.title }}</h4>
            </div>
            <MultiBarChart ref="chart"
                :labels="getYearsRange()" 
                :dataset="getDataset()"
                :labelCallbackFunction="(tooltipItem) => `${tooltipItem.raw} deltaker${tooltipItem.raw > 1 ? 'e' : ''}`"
            />

            <div>
                <FlereKommunerMessage :alleKommuner="alleKommuner" :selectedKommuner="[selectedKommune]" />
            </div>
        </div>
        <div v-else-if="fetchingStarted">
            <LoadingComponent />
        </div>
    </div>
</template>

<script lang="ts">
import MultiBarChart from '../charts/MultiBarChart.vue';
import type Kommune from '../../objects/Kommune'; // Ensure Kommune is imported correctly
import type { PropType } from 'vue';  // Use type-only import for PropType
import LoadingComponent from '../Other/LoadingComponent.vue';
import { getRandomColor } from '../../utils/Colors';
import FlereKommunerMessage from '../Other/FlereKommunerMessage.vue';

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
        LoadingComponent : LoadingComponent,
        FlereKommunerMessage : FlereKommunerMessage,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommunerData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
            fetchingStarted: false,
            alleKommuner: {} as any,

        }
    },
    methods: {
        async init() {
            // Empty old data
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.kommunerData = [];

            for(let year of this.selectedYears) {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'kommune/aldersfordeling',
                    kommuneId: this.selectedKommune.id,
                    season: year,
                    unike: true
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                if (!this.alleKommuner[this.selectedKommune.id]) {
                    this.alleKommuner[this.selectedKommune.id] = {};
                }
                if (!this.alleKommuner[this.selectedKommune.id][year]) {
                    this.alleKommuner[this.selectedKommune.id][year] = {};
                }
                for(let oldKomKey in results.kommuner) {
                    this.alleKommuner[this.selectedKommune.id][year][results.kommuner[oldKomKey]] = results.kommuner[oldKomKey];
                }

                var arr = {
                    kommune: this.selectedKommune,
                    year: year,
                    data: results.data
                }

                if(this.kommunerData[year] == undefined) {
                    this.kommunerData[year] = [];
                }

                this.kommunerData[year].push(arr);
            }

            this.fetchingStarted = false;
            this.dataFetched = true;


        },
        getYearsRange() : Array<string> {
            return ['< 10', '10-11', '12-13', '14-15', '16-17', '18-19', '20+'];
        },
        getDataset() : any {

            var dataArr = [] as any;

            for(let kData in this.kommunerData) {
                for(let d of this.kommunerData[kData]) {
                    let year = d.year;

                    dataArr['' + year] = [];
                    dataArr['' + year]['< 10'] = 0;
                    dataArr['' + year]['10-11'] = 0;
                    dataArr['' + year]['12-13'] = 0;
                    dataArr['' + year]['14-15'] = 0;
                    dataArr['' + year]['16-17'] = 0;
                    dataArr['' + year]['18-19'] = 0;
                    dataArr['' + year]['20+'] = 0;
                    

                    for(let alder of d.data) {
                        var idAlder = '';

                        if(alder.age < 10) {
                            idAlder = '< 10'
                        } else if(alder.age > 19) {
                            idAlder = '20+'
                        } else {
                            idAlder = alder.age % 2 !== 0 ? (parseInt(alder.age) - 1) + '-' + alder.age : alder.age + '-' + (parseInt(alder.age) + 1);
                        }

                        console.log('IDALDER', idAlder, alder.antall);

                        dataArr['' + year][idAlder] = dataArr['' + year][idAlder] ? dataArr['' + year][idAlder] + parseInt(alder.antall) : parseInt(alder.antall);
                    }
                }
            }

            var retArr = [] as any;
            let colorId = 0;
            for(let key in dataArr) {
                let kData = dataArr[key];
                
                let opacityColor = 1; //kData.kommune.id == 0 ? 1 : .4;
                let color = getRandomColor(opacityColor, colorId);
                retArr.push(
                    {
                        label: key,
                        borderColor: color,
                        backgroundColor: color,
                        data: (<any>Object).values(kData),
                        fill: true,
                    }
                );
                colorId++;
            }

            return retArr;
            
        }
    }
}
</script>


<style scoped>

</style>


