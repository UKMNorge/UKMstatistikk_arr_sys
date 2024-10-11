<template>
    <div>
        <!-- Bare hvis data er fetched, kan chart opprettes -->
        <div v-if="dataFetched == true">
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
    import KommuneObj from '../../objects/Kommune'; // Ensure Kommune is imported correctly
    import type Kommune from '../../objects/Kommune'; // Ensure Kommune is imported correctly
    import LoadingComponent from '../Other/LoadingComponent.vue';
  
  
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
            LineChart : LineChart,
            LoadingComponent : LoadingComponent,
        },
        data() {
            return {
                spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
                kommunerData: {} as any, //{kommune : Kommune, year : number, antall : number}[]
                dataFetched: false,
                colors : ['#FF6384', '#36A2EB', '#FFCE56'],
                fetchingStarted: false,
            }
        },
        methods: {
            async init() {
                this.fetchingStarted = true;
                this.dataFetched = false;
                this.kommunerData = [];
    
                for(let kommune of this.selectedKommuner) {
                    for(let year of this.selectedYears) {
                        var data = {
                            action: 'UKMstatistikk_ajax',
                            controller: 'kommune/aldersfordeling',
                            kommuneId: kommune.id,
                            season: year,
                            unike: true
                        };
    
                        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                        let total = 0;
                        let antall = 0;
                        for(let res of results) {
                            let age = parseInt(res.age);
                            if(age < 10) {
                                age = 10;
                            } else if(age > 25) {
                                age = 25;
                            }

                            total += age * parseInt(res.antall);
                            antall += parseInt(res.antall);
                        }

                        var arr = {
                            kommune: kommune,
                            year: year,
                            antall: Math.round(total / antall)
                        }
    
                        if(this.kommunerData[year] == undefined) {
                            this.kommunerData[year] = [];
                        }
    
                        this.kommunerData[year].push(arr);
                    }
                }

                this.fetchingStarted = false;
                this.dataFetched = true;
            },
            getDataset() : any {
                var kommunerArr = [] as any;
    
                for(let kData in this.kommunerData) {
                    for(let d of this.kommunerData[kData]) {
                        // Create array for each kommune if it doesn't exist
                        if(kommunerArr['id-' + d.kommune.id] == undefined) {
                            kommunerArr['id-' + d.kommune.id] = {kommune : d.kommune, data : []};
                        }
    
                        // Add data to kommune array
                        kommunerArr['id-' + d.kommune.id].data.push(d.antall);
                    }
                }
            
    
                var retArr = [];
                for(let key in kommunerArr) {
                let kData = kommunerArr[key];
                
                let opacityColor = kData.kommune.id == 0 ? 1 : .4;
                let color = this.getRandomColor(opacityColor);
                retArr.push(
                    {
                    label: kData.kommune.title,
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
  
  
  