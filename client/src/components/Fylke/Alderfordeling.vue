<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <MultiBarChart ref="chart"
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
import MultiBarChart from '../charts/MultiBarChart.vue';
import LoadingComponent from '../Other/LoadingComponent.vue';
import { getRandomColor } from '../../utils/Colors';

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
                    controller: 'fylke/aldersfordeling',
                    fylkeId: this.selectedFylke,
                    season: year,
                    unike: true
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                var arr = {
                    fylke: this.selectedFylke,
                    year: year,
                    data: results.data
                }

                if(this.fylkeData[year] == undefined) {
                    this.fylkeData[year] = [];
                }

                this.fylkeData[year].push(arr);
            }

            this.fetchingStarted = false;
            this.dataFetched = true;


        },
        getYearsRange() : Array<string> {
            return ['10-11', '12-13', '14-15', '16-17', '18-19', '20+'];
        },
        getDataset() : any {

            var dataArr = [] as any;

            for(let kData in this.fylkeData) {
                for(let d of this.fylkeData[kData]) {
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

                        dataArr['' + year][idAlder] = dataArr['' + year][idAlder] ? dataArr['' + year][idAlder] + parseInt(alder.antall) : parseInt(alder.antall);;
                    }
                }
            }

            var retArr = [] as any;

            let colorId = 0;
            for(let key in dataArr) {
                let kData = dataArr[key];
                
                let opacityColor = 1;
                let color = getRandomColor(opacityColor, colorId);
                retArr.push(
                    {
                        label: key,
                        borderColor: color,
                        backgroundColor: color,
                        data: (<any>Object).values(kData),
                        fill: true,
                    }
                )
                colorId++;
            }

            return retArr;
            
        }
    }
}
</script>


<style scoped>

</style>


