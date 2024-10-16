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
                    v-model="selectedFylke">
                </v-autocomplete>
            </div>

            <div class="as-margin-top-space-1 as-display-flex">
                <div>
                    <v-btn-toggle
                        v-model="dataTypeToggle"
                        color="primary"
                        mandatory
                    >
                        <v-btn class="v-btn-as v-btn-grey as-margin-right-space-2" value="0">Data fra kommuner</v-btn>
                        <v-btn class="v-btn-as v-btn-grey" value="1">Data fra fylkesfestivaler</v-btn>
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
    </div>
</template>

<script lang="ts">
import Fylke from '../objects/Fylke';
import { PermanentNotification } from 'ukm-components-vue3';


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
    },
    components : {
        PermanentNotification : PermanentNotification
    },
    data() {
        return {
            availableFylker: [] as Fylke[],
            selectedFylke : null as Fylke | null,
            availableYears: [] as number[],
            selectedYears: [] as number[],
            availableTyper: [
                {title: 'Alderfordeling', value: 'aldersfordeling'},
                {title: 'Kjønnsfordeling', value: 'kjønnsfordeling'},
                {title: 'Sjangerfordeling', value: 'sjangerfordeling'},
                {title: 'Aldersfordeling SSB', value: 'aldersfordeling_ssb'}
            ],
            selectedType: null as {title: string, value: string} | null,
            dataTypeToggle: undefined,
            dataType : false,
        }
    },
    methods: {
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

        },
        isGeneratingPossible() {
            return this.selectedFylke != null && this.selectedType != null && this.selectedYears.length > 0;
        }
    }
}
</script>


<style scoped>

</style>


