<template>
    <div>
        <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2"> 
            <div class="as-margin-bottom-space-2 as-display-flex">
                <h4 class="as-margin-auto as-margin-left-none">Velg fylke og type</h4>
            </div>
            <div class="as-margin-top-space-4">
                <v-autocomplete 
                    variant="outlined" 
                    label="Velg fylke" 
                    class="v-autocomplete-arr-sys" 
                    :items="availableFylker"
                    clearable
                    chips
                    multiple
                    v-model="selectedFylker"
                    item-text="name" 
                    item-value="id">
                </v-autocomplete>
            </div>

            <div class="as-margin-top-space-1 as-display-flex">
                <div>
                    <v-btn-toggle
                        v-model="dataTypeToggle"
                        color="blue-accent-2"
                        mandatory
                    >
                        <v-btn @click="setAvailableTyper()" class="v-btn-as v-btn-grey as-margin-right-space-2" value="0">Data fra kommuner</v-btn>
                        <v-btn @click="setAvailableTyper()" class="v-btn-as v-btn-grey" value="1">Data fra fylkesfestivaler</v-btn>
                    </v-btn-toggle>
                </div>
                <div class="as-margin-auto as-margin-right-none">
                    <v-btn @click="dataType = !dataType" class="vuetify-icon-button as-margin-auto as-margin-right-none" density="compact" icon variant="tonal">
                        <v-icon>mdi-information-slab-symbol</v-icon>
                    </v-btn>
                </div>
            </div>
            <div v-if="dataType" class="as-margin-top-space-2">
                <PermanentNotification :typeNotification="'primary'" :tittel="dataTypeToggle == 0 ? 'Data fra kommuner i ditt fylke' : 'Data kun fra fylkesfestivaler'" :description="dataTypeToggle == 0 ? 'Viser statistikk fra data samlet fra alle kommuner i ditt fylket unntatt fylkesfestivaler.' : 'Viser kun statistikk fra data relatert til fylkesfestivaler i ditt fylke, filtrert fra andre kommunale aktiviteter.'" />
            </div>

            <div class="as-margin-top-space-4">
                <v-autocomplete 
                    variant="outlined" 
                    label="Velg type" 
                    class="v-autocomplete-arr-sys" 
                    :items="availableTyper" 
                    v-model="selectedType">
                </v-autocomplete>
            </div>

            <div class="as-margin-top-space-1">
                <div class="as-padding-top-space-2 as-padding-bottom-space-4">
                    <h5>Velg tidsperioden</h5> 
                </div>
                <v-range-slider
                    v-model="selectedYears"
                    :items="availableYears" 
                    :min="2009"
                    :max="new Date().getFullYear() + 1"
                    :step="1"
                    thumb-label="always"
                    class="align-center"
                >
                </v-range-slider>
            </div>

            <div>
                <v-btn
                    class="v-btn-as v-btn-success"
                    rounded="large"
                    size="large"
                    @click="generateRapport()"
                    :disabled="!isGeneratingPossible()"
                    variant="outlined">
                    Generer statistikk
                </v-btn>
            </div>

        </div>

        <!-- Statistikk -->
        <div>
            <!-- Alle deltakere -->
            <div v-show="selectedType == 'alleDeltakere'">
                <AlleDeltakere ref="alleDeltakerComponent"
                    :selectedFylker="selectedFylker"
                    :selectedYears="getAllSelectedYears()"
                ></AlleDeltakere>
            </div>

            <!-- Alle deltakere fylke -->
            <div v-show="selectedType == 'alleDeltakereFylke'">
                <AlleDeltakereFylke ref="alleDeltakerFylkeComponent"
                    :selectedFylkeId="selectedFylker[0]"
                    :selectedYears="getAllSelectedYears()"
                ></AlleDeltakereFylke>
            </div>

            <!-- Deltakelse sammenligning -->
            <div v-show="selectedType == 'deltakelseSammenligning'">
                <DeltakelseSammenligning ref="deltakelseSammenligningComponent"
                    :selectedFylker="selectedFylker"
                    :selectedYears="getAllSelectedYears()"
                ></DeltakelseSammenligning>
            </div>

            <!-- Aldersfordeling for hvert fylke -->
            <template v-for="fylkeId in selectedFylker" v-bind:key="fylkeId">
                <div v-show="selectedType == 'aldersfordeling'">
                    <Alderfordeling 
                        :ref="'aldersfordeling-' + fylkeId"
                        :fylkeNavn="_getFylkeById(fylkeId)"
                        :selectedFylke="fylkeId"
                        :selectedYears="getAllSelectedYears()"
                    ></Alderfordeling>
                </div>
            </template>

             <!-- Gjennomsnittsalder -->
             <div v-show="selectedType == 'gjennomsnittsalder'">
                <Gjennomsnittsalder ref="gjennomsnittsalderComponent"
                    :selectedFylker="selectedFylker"
                    :selectedYears="getAllSelectedYears()"
                ></Gjennomsnittsalder>
            </div>

            <!-- Kjønnsfordeling -->
            <template v-for="fylkeId in selectedFylker" v-bind:key="fylkeId">
                <div v-show="selectedType == 'kjonnsfordeling'">
                    <Kjonnsfordeling 
                        :ref="'kjonnsfordeling-' + fylkeId"
                        :fylkeNavn="_getFylkeById(fylkeId)"
                        :selectedFylke="fylkeId"
                        :selectedYears="getAllSelectedYears()"
                    ></Kjonnsfordeling>
                </div>
            </template>

            <!-- Sjangerfordeling for hvert fylker -->
            <template v-for="fylkeId in selectedFylker" v-bind:key="fylkeId">
                <div v-show="selectedType == 'sjangerfordeling'">
                    <Sjangerfordeling
                        :ref="'sjangerfordeling-' + fylkeId"
                        :selectedFylke="fylkeId"
                        :selectedYears="getAllSelectedYears()"
                    ></Sjangerfordeling>
                </div>
            </template>

        </div>

        <div v-if="countGenerating > 1" class="as-nop-impt as-margin-top-space-8">
            <Feedback class="as-padding-top-space-2 statistikk-feedback" />
        </div>

    </div>
</template>

<script lang="ts">
import Fylke from '../objects/Fylke';
import { PermanentNotification } from 'ukm-components-vue3';
import AlleDeltakere from './Fylke/AlleDeltakere.vue';
import AlleDeltakereFylke from './Fylke/AlleDeltakereFylke.vue';
import DeltakelseSammenligning from './Fylke/DeltakelseSammenligning.vue';
import Alderfordeling from './Fylke/Alderfordeling.vue';
import Gjennomsnittsalder from './Fylke/Gjennomsnittsalder.vue';
import Kjonnsfordeling from './Fylke/Kjonnsfordeling.vue';
import Sjangerfordeling from './Fylke/Sjangerfordeling.vue';
import Feedback from './feedback/Feedback.vue';

export default {
    props: {
        mobile: {
            type: Array,
            default: () => []
        },
        message: {
            type: String,
            default: ""
        }
    },
    mounted() {
        this.fetchFylker();
        this.setAvailableTyper();
        // Set selected years
        let currentYear = new Date().getFullYear();
        this.selectedYears = [currentYear-2, currentYear];
    },
    components : {
        PermanentNotification : PermanentNotification,
        AlleDeltakere : AlleDeltakere,
        AlleDeltakereFylke : AlleDeltakereFylke,
        DeltakelseSammenligning : DeltakelseSammenligning,
        Alderfordeling : Alderfordeling,
        Gjennomsnittsalder : Gjennomsnittsalder,
        Kjonnsfordeling : Kjonnsfordeling,
        Sjangerfordeling : Sjangerfordeling,
        Feedback : Feedback,
    },
    data() {
        return {
            availableFylker: [] as Fylke[],
            selectedFylke : null as Fylke | null,
            availableYears: [] as number[],
            selectedYears: [] as number[],
            availableTyper: [] as any,
            selectedType: null as any,
            dataTypeToggle: 0,
            dataType : false,
            countGenerating: 0,
            selectedFylker: [] as number[],
        }
    },
    methods: {
        setAvailableTyper() {
            this.selectedType = null;
            if(this.dataTypeToggle == 0) {
                this.availableTyper = [
                    {title: 'Alle deltakere', value: 'alleDeltakere'},
                    {title: 'Deltakelse Sammenligning', value: 'deltakelseSammenligning'},
                    {title: 'Aldersfordeling', value: 'aldersfordeling'},
                    {title: 'Gjennomsnittsalder', value: 'gjennomsnittsalder'},
                    {title: 'Kjønnsfordeling', value: 'kjonnsfordeling'},
                    {title: 'Sjangerfordeling', value: 'sjangerfordeling'},
                ];
            } else if(this.dataTypeToggle == 1) {
                this.availableTyper = [
                    {title: 'Alle deltakere', value: 'alleDeltakereFylke'},
                ];
            }
            else {
                this.availableTyper = [];
            }

            
        },
        fetchFylker() {
            let fylker = [] as Fylke[];
            for(let omradeItem of (<any>window).ukm_statistikk_klient.omrade) {
                if(omradeItem.type == 'fylke') {
                    fylker.push(new Fylke(omradeItem.id, omradeItem.name));
                }
            }
            this.availableFylker = fylker;
        },
        fetchAvailableYears() {
            // From 2009 to current year
            const currentYear = new Date().getFullYear() + 1;
            for (let i = 2009; i <= currentYear; i++) {
                // this.availableYears.push(i+4);
            }
        },
        _getFylkeById(fylkeId : number) : string {
            let omrader = (<any>window).ukm_statistikk_klient.omrade
            for(let omrade of omrader) {
                if(omrade.type == 'fylke' && omrade.id == fylkeId) {
                    return omrade.name;
                }
            }
            return 'Ukjent';
        },
        generateRapport() {
            this.countGenerating++;

            if(this.selectedType == 'alleDeltakere') {
                (<any>this.$refs).alleDeltakerComponent.init();
            } else if(this.selectedType == 'alleDeltakereFylke') {
                (<any>this.$refs).alleDeltakerFylkeComponent.init();
            } else if(this.selectedType == 'deltakelseSammenligning') {
                (<any>this.$refs).deltakelseSammenligningComponent.init();
            } else if(this.selectedType == 'aldersfordeling') {
                // Loop through all selected fylker and call init() on each
                for(let fylkeId of this.selectedFylker) {
                    console.log(this.$refs['aldersfordeling-' + fylkeId]);
                    (<any>this.$refs)['aldersfordeling-' + fylkeId][0].init();
                }
            } else if(this.selectedType == 'gjennomsnittsalder') {
                (<any>this.$refs).gjennomsnittsalderComponent.init();
            // } else if(this.selectedType == 'kjonnsfordeling') {
            //     (<any>this.$refs).kjonnsfordelingComponent.init();
            } else if(this.selectedType == 'kjonnsfordeling') {
                for(let fylkeId of this.selectedFylker) {
                    (<any>this.$refs)['kjonnsfordeling-' + fylkeId][0].init();
                }
            } else if(this.selectedType == 'sjangerfordeling') {
                for(let fylkeId of this.selectedFylker) {
                    (<any>this.$refs)['sjangerfordeling-' + fylkeId][0].init();
                }
            }
        },
        isGeneratingPossible() {
            return this.selectedFylker[0] != null && this.selectedType != null && this.selectedYears.length > 0 && this.dataTypeToggle != undefined;
        },
        getAllSelectedYears(): number[] {
            var firstYear = this.selectedYears[0];
            var lastYear = this.selectedYears[this.selectedYears.length - 1];
            var years = [];
            for (let i = firstYear; i <= lastYear; i++) {
                years.push(i);
            }

            return years;
        }
    }
}
</script>


<style scoped>

</style>


