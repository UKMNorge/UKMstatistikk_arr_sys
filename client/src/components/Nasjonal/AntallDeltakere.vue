<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
            <MultiBarChart ref="chart"
                :labels="getLabels()" 
                :dataset="getDataset()"
                :labelCallbackFunction="(tooltipItem) => `${tooltipItem.raw} deltaker${tooltipItem.raw > 1 ? 'e' : ''}`"
                :titleCallbackFunction="titleCallbackFunction"
                :stacked="true"
            />

            <div class="as-margin-top-space-4">
                <PermanentNotification :typeNotification="'primary'" tittel="Hvordan data genereres" :isHTML="true" description="
                <br>
                <p>
                    Statistikken viser antall deltakere i både fullførte og ufullførte innslag. 
                    <b>Et fullført innslag er et innslag der alle nødvendige data er sendt inn.</b>
                </p>
                <br>
                <p>
                    Husk at statistikken viser antall deltakere, ikke antall innslag. 
                    Siden et innslag kan ha flere deltakere, kan det være flere deltakere per fullført eller ufullført innslag. 
                    For eksempel vil et band med fire medlemmer telles som fire deltakere.
                </p>
                <br>
                <p>
                    OBS: Deltakere telles kun <b>1 gang i en sesong</b>. En deltaker kan potensielt være med i fullførte og ufullførte beregninger og i flere sesonger.
                </p>
                <br>
                <p>  
                    Påmeldinger før 2019 kan være noe unøyaktige, spesielt for ufullførte innslag. 
                    Dette skyldes manglende oversikt over kodene som ble brukt for å markere disse innslagene, noe som kan påvirke beregningene.
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
import MultiBarChart from '../charts/MultiBarChart.vue';
import LoadingComponent from '../Other/LoadingComponent.vue';
import { PermanentNotification } from 'ukm-components-vue3';
import { getRandomColor } from '../../utils/Colors';  

export default {
    props: {
        selectedYears: {
            type: Array as () => number[],
            required: true
        }
    },
    components: {
        MultiBarChart : MultiBarChart,
        LoadingComponent : LoadingComponent,
        PermanentNotification,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            antallDeltakereData: {} as any, //{year : number, antall : number}[]
            dataFetched: false,
            fetchingStarted: false,
        }
    },
    methods: {
        async init() {
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.antallDeltakereData = {};

            const requests = this.selectedYears.map(async (year) => {
                const data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'nasjonalt/antallDeltakere',
                    season: year,
                    unike: true
                };
                const results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
                this.antallDeltakereData[year] = [{ year, antall: results.antallDeltakere, antallUfullforte: results.antallDeltakereUfullforte }];
            });

            await Promise.all(requests);

            this.fetchingStarted = false;
            this.dataFetched = true;
        },
        getLabels() : any {
            let retArr = [];

            for(let year of this.getAllSelectedYears()) {
                retArr.push(year.toString());
            }
        
            return retArr;
        },
        getDataset() : any { 
            var retArr = [] as any;
            var singleRetArr = [] as any;
            
            var arrFullforte = [] as any;
            var arrUfullforte = [] as any;

            for(let year of this.selectedYears) {
                for(let data of this.antallDeltakereData[year]) {
                    // singleRetArr.push(data.antall);
                    arrFullforte.push(data.antall);
                    arrUfullforte.push(data.antallUfullforte);
                }
            }

            retArr.push({
                label: 'Fullforte',
                data: arrFullforte,
                backgroundColor: getRandomColor(1, 0),
            });

            retArr.push({
                label: 'Ufullførte',
                data: arrUfullforte,
                backgroundColor: '#bebebe',
            });

            return retArr;
        },
        getAllSelectedYears(): number[] {
            const minYear = Math.min(...this.selectedYears);
            const maxYear = Math.max(...this.selectedYears);
            return Array.from({ length: maxYear - minYear + 1 }, (_, i) => minYear + i);
        },
        titleCallbackFunction(tooltipItem : any) {
            return tooltipItem[0].dataset.label;
        }
    }
}
</script>


<style scoped>

</style>


