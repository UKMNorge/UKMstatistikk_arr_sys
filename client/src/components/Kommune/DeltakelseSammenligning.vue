<template>
  <div>
      <!-- Bare hvis data er fetched, kan chart opprettes -->
      <div v-if="dataFetched == true" class="as-card-1 as-padding-space-3 as-margin-top-space-4">
        <LineChart ref="chart"
            :labels="selectedYears"
            :datasets="getDataset()"
        />
        <div class="as-margin-top-space-4">
            <PermanentNotification :typeNotification="'info'" tittel="Info om statistikken" description="Vær oppmerksom på at ikke alle kommuner er inkludert i det nasjonale gjennomsnittet. Enkelte kommuner kan mangle deltakere i noen sesonger, og derfor er de utelatt fra beregningen. Dette gjøres for å sikre at statistikken gir en mest mulig nøyaktig representasjon av kommuner med aktiv deltakelse." />
        </div>
        </div>
        <div v-else-if="fetchingStarted">
            <LoadingComponent />
        </div>
  </div>
</template>

<script lang="ts">
import LineChart from '../charts/LineChart.vue';
import { PermanentNotification } from 'ukm-components-vue3';
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
    PermanentNotification : PermanentNotification,
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
          // Empty old data
          this.fetchingStarted = true;
          this.dataFetched = false;
          this.kommunerData = [];

          for(let kommune of this.selectedKommuner) {
              for(let year of this.selectedYears) {
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

          // Get gjennomsnitt deltakere i hele landet
          for(let year of this.selectedYears) {
            // Simulate statistikk for hele landet som kommune
            var landKommune = new KommuneObj(0, 'Hele landet');

            let data = {
                action: 'UKMstatistikk_ajax',
                controller: 'land/gjennomsnittDeltakereAlleKommuner',
                season: year,
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            let arr = {
                kommune: landKommune,
                year: year,
                antall: results.gjennomsnitt
            }

            this.kommunerData[year].push(arr);
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


