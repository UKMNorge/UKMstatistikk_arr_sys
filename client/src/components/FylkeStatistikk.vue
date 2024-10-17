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
                    v-model="selectedFylke"
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
                <PermanentNotification :typeNotification="'info'" :tittel="dataTypeToggle == 0 ? 'Data fra kommuner i ditt fylke' : 'Data kun fra fylkesfestivaler'" :description="dataTypeToggle == 0 ? 'Viser statistikk fra data samlet fra alle kommuner i ditt fylket ekskludering fylkesfestivaler.' : 'Viser kun statistikk fra data relatert til fylkesfestivaler i ditt fylke, filtrert fra andre kommunale aktiviteter.'" />
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
                    :selectedFylkeId="selectedFylke"
                    :selectedYears="getAllSelectedYears()"
                ></AlleDeltakere>
            </div>

            <!-- Alle deltakere fylke -->
            <div v-show="selectedType == 'alleDeltakereFylke'">
                <AlleDeltakereFylke ref="alleDeltakerFylkeComponent"
                    :selectedFylkeId="selectedFylke"
                    :selectedYears="getAllSelectedYears()"
                ></AlleDeltakereFylke>
            </div>

            <!-- Deltakelse sammenligning -->
            <div v-show="selectedType == 'deltakelseSammenligning'">
                <DeltakelseSammenligning ref="deltakelseSammenligningComponent"
                    :selectedFylke="selectedFylke"
                    :selectedYears="getAllSelectedYears()"
                ></DeltakelseSammenligning>
            </div>

            <!-- Aldersfordeling -->
            <div v-show="selectedType == 'aldersfordeling'">
                <Alderfordeling ref="aldersfordelingComponent"
                    :selectedFylke="selectedFylke"
                    :selectedYears="getAllSelectedYears()"
                ></Alderfordeling>
            </div>
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
    },
    components : {
        PermanentNotification : PermanentNotification,
        AlleDeltakere : AlleDeltakere,
        AlleDeltakereFylke : AlleDeltakereFylke,
        DeltakelseSammenligning : DeltakelseSammenligning,
        Alderfordeling : Alderfordeling,
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
                    {title: 'Kjønnsfordeling', value: 'kjønnsfordeling'},
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
        generateRapport() {
            if(this.selectedType == 'alleDeltakere') {
                (<any>this.$refs).alleDeltakerComponent.init();
            } else if(this.selectedType == 'alleDeltakereFylke') {
                (<any>this.$refs).alleDeltakerFylkeComponent.init();
            } else if(this.selectedType == 'deltakelseSammenligning') {
                (<any>this.$refs).deltakelseSammenligningComponent.init();
            } else if(this.selectedType == 'aldersfordeling') {
                (<any>this.$refs).aldersfordelingComponent.init();
            }
        },
        isGeneratingPossible() {
            return this.selectedFylke != null && this.selectedType != null && this.selectedYears.length > 0 && this.dataTypeToggle != undefined;
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


