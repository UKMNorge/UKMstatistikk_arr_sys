<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <div class="as-display-flex as-margin-bottom-space-4">
                <div class="as-margin-auto as-margin-left-none">
                    <h4>Aldersfordeling nasjonalt {{ isProsentandel ? 'i prosent' : 'i antall' }}</h4>
                </div>
                <div class="as-margin-auto as-margin-right-none">
                    <v-switch 
                        inset 
                        color="primary" 
                        v-model="isProsentandel" 
                        label="Prosentandel">
                    </v-switch>
                </div>
            </div>

            <template v-if="isProsentandel">
                <MultiBarChart ref="chart"
                    :labels="getYearsRange()" 
                    :dataset="getDataset()"
                    :titleCallbackFunction="titleCallbackFunction"
                    :labelCallbackFunction="(tooltipItem) => `${tooltipItem.raw} %`"

                />
            </template>
            <template v-if="!isProsentandel">
                <MultiBarChart ref="chart"
                    :labels="getYearsRange()"
                    :dataset="getDataset()"
                    :titleCallbackFunction="titleCallbackFunction"
                    :labelCallbackFunction="(tooltipItem) => `${tooltipItem.raw} deltaker${tooltipItem.raw > 1 ? 'e' : ''}`"
                />
            </template>
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
    mounted() {

    },
    components: {
        MultiBarChart : MultiBarChart,
        LoadingComponent : LoadingComponent,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            aldersfordelingsData: {} as any,
            dataFetched: false,
            fetchingStarted: false,
            isProsentandel: false
        }
    },
    methods: {
        async init() {
            // Empty old data
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.aldersfordelingsData = {};

            const requests = this.selectedYears.map(async (year) => {
                const data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'nasjonalt/aldersfordeling',
                    season: year,
                    unike: true
                };

                const results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                return { year, data: results };
            });

            // Wait for all requests to complete
            const responses = await Promise.all(requests);

            // Store results
            responses.forEach(({ year, data }) => {
                if (!this.aldersfordelingsData[year]) {
                    this.aldersfordelingsData[year] = [];
                }
                this.aldersfordelingsData[year].push({ year, data });
            });

            this.fetchingStarted = false;
            this.dataFetched = true;
        },
        getYearsRange() : Array<string> {
            return ['< 10', '10-11', '12-13', '14-15', '16-17', '18-19', '20+'];
        },
        getTotalForYear(year : number) : number {
            let data = this.aldersfordelingsData[year][0].data;
            let total = 0;
            for(let age in data) {
                total += parseInt(data[age]);
            }
            return total;
        },
        getDataset() : any {
            var dataArr = {} as any;

            for(let year in this.aldersfordelingsData) {
                let data = this.aldersfordelingsData[year][0].data;

                dataArr['' + year] = [];
                dataArr['' + year]['< 10'] = 0;
                dataArr['' + year]['10-11'] = 0;
                dataArr['' + year]['12-13'] = 0;
                dataArr['' + year]['14-15'] = 0;
                dataArr['' + year]['16-17'] = 0;
                dataArr['' + year]['18-19'] = 0;
                dataArr['' + year]['20+'] = 0;

                let antallPersoner = this.getTotalForYear(parseInt(year));
                console.log(year + ' = ' +  antallPersoner);

                for(let age in data) {

                    let alder = parseInt(age);
                    var antallIAlder = this.isProsentandel ? data[alder]/antallPersoner * 100 : data[alder];

                    var idAlder = '';

                    if(alder < 10) {
                        idAlder = '< 10'
                    } else if(alder > 19) {
                        idAlder = '20+'
                    } else {
                        idAlder = alder % 2 !== 0 ? (alder - 1) + '-' + alder : alder + '-' + (alder + 1);
                    }

                    dataArr['' + year][idAlder] = dataArr['' + year][idAlder] ? dataArr['' + year][idAlder] + parseInt(antallIAlder) : parseInt(antallIAlder);
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

            console.log(retArr);
            return retArr;
            
        },
        titleCallbackFunction(tooltipItem : any) {
            return tooltipItem[0].dataset.label;
        }
    }
}
</script>


<style scoped>

</style>


