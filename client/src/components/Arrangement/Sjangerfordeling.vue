<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <div class="as-margin-bottom-space-2">
                <h4>{{ arrangementNavn }}</h4>
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
        arrangementNavn: {
            type: String,
            required: false
        },
        selectedArrangement: {
            type: Object as any,
            required: true
        },
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

            
            var data = {
                action: 'UKMstatistikk_ajax',
                controller: 'arrangement/sjangerfordeling',
                plId: this.selectedArrangement,
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            for (let sjanger in results) {
                if (!(sjanger in this.alleSjangere)) {
                    this.alleSjangere[sjanger] = '';
                }
            }

            var arr = {
                fylke: this.selectedArrangement,
                year: 'season',
                data: results
            };


            this.fylkeData.push(arr);
           

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

            for(let key in this.fylkeData[0].data) {
                let arrData = this.fylkeData[0].data[key];

                dataArr[key] = parseInt(arrData.antall);
            }

            var retArr = [] as any;
            let arrAll = [] as any;
            let colorId = 0;
            for(let key in dataArr) {
                arrAll.push(dataArr[key]);
            }
                
            let opacityColor = 1;
            let color = getRandomColor(opacityColor, colorId);
            retArr.push(
                {
                    label: this.arrangementNavn + ' - Sjangerfordeling',
                    borderColor: color,
                    backgroundColor: color,
                    data: arrAll,
                    fill: true,
                }
            );


            return retArr;

        }
    }
}
</script>


<style scoped>

</style>


