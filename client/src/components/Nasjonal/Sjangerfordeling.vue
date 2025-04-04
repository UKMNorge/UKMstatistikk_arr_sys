<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <div class="as-display-flex as-margin-bottom-space-4">
                <div class="as-margin-auto as-margin-left-none">
                    <h4>Sjangerfordeling nasjonalt {{ isProsentandel ? 'i prosent' : 'i antall innslag' }}</h4>
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
                    :labels="getLabels()" 
                    :dataset="getDataset()"
                    :labelCallbackFunction="(tooltipItem) => `${tooltipItem.raw} %`"
                    :titleCallbackFunction="titleCallbackFunction"
                />
            </template>

            <template v-if="!isProsentandel">
                <MultiBarChart ref="chart"
                    :labels="getLabels()" 
                    :dataset="getDataset()"
                    :labelCallbackFunction="(tooltipItem) => `${tooltipItem.raw} innslag`"
                    :titleCallbackFunction="titleCallbackFunction"
                />
            </template>

            <div class="as-margin-top-space-4">
                <PermanentNotification :typeNotification="'primary'" :isHTML="true" tittel="Info om statistikken" description="
                    <p>
                        Sjangerfordelingen viser hvordan ulike sjangre er representert i innslagene, ikke blant deltakerne.
                    </p>"
                />
            </div>
        </div>        
        <div v-else-if="fetchingStarted">
            <LoadingComponent />
        </div>
    </div>
</template>

<script lang="ts">
import MultiBarChart from '../charts/MultiBarChart.vue';;
import LoadingComponent from '../Other/LoadingComponent.vue';
import { getRandomColor } from '../../utils/Colors';
import { PermanentNotification } from 'ukm-components-vue3';


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
        PermanentNotification,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            sjangerData: {} as any,
            dataFetched: false,
            alleSjangere: {} as any,
            fetchingStarted: false,
            isProsentandel: false,
        }
    },
    methods: {
        async init() {
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.sjangerData = {};
            this.alleSjangere = {};

            const promises = this.selectedYears.map(async (year : number) => {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'nasjonalt/sjangerfordeling',
                    season: year,
                    unike: true
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
                
                for (let sjanger in results) {
                    if (!(sjanger in this.alleSjangere)) {
                        this.alleSjangere[sjanger] = '';
                    }
                }

                var arr = {
                    year: year,
                    data: results
                }

                if(this.sjangerData[year] == undefined) {
                    this.sjangerData[year] = [];
                }

                this.sjangerData[year].push(arr);
            });

            await Promise.all(promises);

            // Sort alleSjangere by keys
            this.alleSjangere = Object.keys(this.alleSjangere)
                .sort()  
                .reduce((sortedObj : any, key) => {
                    sortedObj[key] = this.alleSjangere[key];
                    return sortedObj;
            }, {});

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

            for(let kData in this.sjangerData) {
                let d = this.sjangerData[kData][0];
                let year = d.year;

                dataArr['' + year] = [];

                for(let sjanger in this.alleSjangere) {
                    dataArr['' + year][sjanger] = [];
                }

                if(this.isProsentandel) {
                    let total = 0;
                    for(let key in d.data) {
                        total += d.data[key];
                    }

                    for(let key in d.data) {
                        let value = d.data[key];
                        dataArr['' + year][key] = parseFloat(((value / total) * 100).toFixed(2));
                    }
                } else {

                    for(let key in d.data) {
                        let value = d.data[key];
                        dataArr['' + year][key] = parseInt(value);
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
                );
                colorId++;
            }

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


