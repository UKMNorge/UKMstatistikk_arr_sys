<template>
    <div>
        <div class="as-card-1 as-padding-space-3 as-margin-top-space-4" v-if="dataFetched == true && startYear == selectedYears[0] && endYear == selectedYears[selectedYears.length-1]">
            <div class="as-display-flex as-margin-bottom-space-4">
                <div class="as-margin-auto as-margin-left-none">
                    <h4>
                        <template v-if="selectedYears.length > 1">
                            {{ selectedKommune.title }} kjønnsfordeling fra {{ selectedYears[0] }} til {{ selectedYears[selectedYears.length-1] }}
                        </template>
                        <template v-else>
                            {{ selectedKommune.title }} kjønnsfordeling for {{ selectedYears[0] }}
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
                    I vårt påmeldingssystem <b>innhenter vi ikke informasjon om kjønn</b> direkte fra deltakerne. I stedet bruker vi et automatisert verktøy som estimerer kjønn basert på deltakerens fornavn. Dette er en teknisk løsning som lar oss gi deg nødvendig statistikk uten å be deltakerne spesifikt oppgi sitt kjønn.
                </p>
                <p>
                    For noen navn klarer ikke systemet å fastslå et kjønnsestimat. I disse tilfellene vil personen bli kategorisert som «Ukjent» i statistikkene. Dette betyr ikke at personen har valgt å ikke identifisere seg med et bestemt kjønn, men heller at verktøyet ikke har vært i stand til å generere et kjønn basert på navnet.
                </p>
                <br>
                <p>
                    Det er viktig å merke seg at statistikken noen ganger kan være unøyaktig, siden kjønnsbestemmelsen er basert på navn, noe som kan gi feil resultater for enkelte personer.
                </p>
                <br>
                <p>  
                    Vi i UKM Norge har full forståelse for at enkelte ikke ønsker å identifisere seg med et spesifikt kjønn. Selv om systemet vårt er avhengig av å generere kjønnsdata for statistiske formål, blir ingen personlig identifiserbar informasjon om kjønnsidentitet lagret. Kjønnsdata genereres kun for å gi anonymisert statistikk.
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

            for(let year of this.selectedYears) {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'kommune/kjonnsfordeling',
                    kommuneId: this.selectedKommune.id,
                    season: year,
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                this.kommuneData['female'] += results['female'] ? results['female'] : 0;
                this.kommuneData['male'] += results['male'] ? results['male'] : 0;
                this.kommuneData['unknown'] += results['unknown'] ? results['unknown'] : 0;

                this.kommuneDataYear[year] = {} as any;
                this.kommuneDataYear[year]['female'] = results['female'] ?? 0;
                this.kommuneDataYear[year]['male'] = results['male'] ?? 0;
                this.kommuneDataYear[year]['unknown'] = results['unknown'] ?? 0;
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

