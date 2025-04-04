<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <div class="as-margin-bottom-space-2">
                <h4>{{ fylkeNavn }}</h4>
            </div>
            <MultiBarChart ref="chart"
                :labels="getLabels()" 
                :dataset="getDataset()"
                :labelCallbackFunction="(tooltipItem) => `${tooltipItem.raw} innslag`"
            />

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
import MultiBarChart from '../charts/MultiBarChart.vue';
import type Fylke from '../../objects/Fylke'; 
import type { PropType } from 'vue';  // Use type-only import for PropType
import LoadingComponent from '../Other/LoadingComponent.vue';
import { getRandomColor } from '../../utils/Colors';
import { PermanentNotification } from 'ukm-components-vue3';


export default {
    props: {
        fylkeNavn: {
            type: String,
            required: false
        },
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
        PermanentNotification,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            fylkeData: {} as any,
            dataFetched: false,
            alleSjangere: {} as any,
            fetchingStarted: false,
        }
    },
    methods: {
        async init() {
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.fylkeData = [];
            this.alleSjangere = {};

            const promises = this.selectedYears.map(async (year: number) => {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'fylke/sjangerfordeling',
                    fylkeId: this.selectedFylke,
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
                    fylke: this.selectedFylke,
                    year: year,
                    data: results
                };

                if (this.fylkeData[year] == undefined) {
                    this.fylkeData[year] = [];
                }

                this.fylkeData[year].push(arr);
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

            console.log('retArr');
            console.log(retArr);

            return retArr;

        }
    }
}
</script>


<style scoped>

</style>


