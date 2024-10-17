<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
          <LineChart ref="chart"
              :labels="selectedYears"
              :datasets="getDataset()"
          />
          </div>
          <div v-else-if="fetchingStarted">
              <LoadingComponent />
          </div>
    </div>
  </template>

<script lang="ts">
import LineChart from '../charts/LineChart.vue';
import KommuneObj from '../../objects/Kommune'; // Ensure Kommune is imported correctly
import LoadingComponent from '../Other/LoadingComponent.vue';
import Fylke from '../../objects/Fylke';
  
  
  export default {
    props: {
        selectedFylke: {
            type: Object as any,
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
      LineChart : LineChart,
      LoadingComponent : LoadingComponent,
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            fylkeData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
            dataFetched: false,
            colors : ['#FF6384', '#36A2EB', '#FFCE56'],
            fetchingStarted: false,
        }
    },
    methods: {
        async init() {
            // Empty old data
            this.fetchingStarted = true;
            this.dataFetched = false;
            this.fylkeData = [];
  
            for(let year of this.selectedYears) {
                var data = {
                    action: 'UKMstatistikk_ajax',
                    controller: 'fylke/antallDeltakere',
                    fylkeId: this.selectedFylke,
                    season: year,
                    unike: true
                };

                var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                var arr = {
                    fylkeNavn: results.fylkeNavn,
                    year: year,
                    antall: results.antall
                }

                if(this.fylkeData[year] == undefined) {
                    this.fylkeData[year] = [];
                }

                this.fylkeData[year].push(arr);
            }
  
            // Get gjennomsnitt deltakere i hele landet
            for(let year of this.selectedYears) {  
              let data = {
                  action: 'UKMstatistikk_ajax',
                  controller: 'fylke/gjennomsnittDeltakereIAlleFylker',
                  season: year,
              };
  
              var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
  
              let arr = {
                  fylkeNavn: 'Deltaker gjennomsnitt i alle fylker',
                  year: year,
                  antall: results.gjennomsnitt
              }
  
              this.fylkeData[year].push(arr);
            }
  
            this.fetchingStarted = false;
            this.dataFetched = true;
  
  
        },
        getDataset() : any {
            var fylkeArr = [] as any;

            for(let kData in this.fylkeData) {
                for(let d of this.fylkeData[kData]) {
                    console.log(d);
                    // Create array for each fylke if it doesn't exist
                    if(fylkeArr['id-' + d.fylkeNavn] == undefined) {
                        fylkeArr['id-' + d.fylkeNavn] = {fylkeNavn : d.fylkeNavn, data : []};
                    }
  
                    // Add data to fylke array
                    fylkeArr['id-' + d.fylkeNavn].data.push(d.antall);
                }
            }
          
            

            var retArr = [];
            for(let key in fylkeArr) {
                let kData = fylkeArr[key];
              
                let color = this.getRandomColor(1);
                retArr.push(
                    {
                    label: kData.fylkeNavn,
                    borderColor: color,
                    backgroundColor: color,
                    data: kData.data,
                    fill: true,
                    }
                )
            }
          
            
            return retArr;
          
        },
        getRandomColor(transparency = 1): string {
            // Random hue value between 0 and 360 (full spectrum of colors)
            const hue = Math.floor(Math.random() * 360);
            
            // Medium saturation for balanced colors (between 40% and 70%)
            const saturation = Math.floor(Math.random() * 10) + 40; // Range: [40, 70]
            
            // Medium lightness for slightly vibrant colors (between 50% and 70%)
            const lightness = Math.floor(Math.random() * 21) + 50; // Range: [50, 70]
  
            return `hsla(${hue}, ${saturation}%, ${lightness}%, ${transparency})`;
        }
    }
  }
  </script>
  
  
  <style scoped>
  
  </style>
  
  
  