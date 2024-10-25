<template>
    <div>
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <div class="as-display-flex as-margin-bottom-space-4">
                <div class="as-margin-auto as-margin-left-none">
                    <h4>
                        <template v-if="selectedYears.length > 1">
                            {{ selectedFylke.title }} kjønnsfordeling fra {{ selectedYears[0] }} til {{ selectedYears[selectedYears.length-1] }}
                        </template>
                        <template v-else>
                            {{ selectedFylke.title }} kjønnsfordeling for {{ selectedYears[0] }}
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
import LoadingComponent from '../Other/LoadingComponent.vue';
import { PermanentNotification } from 'ukm-components-vue3';
import MultiBarChart from '../charts/MultiBarChart.vue';


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
        Pie : Pie,
        MultiBarChart : MultiBarChart,
        LoadingComponent : LoadingComponent,
        PermanentNotification,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            fylkeData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            fylkeDataYear : {} as any,
            dataFetched: false,
            startYear: 0,
            endYear: 0,
            fetchingStarted: false,
            isBarChart: false,
        }
    },
    methods: {
        async init() {
            
            this.startYear = this.selectedYears[0];
            this.endYear = this.selectedYears[this.selectedYears.length-1];
            
            this.fetchingStarted = true;
            this.dataFetched = false;

            this.fylkeData = [];
            this.fylkeData['female'] = 0;
            this.fylkeData['male'] = 0;
            this.fylkeData['unknown'] = 0;

            this.fylkeDataYear = {} as any;

            for(let year of this.selectedYears) {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'fylke/kjonnsfordeling',
                    fylkeId: this.selectedFylke,
                    season: year,
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                this.fylkeData['female'] += results['female'] ? results['female'] : 0;
                this.fylkeData['male'] += results['male'] ? results['male'] : 0;
                this.fylkeData['unknown'] += results['unknown'] ? results['unknown'] : 0;

                this.fylkeDataYear[year] = {} as any;
                this.fylkeDataYear[year]['female'] = results['female'] ?? 0;
                this.fylkeDataYear[year]['male'] = results['male'] ?? 0;
                this.fylkeDataYear[year]['unknown'] = results['unknown'] ?? 0;
            }

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
                label: 'Dataset',
                data: [this.fylkeData['female'], this.fylkeData['male'], this.fylkeData['unknown']], // Data for pie slices
                backgroundColor: [
                    '#fbcce4',
                    '#afd7ff',
                    '#ededed'
                ]
            }];
            
        },
        getDatasetMultibar() : any {
            let retArr = new Array();

            let arrF = new Array();
            let arrM = new Array();
            let arrU = new Array();

            console.log(1921);
            for (let year of this.selectedYears) {
                let kjData = this.fylkeDataYear[year];
                
                console.log(1924);
                console.log(kjData);

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
    }
}
</script>


<style scoped>

</style>

