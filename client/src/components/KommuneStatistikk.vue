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

        <div class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <!-- Antall deltakere -->
            <div v-show="selectedType == 'Antall deltakere'">
                <AntallDeltakere ref="antallDeltakerComponent"
                    :selectedKommuner="selectedKommuner"
                    :selectedYears="getAllSelectedYears()"
                ></AntallDeltakere>
            </div>

            <!-- Deltakelse Sammenligning -->
            <div v-show="selectedType == 'Deltakelse Sammenligning'">
                <DeltakelseSammenligning ref="deltakelseSammenligning"
                    :selectedKommuner="selectedKommuner"
                    :selectedYears="getAllSelectedYears()"
                ></DeltakelseSammenligning>
            </div>

            <!-- Aldersfordeling for hver kommuner -->
            <template v-for="kommune in selectedKommuner" v-bind:key="kommune.id">
                <div v-show="selectedType == 'Aldersfordeling'">
                    <Alderfordeling 
                        :ref="'aldersfordeling-' + kommune.id"
                        :selectedKommune="kommune"
                        :selectedYears="getAllSelectedYears()"
                    ></Alderfordeling>
                </div>
            </template>

            <!-- Aldersfordeling fra SSB -->
            <template v-for="kommune in selectedKommuner" v-bind:key="kommune.id">
                <div v-show="selectedType == 'Aldersfordeling fra SSB'">
                    <AldersfordelingSSB
                        :ref="'aldersfordeling-ssb-' + kommune.id"
                        :selectedKommune="kommune"
                        :selectedYears="getAllSelectedYears()"
                    ></AldersfordelingSSB>
                </div>
            </template>

            <!-- Deltakelse Sammenligning -->
            <div v-show="selectedType == 'Gjennomsnittsalder'">
                <Gjennomsnittsalder ref="gjennomsnittsalder"
                    :selectedKommuner="selectedKommuner"
                    :selectedYears="getAllSelectedYears()"
                ></Gjennomsnittsalder>
            </div>

            <!-- Kjønnsfordeling -->
            <template v-for="kommune in selectedKommuner" v-bind:key="kommune.id">
                <div v-show="selectedType == 'Kjønnsfordeling'">
                    <Kjonnsfordeling 
                        :ref="'kjonnsfordeling-' + kommune.id"
                        :selectedKommune="kommune"
                        :selectedYears="getAllSelectedYears()"
                    ></Kjonnsfordeling>
                </div>
            </template>
        </div>
        
    </div>
</template>

<script lang="ts">
import Arrangement from './ArrSys/Arrangement.vue';
import Kommune from '../objects/Kommune';
import AntallDeltakere from './Kommune/AntallDeltakere.vue';
import DeltakelseSammenligning from './Kommune/DeltakelseSammenligning.vue';
import Alderfordeling from './Kommune/Alderfordeling.vue';
import AldersfordelingSSB from './Kommune/AldersfordelingSSB.vue';
import Gjennomsnittsalder from './Kommune/Gjennomsnittsalder.vue';
import Kjonnsfordeling from './Kommune/Kjonnsfordeling.vue';

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
        Arrangement : Arrangement,
        AntallDeltakere : AntallDeltakere,
        DeltakelseSammenligning : DeltakelseSammenligning,
        Alderfordeling : Alderfordeling,
        AldersfordelingSSB : AldersfordelingSSB,
        Gjennomsnittsalder : Gjennomsnittsalder,
        Kjonnsfordeling : Kjonnsfordeling
    },
    mounted() {
        console.log('mounted on ArrangorsystemStatistikk.vue');
        this.fetchAvailableKommuner();
    },
    data() {
        return {
            selectedType: '' as any,
            selectedKommuner: [] as Kommune[],  // Hold the ids of selected municipalities
            availableTypes: [
                'Antall deltakere', 
                'Deltakelse Sammenligning', 
                'Aldersfordeling',
                'Gjennomsnittsalder', 
                'Aldersfordeling fra SSB', 
                'Kjønnsfordeling', 
                'Sjangerfordeling',
            ],
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
            for (let i = 0; i < 2; i++) {
                kommuner.push(new Kommune(i, 'Kommune ' + i));
            }
            if(kommuner.length > 0) {
                this.availableKommuner = kommuner;  // Corrected typo here
            }

            this.availableKommuner.push(new Kommune(5028, 'Melhus'));
            this.availableKommuner.push(new Kommune(5628, 'Deatnu - Tana'));
            
        },
        generateRapport() {
            if(this.selectedType == 'Antall deltakere') {
                (<any>this.$refs).antallDeltakerComponent.init();
            } else if(this.selectedType == 'Deltakelse Sammenligning') {
                (<any>this.$refs).deltakelseSammenligning.init();
            } else if(this.selectedType == 'Aldersfordeling') {
                // Loop through all selected kommuner and call init() on each
                for(let kommune of this.selectedKommuner) {
                    console.log(this.$refs['aldersfordeling-' + kommune.id]);
                    (<any>this.$refs)['aldersfordeling-' + kommune.id][0].init();
                }
            } else if(this.selectedType == 'Aldersfordeling fra SSB') {
                for(let kommune of this.selectedKommuner) {
                    (<any>this.$refs)['aldersfordeling-ssb-' + kommune.id][0].init();
                }
            } else if(this.selectedType == 'Gjennomsnittsalder') {
                (<any>this.$refs).gjennomsnittsalder.init();
            } else if(this.selectedType == 'Kjønnsfordeling') {
                for(let kommune of this.selectedKommuner) {
                    (<any>this.$refs)['kjonnsfordeling-' + kommune.id][0].init();
                }
            }

            console.log('Selected Kommuner IDs:', this.selectedKommuner);
            // Map the selected ids to their corresponding names
        },
        isGeneratingPossible(): boolean {
            return this.selectedType !== '' && this.selectedKommuner.length > 0 && this.selectedYears.length > 0;
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
