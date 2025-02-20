<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <div class="as-margin-bottom-space-2">
                <h4>{{ selectedKommune.title }}</h4>
            </div>
            <MultiBarChart ref="chart"
                :labels="getLabels()" 
                :dataset="getDataset()"
            />

            <div class="as-margin-top-space-4">
                <PermanentNotification :typeNotification="'primary'" :isHTML="true" tittel="Info om statistikken" description="
                    <p>
                        Sjangerfordelingen viser hvordan ulike sjangre er representert i innslagene, ikke blant deltakerne.
                    </p>"
                />
            </div>

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
        MultiBarChart : MultiBarChart,
        LoadingComponent : LoadingComponent,
        FlereKommunerMessage : FlereKommunerMessage,
        PermanentNotification,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommunerData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
            alleSjangere: [] as any,
            fetchingStarted: false,
            alleKommuner: {} as any,
        }
    },
    methods: {
        async init() {
            this.fetchingStarted = true;
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
                
                if (!this.alleKommuner[this.selectedKommune.id]) {
                    this.alleKommuner[this.selectedKommune.id] = {};
                }
                if (!this.alleKommuner[this.selectedKommune.id][year]) {
                    this.alleKommuner[this.selectedKommune.id][year] = {};
                }
                for(let oldKomKey in results.kommuner) {
                    this.alleKommuner[this.selectedKommune.id][year][results.kommuner[oldKomKey]] = results.kommuner[oldKomKey];
                }

                for(let sjanger in results.data) {
                    if(this.alleSjangere.indexOf(sjanger) == -1) {
                        this.alleSjangere[sjanger] = '';
                    }
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


