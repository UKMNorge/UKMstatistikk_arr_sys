<template>
    <div>
        <div class="as-card-1 as-padding-space-3 as-margin-top-space-4" v-if="dataFetched == true && startYear == selectedYears[0] && endYear == selectedYears[selectedYears.length-1]">
            <div class="as-display-flex as-margin-bottom-space-4">
                <div class="as-margin-auto as-margin-left-none">
                    <h4>
                        <template v-if="selectedYears.length > 1">
                            {{ selectedKommune.title }} - kjønnsfordeling fra {{ selectedYears[0] }} til {{ selectedYears[selectedYears.length-1] }}
                        </template>
                        <template v-else>
                            {{ selectedKommune.title }} - kjønnsfordeling for {{ selectedYears[0] }}
                        </template>
                    </h4>
                </div>
                <div class="as-margin-auto as-margin-right-none">
                    <v-switch 
                        inset 
                        color="primary" 
                        v-model="isBarChart" 
                        label="Stolpediagram">
                    </v-switch>
                </div>
            </div>
            <Pie v-if="!isBarChart" ref="chart"
                :labels="getLabels()" 
                :dataset="getDataset()"
            />
            <MultiBarChart v-else ref="chart"
                :labels="getLabels()" 
                :dataset="getDatasetMultibar()"
                :titleCallbackFunction="(tooltipItems) => `${tooltipItems[0].dataset.label} ${tooltipItems[0].label}`"
                :stacked="true"
            />

            <div>
                <FlereKommunerMessage :alleKommuner="alleKommuner" :selectedKommuner="[selectedKommune]" />
            </div>

            <div class="as-margin-top-space-4">
                <PermanentNotification :typeNotification="'primary'" tittel="Hvordan vi genererer kjønnsdata" :isHTML="true" description="
                <br>
                <p>
                    UKMs påmeldingssystem <b>innhenter ikke informasjon om kjønn</b> fra deltakerne. Statistikkmodulen bruker et automatisert verktøy som estimerer kjønn basert på deltakernes fornavn. Dette er en teknisk løsning som gjør det mulig å generere nødvendig statistikk uten å be deltakerne oppgi kjønn, og uten at dette kobles til enkeltdeltakere noe sted. Kjønnsdata genereres altså kun for å gi anonymisert statistikk.
                </p>
                <br>
                <p>  
                    Enkelte fornavn kan være vanskelig for systemet å kjønnsbestemme. I disse tilfellene vil personen bli kategorisert som «ukjent» i statistikkene. Det er derfor viktig å merke seg at statistikken vil være noe unøyaktig.
                </p>
                "/>
            </div>
        </div>

        <div v-else-if="fetchingStarted">
            <LoadingComponent />
        </div>
    </div>
</template>

<script lang="ts">
import Pie from '../charts/Pie.vue';
import type Kommune from '../../objects/Kommune'; // Ensure Kommune is imported correctly
import type { PropType } from 'vue';  // Use type-only import for PropType
import { toRaw } from 'vue';  // Use type-only import for PropType
import LoadingComponent from '../Other/LoadingComponent.vue';
import MultiBarChart from '../charts/MultiBarChart.vue';
import { PermanentNotification } from 'ukm-components-vue3';
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
        Pie : Pie,
        MultiBarChart : MultiBarChart,
        LoadingComponent : LoadingComponent,
        PermanentNotification,
        FlereKommunerMessage : FlereKommunerMessage,

    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommuneData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            kommuneDataYear : {} as any,
            dataFetched: false,
            startYear: 0,
            endYear: 0,
            fetchingStarted: false,
            isBarChart: false,
            alleKommuner: {} as any,
        }
    },
    methods: {
        async init() {
            this.startYear = this.selectedYears[0];
            this.endYear = this.selectedYears[this.selectedYears.length-1];
            
            this.fetchingStarted = true;
            this.dataFetched = false;

            this.kommuneData = [];
            this.kommuneData['female'] = 0;
            this.kommuneData['male'] = 0;
            this.kommuneData['unknown'] = 0;

            this.kommuneDataYear = {} as any;

            const promises = this.selectedYears.map(async (year) => {
            var data = {
                action: 'UKMstatistikk_ajax',
                controller: 'kommune/kjonnsfordeling',
                kommuneId: this.selectedKommune.id,
                season: year,
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

            var resultsData = results.data;
            this.kommuneData['female'] += resultsData['female'] ? resultsData['female'] : 0;
            this.kommuneData['male'] += resultsData['male'] ? resultsData['male'] : 0;
            this.kommuneData['unknown'] += resultsData['unknown'] ? resultsData['unknown'] : 0;

            this.kommuneDataYear[year] = {} as any;
            this.kommuneDataYear[year]['female'] = resultsData['female'] ?? 0;
            this.kommuneDataYear[year]['male'] = resultsData['male'] ?? 0;
            this.kommuneDataYear[year]['unknown'] = resultsData['unknown'] ?? 0;
            });

            await Promise.all(promises);

            this.fetchingStarted = false;
            this.dataFetched = true;
        },
        getLabels() : Array<string> {
            if(!this.isBarChart) {
                return ['Jenter', 'Gutter', 'Ukjent'];
            }

            return this.getAllSelectedYears().map(year => year.toString()); 
        },
        getDataset() : any {
            return [{
                label: 'Kjønnsfordeling',
                data: [this.kommuneData['female'], this.kommuneData['male'], this.kommuneData['unknown']], // Data for pie slices
                backgroundColor: [
                    '#fbcce4',
                    '#afd7ff',
                    '#d5d5d5'
                ]
            }];
        },
        getDatasetMultibar() : any {
            let retArr = new Array();

            let arrF = new Array();
            let arrM = new Array();
            let arrU = new Array();

            let colorId = 0;
            for (let year of this.selectedYears) {
                let kjData = this.kommuneDataYear[year];
                
                let dataArr = new Array();
                let f = kjData['female'];
                let m = kjData['male'];
                let u = kjData['unknown'];

                arrF.push(f);
                arrM.push(m);
                arrU.push(u);
            }

            retArr.push({
                label: 'Jenter',
                data: arrF,
                backgroundColor: '#fbcce4',
            });

            retArr.push({
                label: 'Gutter',
                data: arrM,
                backgroundColor: '#afd7ff',
            });

            retArr.push({
                label: 'Ukjent',
                data: arrU,
                backgroundColor: '#d5d5d5',
            });

            console.log('retArr 1927');
            console.log(retArr);

            return retArr;
        },
        getAllSelectedYears(): number[] {
            var firstYear = this.selectedYears[0];
            var lastYear = this.selectedYears[this.selectedYears.length - 1];
            var years = [];
            for (let i = firstYear; i <= lastYear; i++) {
                years.push(i);
            }
            
            return years;
        },
    },
}
</script>


<style scoped>

</style>

