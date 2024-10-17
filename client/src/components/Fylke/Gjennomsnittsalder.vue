<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
          <LineChart ref="chartGejnnomsnittsalder"
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
    import LoadingComponent from '../Other/LoadingComponent.vue';
  
  
    export default {
        props: {
            selectedFylke: {
                type: Array as any,
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
                this.fetchingStarted = true;
                this.dataFetched = false;
                this.fylkeData = [];
    
                for(let year of this.selectedYears) {
                    var data = {
                        action: 'UKMstatistikk_ajax',
                        controller: 'fylke/aldersfordeling',
                        fylkeId: this.selectedFylke,
                        season: year,
                        unike: true
                    };

                    var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                    let total = 0;
                    let antall = 0;
                    for(let res of results.data) {
                        let age = parseInt(res.age);
                        if(isNaN(age)) {
                            continue;
                        }
                        if(age < 10) {
                            age = 10;
                        } else if(age > 25) {
                            age = 25;
                        }
                        
                        total += age * parseInt(res.antall);
                        antall += parseInt(res.antall);
                    }

                    var arr = {
                        fylke: results.fylke,
                        fylkeId: this.selectedFylke,
                        year: year,
                        antall: Math.round(total / antall)
                    }

                    if(this.fylkeData[year] == undefined) {
                        this.fylkeData[year] = [];
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
                        // Create array for each kommune if it doesn't exist
                        if(fylkeArr['id-' + d.fylkeId] == undefined) {
                            fylkeArr['id-' + d.fylkeId] = {fylke : d.fylke, data : []};
                        }
    
                        // Add data to kommune array
                        fylkeArr['id-' + d.fylkeId].data.push(d.antall);
                    }
                }
            
    
                var retArr = [];
                for(let key in fylkeArr) {
                let kData = fylkeArr[key];
                
                let color = this.getRandomColor(1);
                retArr.push(
                    {
                    label: kData.fylke,
                    borderColor: color,
                    backgroundColor: color,
                    data: kData.data,
                    fill: true,
                    }
                )
                }
            
                
                console.log('retArr');
                console.log(retArr);
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
  
  
  