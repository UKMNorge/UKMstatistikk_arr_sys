<template>
    <div>
        <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2"> 
            <div class="as-margin-bottom-space-2 as-display-flex">
                <h4 class="as-margin-auto as-margin-left-none">Velg område, arrangement og type</h4>
            </div>
            
            <div v-if="availableFylker.length > 0" class="as-margin-top-space-4 as-margin-bottom-space-1">
                <v-btn-toggle
                    v-model="selectOmradeType"
                    color="blue-accent-2"
                    mandatory>
                        <v-btn class="v-btn-as v-btn-grey as-margin-right-space-2" value="0">Kommuner</v-btn>
                        <v-btn class="v-btn-as v-btn-grey" value="1">Fylker</v-btn>
                </v-btn-toggle>
            </div>
            <div>
                <div v-if="selectOmradeType == 0" class="as-margin-top-space-3">
                    <v-autocomplete 
                        variant="outlined" 
                        label="Velg kommune"
                        class="v-autocomplete-arr-sys" 
                        :items="availableKommuner"
                        item-title="title"
                        item-value="id"
                        v-model="selectedKommune">
                    </v-autocomplete>
                </div>
    
                <div v-if="selectOmradeType == 1" class="as-margin-top-space-3">
                    <v-autocomplete 
                        variant="outlined" 
                        label="Velg fylke"
                        class="v-autocomplete-arr-sys" 
                        :items="availableFylker"
                        item-title="title"
                        item-value="id"
                        v-model="selectedFylke">
                    </v-autocomplete>
                </div>
            </div>

            <div class="as-margin-top-space-1">
                <v-autocomplete 
                    variant="outlined" 
                    label="Velg arrangement" 
                    class="v-autocomplete-arr-sys"
                    item-title="title"
                    item-value="id"
                    :items="availableArrangementer"
                    v-model="selectedArrangement">
                </v-autocomplete>
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

            <div class="as-margin-top-space-2">
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
                <template v-if="selectedArrangement != null">
                    <AntallDeltakere ref="antallDeltakerComponent"
                        :selectedArrangement="selectedArrangement"
                        :selectedYears="getAllSelectedYears()"
                    ></AntallDeltakere>
                </template>
            </div>

            <!-- Deltakelse Sammenligning -->
            <!-- <div v-show="selectedType == 'Deltakelse Sammenligning'">
                <DeltakelseSammenligning ref="deltakelseSammenligning"
                    :selectedKommuner="selectedKommuner"
                    :selectedYears="getAllSelectedYears()"
                ></DeltakelseSammenligning>
            </div> -->

            <!-- Aldersfordeling for hver kommuner -->
            <!-- <template v-for="kommune in selectedKommuner" v-bind:key="kommune.id"> -->
                <div v-show="selectedType == 'Aldersfordeling'">
                    <Alderfordeling 
                        :ref="'aldersfordelingArrangement'"
                        :selectedArrangement="selectedArrangement"
                    ></Alderfordeling>
                </div>
            <!-- </template> -->

            <!-- Aldersfordeling fra SSB -->
            <!-- <template v-for="kommune in selectedKommuner" v-bind:key="kommune.id">
                <div v-show="selectedType == 'Aldersfordeling fra SSB'">
                    <AldersfordelingSSB
                        :ref="'aldersfordeling-ssb-' + kommune.id"
                        :selectedKommune="kommune"
                        :selectedYears="getAllSelectedYears()"
                    ></AldersfordelingSSB>
                </div>
            </template> -->

            <!-- Deltakelse Sammenligning -->
            <!-- <div v-show="selectedType == 'Gjennomsnittsalder'">
                <Gjennomsnittsalder ref="gjennomsnittsalder"
                    :selectedKommuner="selectedKommuner"
                    :selectedYears="getAllSelectedYears()"
                ></Gjennomsnittsalder>
            </div> -->

            <!-- Kjønnsfordeling -->
            <!-- <template v-for="kommune in selectedKommuner" v-bind:key="kommune.id">
                <div v-show="selectedType == 'Kjønnsfordeling'">
                    <Kjonnsfordeling 
                        :ref="'kjonnsfordeling-' + kommune.id"
                        :selectedKommune="kommune"
                        :selectedYears="getAllSelectedYears()"
                    ></Kjonnsfordeling>
                </div>
            </template> -->

            <!-- Sjangerfordeling for hver kommuner -->
            <!-- <template v-for="kommune in selectedKommuner" v-bind:key="kommune.id">
                <div v-show="selectedType == 'Sjangerfordeling'">
                    <Sjangerfordeling 
                        :ref="'sjangerfordeling-' + kommune.id"
                        :selectedKommune="kommune"
                        :selectedYears="getAllSelectedYears()"
                    ></Sjangerfordeling>
                </div>
            </template> -->
        </div>

        <div v-if="countGenerating > 1" class="as-nop-impt as-margin-top-space-8">
            <Feedback class="as-padding-top-space-2 statistikk-feedback" />
        </div>

    </div>
</template>

<script lang="ts">
import Arrangement from './ArrSys/Arrangement.vue';
import Kommune from '../objects/Kommune';
import Fylke from '../objects/Fylke';
import AntallDeltakere from './Arrangement/AntallDeltakere.vue';
import Alderfordeling from './Arrangement/Alderfordeling.vue';
import DeltakelseSammenligning from './Kommune/DeltakelseSammenligning.vue';
import AldersfordelingSSB from './Kommune/AldersfordelingSSB.vue';
import Gjennomsnittsalder from './Kommune/Gjennomsnittsalder.vue';
import Kjonnsfordeling from './Kommune/Kjonnsfordeling.vue';
import Sjangerfordeling from './Kommune/Sjangerfordeling.vue';
import Feedback from './feedback/Feedback.vue';
import ArrangementObject from '../objects/Arrangement';

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
    watch: {
        selectedKommune(kommune : Kommune) {
            console.log("selectedKommuner changed:", kommune);
            this.fetchAvailableArrangementer(kommune, 'kommune');
        },
        selectedFylke(fylke : Fylke) {
            console.log("selectedKommuner changed:", fylke);
            this.fetchAvailableArrangementer(fylke, 'fylke');
        },
    },
    components: {
        Alderfordeling : Alderfordeling,
        Arrangement : Arrangement,
        AntallDeltakere : AntallDeltakere,
        DeltakelseSammenligning : DeltakelseSammenligning,
        AldersfordelingSSB : AldersfordelingSSB,
        Gjennomsnittsalder : Gjennomsnittsalder,
        Kjonnsfordeling : Kjonnsfordeling,
        Sjangerfordeling : Sjangerfordeling,
        Feedback : Feedback,
    },
    mounted() {
        this.fetchAvailableKommuner();

        // Set selected years
        let currentYear = new Date().getFullYear();
        this.selectedYears = [currentYear-2, currentYear];
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            selectedType: '' as any,
            selectedKommune: null as Kommune | null,  // Hold the ids of selected municipalities
            availableTypes: [
                'Antall deltakere', 
                // 'Deltakelse Sammenligning', 
                'Aldersfordeling',
                // 'Gjennomsnittsalder', 
                // 'Aldersfordeling fra SSB', 
                // 'Kjønnsfordeling', 
                // 'Sjangerfordeling',
            ],
            availableArrangementer : [] as ArrangementObject[],
            selectedArrangement: null as ArrangementObject | null,
            availableKommuner: [] as Kommune[],
            availableFylker: [] as Fylke[],
            availableYears: [] as number[],
            selectedYears: [] as number[],
            selectedFylke: null as Fylke | null,
            countGenerating: 0,
            selectOmradeType: 0,
        };
    },
    methods: {
        async fetchAvailableArrangementer(omradeId : any, omradeType : string) {
            this.availableArrangementer = [];
            this.selectedArrangement = null;

            // Fetch available arrangementer
            var data = {
                action: 'UKMstatistikk_ajax',
                controller: 'arrangement/availableArrangementer',
                omradeId: omradeId,
                omradeType: omradeType,
            };

            var arrangementer = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            for(let arrangement of arrangementer) {
                // Add if it deosn't already exist
                this.availableArrangementer.push(new ArrangementObject(arrangement.id, arrangement.navn + ' (' + arrangement.sesong + ')'));
            }
            console.log(this.availableArrangementer);
        },
        velgOmraade(omrade : any) {
            alert('a');
            console.log(omrade);
        },
        fetchAvailableYears() {
            // From 2009 to current year
            const currentYear = new Date().getFullYear() + 1;
            for (let i = 2009; i <= currentYear; i++) {
                this.availableYears.push(i);
            }
        },
        async fetchAvailableKommuner() {
            var kommuner = [];
            var fylker = [];

            for(let omradeItem of (<any>window).ukm_statistikk_klient.omrade) {
                if(omradeItem.type == 'kommune') {
                    kommuner.push(new Kommune(omradeItem.id, omradeItem.name));
                } else if(omradeItem.type == 'fylke') {
                    fylker.push(new Fylke(omradeItem.id, omradeItem.name));
                }
            }

            // Add available kommuner
            if(kommuner.length > 0) {
                this.availableKommuner = kommuner;  // Corrected typo here
            }

            // Get alle kommuner if brukeren er admin i fylke
            for(let fylke of fylker) {
                this.availableFylker.push(fylke);

                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'kommune/getAlleKommunerIFylke',
                    fylkeId: fylke.id,
                };

                var resultFylker = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                for(let kommune of resultFylker) {
                    // Add if it deosn't already exist
                    if(!this.availableKommuner.find(k => k.id == kommune.id)) {
                        this.availableKommuner.push(new Kommune(kommune.id, kommune.navn + ' (' + fylke.title + ')'));
                    }
                }
            }
        },
        generateRapport() {
            this.countGenerating++;

            if(this.selectedType == 'Antall deltakere') {
                (<any>this.$refs).antallDeltakerComponent.init();
            } else if(this.selectedType == 'Deltakelse Sammenligning') {
                (<any>this.$refs).deltakelseSammenligning.init();
            } else if(this.selectedType == 'Aldersfordeling') {
                // Loop through all selected kommuner and call init() on each
                (<any>this.$refs).aldersfordelingArrangement.init();
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
            }

            // Map the selected ids to their corresponding names
        },
        isGeneratingPossible(): boolean {
            return this.selectedArrangement != null && this.selectedType !== '' && this.selectedYears.length > 0;
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
