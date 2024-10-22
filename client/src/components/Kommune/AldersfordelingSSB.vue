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
                :labelCallbackFunction="(tooltipItem) => `${tooltipItem.raw}%`"
            />
            <div class="as-margin-top-space-4">
                <PermanentNotification :typeNotification="'primary'" tittel="Info om statistikken" :isHTML="true" description="
                <br>
                <p>
                    <strong>Denne statistikken viser en sammenligning mellom aldersfordelingen i kommunen basert på SSB-data og prosentandelen av deltakere i arrangørsystemet for samme aldersgrupper. Diagrammet dekker ungdommer i alderen 10 til 21 år, fordelt på følgende aldersgrupper: 10-11, 12-13, 14-15, 16-17, 18-19, og 20-21.</strong>
                </p>
                <br>
                <p>
                    - Stolpene representerer prosentandelen av befolkningen i kommunen som deltar i arrangørsystemet innenfor hver aldersgruppe. Hvis en stolpe for aldersgruppen 18-19 år viser 20 %, betyr det at 20 % av befolkningen i denne aldersgruppen deltar i arrangementene i en sesong.
                </p>
                <br>
                <p>
                    Ved å sammenligne disse prosentandelene kan du få innsikt i hvilke aldersgrupper som er mest og minst representert i arrangementene i forhold til befolkningsandelen i hver gruppe. Dette kan hjelpe med å identifisere hvilke grupper som er mer engasjert, og hvilke som kan engasjeres mer.
                </p>
                " />

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
import { PermanentNotification } from 'ukm-components-vue3';
  
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
        LoadingComponent,
        PermanentNotification,
        MultiBarChart : MultiBarChart,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommunerData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
            colors : ['#FF6384', '#36A2EB', '#FFCE56'],
            fetchingStarted: false,
            arrSysAldersfordeling : [] as any,
        }
    },
    methods: {
        async init() {
            // Empty old data
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.kommunerData = [];

            for(let year of this.selectedYears) {
                var dataSSB = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'kommune/aldersfordelingSSB',
                    kommuneId: this.selectedKommune.id,
                    year: year,
                };
                var results = await this.spaInteraction.runAjaxCall('/', 'POST', dataSSB);

                var arr = {
                    kommune: this.selectedKommune,
                    year: year,
                    data: results
                }

                if(this.kommunerData[year] == undefined) {
                    this.kommunerData[year] = [];
                }

                this.kommunerData[year].push(arr);

                var dataAldersfordeling = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'kommune/aldersfordeling',
                    kommuneId: this.selectedKommune.id,
                    season: year,
                    unike: true
                };

                var resultsAldersfordeling = await this.spaInteraction.runAjaxCall('/', 'POST', dataAldersfordeling);

                this.arrSysAldersfordeling[year] = resultsAldersfordeling;
            }

            this.fetchingStarted = false;
            this.dataFetched = true;

        },
        getYearsRange() : Array<string> {
            return ['10-11', '12-13', '14-15', '16-17', '18-19', '20-21'];
        },
        getArrSysAldersfordeling() : any {
            let retArr: { [key: string]: any } = {};
            let totalPopulationYear = {} as any;

            for(let year of this.selectedYears) {
                retArr[year.toString()] = [];
                retArr[year.toString()]['10-11'] = 0;
                retArr[year.toString()]['12-13'] = 0;
                retArr[year.toString()]['14-15'] = 0;
                retArr[year.toString()]['16-17'] = 0;
                retArr[year.toString()]['18-19'] = 0;
                retArr[year.toString()]['20-21'] = 0;

                for(let aldersFordel of this.arrSysAldersfordeling[year]) {

                    if(aldersFordel.age < 10 || aldersFordel.age > 21) {
                        continue;
                    }

                    totalPopulationYear[year] = totalPopulationYear[year] ? totalPopulationYear[year] + parseInt(aldersFordel.antall) : parseInt(aldersFordel.antall);

                    let idAlder = aldersFordel.age % 2 !== 0 ? (parseInt(aldersFordel.age) - 1) + '-' + aldersFordel.age : aldersFordel.age + '-' + (parseInt(aldersFordel.age) + 1);

                    retArr[year.toString()][idAlder] = retArr[year.toString()][idAlder] ? retArr[year.toString()][idAlder] + parseInt(aldersFordel.antall) : parseInt(aldersFordel.antall);;
                }
            }
            
            return retArr;
        },
        getDataset() : any {
            let arrSysAldersfordeling = this.getArrSysAldersfordeling();

            var dataArr = {} as any;
            let totalPopulationYear = {} as any;
            let retArrObjs = [];
            
            for(let kData in this.kommunerData) {                
                for(let d of this.kommunerData[kData]) {
                    let year = d.year;

                    dataArr['' + year] = [];
                    dataArr['' + year]['10-11'] = 0;
                    dataArr['' + year]['12-13'] = 0;
                    dataArr['' + year]['14-15'] = 0;
                    dataArr['' + year]['16-17'] = 0;
                    dataArr['' + year]['18-19'] = 0;
                    dataArr['' + year]['20-21'] = 0;

                    totalPopulationYear['' + year] = 0;

                    for(let key in d.data.age_distribution) {
                        let alder = d.data.age_distribution[key];

                        totalPopulationYear['' + year] += parseInt(alder.antall);

                        var idAlder = '';

                        idAlder = alder.age % 2 !== 0 ? (parseInt(alder.age) - 1) + '-' + alder.age : alder.age + '-' + (parseInt(alder.age) + 1);

                        dataArr['' + year][idAlder] = dataArr['' + year][idAlder] ? dataArr['' + year][idAlder] + parseInt(alder.antall) : parseInt(alder.antall);;
                    }
                }
            }

            var retArr = [] as any;
            let colorId = 0;

            for(let year in dataArr) {
                let kData = dataArr[year];
                let kDataArrSys = arrSysAldersfordeling[year];

                for(let alderKey in arrSysAldersfordeling[year]) {
                    if(kData[alderKey] == 0) {
                        kData[alderKey] = 0;
                    } else {
                        kData[alderKey] = (kDataArrSys[alderKey] / (kData[alderKey]) * 100).toFixed(1);
                    }
                }
                
                let opacityColor = 1; //kData.kommune.id == 0 ? 1 : .4;
                let color = getRandomColor(opacityColor, colorId);
                
                retArr.push(
                    {
                        label: this.selectedKommune.title + year,
                        borderColor: color,
                        backgroundColor: color,
                        data: (<any>Object).values(kData),
                        fill: false,
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
  
  
  