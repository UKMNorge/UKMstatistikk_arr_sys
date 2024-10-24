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
            <br>
            <p>
                'Kommuner i hele landet' refererer til det nasjonale gjennomsnittet, som inkluderer data fra alle kommuner i Norge. Dette gjennomsnittet representerer antall deltakere fra kommuner som aktivt har bidratt med data i den spesifikke tidsperioden.
            </p>
            <br>
            <p>
                Vær oppmerksom på at ikke alle kommuner er inkludert i det nasjonale gjennomsnittet. Enkelte kommuner kan mangle deltakere i noen sesonger, og derfor er de utelatt fra beregningen. Dette gjøres for å sikre at statistikken gir en mest mulig nøyaktig representasjon av kommuner med aktiv deltakelse.
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
import { PermanentNotification } from 'ukm-components-vue3';
import KommuneObj from '../../objects/Kommune'; // Ensure Kommune is imported correctly
import type Kommune from '../../objects/Kommune'; // Ensure Kommune is imported correctly
import LoadingComponent from '../Other/LoadingComponent.vue';
import { getRandomColor } from '../../utils/Colors';


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
            var landKommune = new KommuneObj(0, 'Kommuner i hele landet');

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
          let colorId = 0;
          for(let key in kommunerArr) {
            let kData = kommunerArr[key];
            
            let opacityColor = kData.kommune.id == 0 ? 1 : .4;
            let color = getRandomColor(opacityColor, colorId);
            retArr.push(
              {
                label: kData.kommune.title,
                borderColor: color,
                backgroundColor: color,
                data: kData.data,
                fill: true,
              }
            );
            colorId++;
          }
        
          
          return retArr;
        
      }
  }
}
</script>


<style scoped>

</style>


