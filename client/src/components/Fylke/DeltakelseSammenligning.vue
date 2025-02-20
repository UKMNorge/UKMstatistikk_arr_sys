<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
          <LineChart ref="chart"
              :labels="selectedYears"
              :datasets="getDataset()"
          />
            <div class="as-margin-top-space-4">
                <PermanentNotification :typeNotification="'primary'" :isHTML="true" tittel="Info om statistikken" description="
                <p>
                    'Gjennomsnitt i alle fylker' refererer til det nasjonale gjennomsnittet, som inkluderer data fra alle fylker i Norge.
                </p>"
                />
            </div>
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
import { getRandomColor } from '../../utils/Colors';
import { PermanentNotification } from 'ukm-components-vue3';
  
  
  export default {
    props: {
        selectedFylker: {
            type: Array as () => number[],
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
      PermanentNotification,
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
            
            // Create an array to hold all promises
            let promises = [];

            // Get gjennomsnitt deltakere i hele landet
            for(let year of this.selectedYears) {  
            let data = {
                action: 'UKMstatistikk_ajax',
                controller: 'land/gjennomsnittDeltakereIAlleFylker',
                season: year,
            };

            if(this.fylkeData[year] == undefined) {
                this.fylkeData[year] = [];
            }

            promises.push(
                this.spaInteraction.runAjaxCall('/', 'POST', data).then((results : any) => {
                let arr = {
                    fylkeNavn: 'Gjennomsnitt i alle fylker',
                    year: year,
                    antall: results.gjennomsnitt
                }
                this.fylkeData[year].push(arr);
                })
            );
            }

            for(let year of this.selectedYears) {
            for(let fylke of this.selectedFylker) {
                var data = {
                action: 'UKMstatistikk_ajax',
                controller: 'fylke/antallDeltakere',
                fylkeId: fylke,
                season: year,
                unike: true
                };

                promises.push(
                this.spaInteraction.runAjaxCall('/', 'POST', data).then((results : any) => {
                    var arr = {
                        fylkeNavn: results.fylkeNavn,
                        year: year,
                        antall: results.antall
                    }

                    if(this.fylkeData[year] == undefined) {
                    this.fylkeData[year] = [];
                    }

                    this.fylkeData[year].push(arr);
                })
                );
            }
            }

            // Wait for all promises to resolve
            await Promise.all(promises);

            this.fetchingStarted = false;
            this.dataFetched = true;
        },
        getDataset() : any {
            var fylkeArr = [] as any;

            for(let kData in this.fylkeData) {
                for(let d of this.fylkeData[kData]) {
                    // Create array for each fylke if it doesn't exist
                    if(fylkeArr['id-' + d.fylkeNavn] == undefined) {
                        fylkeArr['id-' + d.fylkeNavn] = {fylkeNavn : d.fylkeNavn, data : []};
                    }
  
                    // Add data to fylke array
                    fylkeArr['id-' + d.fylkeNavn].data.push(d.antall);
                }
            }
          
            

            var retArr = [];
            let colorId = 0;
            for(let key in fylkeArr) {
                let kData = fylkeArr[key];
              
                let color = getRandomColor(1, colorId);
                retArr.push(
                    {
                    label: kData.fylkeNavn,
                    borderColor: color,
                    backgroundColor: color,
                    data: kData.data,
                    fill: true,
                    }
                )
                
                colorId++;
            }
          
            
            return retArr;
          
        }
    }
  }
  </script>
  
  
  <style scoped>
  
  </style>
  
  
  