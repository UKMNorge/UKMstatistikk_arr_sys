<template>
    <div>
        <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2"> 
            <div class="as-margin-bottom-space-2 as-display-flex">
                <h4 class="as-margin-auto as-margin-left-none">Velg type</h4>
            </div>
            <div class="as-margin-top-space-4">
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

        <div>
            <!-- Antall deltakere -->
            <div v-show="selectedType == 'Antall deltakere'">
                <AntallDeltakere ref="antallDeltakerComponent"
                    :selectedYears="getAllSelectedYears()"
                ></AntallDeltakere>
            </div>

            <!-- Aldersfordeling -->
            <div v-show="selectedType == 'Aldersfordeling'">
                <Aldersfordeling ref="aldersfordelingComponent"
                    :selectedYears="getAllSelectedYears()"
                ></Aldersfordeling>
            </div>
        </div>

        <div v-if="countGenerating > 1" class="as-nop-impt as-margin-top-space-8">
            <Feedback class="as-padding-top-space-2 statistikk-feedback" />
        </div>

    </div>
</template>

<script lang="ts">
import AntallDeltakere from './Nasjonal/AntallDeltakere.vue';
import Aldersfordeling from './Nasjonal/Aldersfordeling.vue';
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
    components: {
        AntallDeltakere : AntallDeltakere,
        Aldersfordeling : Aldersfordeling,
        Feedback : Feedback,
    },
    mounted() {
        // Set selected years
        let currentYear = new Date().getFullYear();
        this.selectedYears = [currentYear-2, currentYear];
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            selectedType: '' as any,
            availableTypes: [
                'Antall deltakere',
                'Aldersfordeling',
            ],
            availableYears: [] as number[],
            selectedYears: [] as number[],
            countGenerating: 0,
        };
    },
    methods: {
        fetchAvailableYears() {
            // From 2009 to current year
            const currentYear = new Date().getFullYear() + 1;
            for (let i = 2009; i <= currentYear; i++) {
                this.availableYears.push(i);
            }
        },
        
        generateRapport() {
            this.countGenerating++;

            if(this.selectedType == 'Antall deltakere') {
                (<any>this.$refs).antallDeltakerComponent.init();
            } 
            else if(this.selectedType == 'Aldersfordeling') {
                (<any>this.$refs).aldersfordelingComponent.init();
            }
            // } else if(this.selectedType == 'Aldersfordeling fra SSB') {
            //     for(let kommune of this.selectedKommuner) {
            //         (<any>this.$refs)['aldersfordeling-ssb-' + kommune.id][0].init();
            //     }
            // } else if(this.selectedType == 'Gjennomsnittsalder') {
            //     (<any>this.$refs).gjennomsnittsalder.init();
            // } else if(this.selectedType == 'Kjønnsfordeling') {
            //     for(let kommune of this.selectedKommuner) {
            //         (<any>this.$refs)['kjonnsfordeling-' + kommune.id][0].init();
            //     }
            // } else if(this.selectedType == 'Sjangerfordeling') {
            //     for(let kommune of this.selectedKommuner) {
            //         (<any>this.$refs)['sjangerfordeling-' + kommune.id][0].init();
            //     }
            // }
        },
        isGeneratingPossible(): boolean {
            return this.selectedType !== '' && this.selectedYears.length > 0;
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
