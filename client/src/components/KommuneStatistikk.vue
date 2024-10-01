<template>
    <div>
        <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2"> 
            <div class="as-margin-bottom-space-2 as-display-flex">
                <h4 class="as-margin-auto as-margin-left-none">Velg kommuner og type</h4>
            </div>
            <div class="as-margin-top-space-4">
                <v-combobox
                  variant="outlined"
                  class="v-autocomplete-arr-sys"
                  clearable
                  chips
                  multiple
                  label="Valgte kommuner"
                  :items="availableKommuner"
                  v-model="selectedKommuner"
                />
            </div>
            <div class="as-margin-top-space-1">
                <v-autocomplete 
                    variant="outlined" 
                    label="Velg type" 
                    class="v-autocomplete-arr-sys" 
                    :items="availableTypes" 
                    v-model="selectedType">
                </v-autocomplete>
            </div>

            <!-- Årstall -->
            <div class="as-margin-top-space-1">
                <div class="as-padding-top-space-2 as-padding-bottom-space-4">
                    <h5>Velg tidsperioden</h5> 
                </div>
                <v-range-slider
                    v-model="selectedYears"
                    :items="availableYears" 
                    :min="2009"
                    :max="2024"
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

        <div class="as-card-1 as-padding-space-3 as-margin-top-space-7"> 
            <Arrangement />
        </div>
        
        
    </div>
</template>

<script lang="ts">
import Arrangement from './ArrSys/Arrangement.vue';
import Kommune from '../objects/Kommune';

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
    components: {
        Arrangement : Arrangement
    },
    mounted() {
        console.log('mounted on ArrangorsystemStatistikk.vue');
        this.fetchAvailableKommuner();
    },
    data() {
        return {
            selectedType: '' as any,
            selectedKommuner: [] as Kommune[],  // Hold the ids of selected municipalities
            availableTypes: ['Antall deltakere', 'Aldersfordeling', 'Kjønnsfordeling', 'Sjangerfordeling'],
            availableKommuner: [] as Kommune[],  // Corrected typo here
            availableYears: [] as number[],
            selectedYears: [] as number[]
        };
    },
    methods: {
        fetchAvailableYears() {
            // From 2009 to current year
            const currentYear = new Date().getFullYear();
            for (let i = 2009; i <= currentYear; i++) {
                this.availableYears.push(i);
            }
        },
        fetchAvailableKommuner() {
            var kommuner = [];
            for (let i = 0; i < 10; i++) {
                kommuner.push(new Kommune(i, 'Kommune ' + i));
            }
            if(kommuner.length > 0) {
                this.availableKommuner = kommuner;  // Corrected typo here
            }
        },
        generateRapport() {
            console.log('Selected Kommuner IDs:', this.selectedKommuner);
            // Map the selected ids to their corresponding names
            const selectedNames = this.selectedKommuner.map(id =>
                this.availableKommuner.find(kommune => kommune.id === id)?.navn
            );
            console.log('Selected Kommuner Names:', selectedNames);
        },
        isGeneratingPossible(): boolean {
            return this.selectedType !== '' && this.selectedKommuner.length > 0 && this.selectedYears.length > 0;
        }
    }
}
</script>

<style scoped>

</style>
