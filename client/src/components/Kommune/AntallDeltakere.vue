<template>
    <div>
        <h1>{{ dataFetched }}</h1>
        <div v-if="dataFetched == true">
            <h1>INSIDE MultiBarChart YoYo!</h1>
            <MultiBarChart ref="chart"
                :labels="getLabels()" 
                :dataset="getDataset()"
                :barColors="[]" 
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

            // setTimeout(() => {
            //     this.dataFetched = true;
            // }, 10000);
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
                    backgroundColor: this.colors[count] ? this.colors[count] : '#FF6384',
                });
                count++;
            }

            return retArr;
        }
    }
}
</script>


<style scoped>

</style>


