<template>
    <div>
        <h1>{{ dataFetched }}</h1>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true">
            <MultiBarChart ref="chart"
                :labels="getLabels()" 
                :dataset="getDataset()"
            />
        </div>
    </div>
</template>

<script lang="ts">
import MultiBarChart from '../charts/MultiBarChart.vue';
import type Kommune from '../../objects/Kommune'; // Ensure Kommune is imported correctly


export default {
    props: {
        selectedKommuner: {
            type: Array as () => Kommune[],
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
        MultiBarChart : MultiBarChart,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kommunerData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
            colors : ['#FF6384', '#36A2EB', '#FFCE56']
        }
    },
    methods: {
        async init() {
            // Empty old data
            this.dataFetched = false;
            this.kommunerData = [];

            for(let kommune of this.selectedKommuner) {
                for(let year of this.selectedYears) {
                    console.warn("AJAX...");
                    var data = {
                        action: 'UKMstatistikk_ajax',
                        controller: 'kommune/antallDeltakere',
                        kommuneId: kommune.id,
                        season: year,
                        unike: true
                    };

                    var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                    var arr = {
                        kommune: kommune,
                        year: year,
                        antall: results.antall
                    }

                    if(this.kommunerData[year] == undefined) {
                        this.kommunerData[year] = [];
                    }

                    this.kommunerData[year].push(arr);
                }
            }

            this.dataFetched = true;

        },
        getLabels() : any {
            let retArr = [];
            for(let kommune of this.selectedKommuner) {
                retArr.push(kommune.title);
            }

            return retArr;
        },
        getDataset() : any { 
            var retArr = [] as any;
            
            var count = 0;
            for(let year of this.selectedYears) {
                var dataKomm = [];
                for(let data of this.kommunerData[year]) {
                    dataKomm.push(data.antall);
                }

                retArr.push({
                    label: year.toString(),
                    data: dataKomm, 
                    backgroundColor: this.getRandomColor(),
                });
                count++;
            }

            return retArr;
        },
        getRandomColor(): string {
            // Random hue value between 0 and 360 (full spectrum of colors)
            const hue = Math.floor(Math.random() * 360);
            
            // Medium saturation for balanced colors (between 40% and 70%)
            const saturation = Math.floor(Math.random() * 10) + 40; // Range: [40, 70]
            
            // Medium lightness for slightly vibrant colors (between 50% and 70%)
            const lightness = Math.floor(Math.random() * 21) + 50; // Range: [50, 70]

            return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
        }
    }
}
</script>


<style scoped>

</style>


