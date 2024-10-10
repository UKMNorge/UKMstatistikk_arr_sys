<template>
    <div>
        <div v-show="!loading" class="chart-container">
            <div class="chart-inner">
                <canvas :id="chartId"></canvas>
            </div>
        </div>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent } from 'vue';
  import type { PropType } from 'vue';  // Use type-only import for PropType
  import { Chart, PieController, ArcElement, Tooltip, Legend } from 'chart.js'; // Import PieController and ArcElement
  import { v4 as uuidv4 } from 'uuid';
  
  // Register the necessary components from chart.js
  Chart.register(PieController, ArcElement, Tooltip, Legend);
  
  export default defineComponent({
    props: {
      labels: {
        type: Array as PropType<string[]>, // Labels for the kommuner
        required: true
      },
      dataset: {
        type: Array as PropType<{ label: string; data: number[]; backgroundColor: string[] }[]>, // Dataset for the chart
        required: true
      },
      loading: {
        type: Boolean,
        required: false,
        default: false
      }
    },
    data() {
      return {
        chart: null as any | null,
        chartId: uuidv4() // Generate unique ID for the canvas
      };
    },
    mounted() {
      this.generateChart();
    },
    methods: {
      generateChart() {
        this.destroyChart();
  
        const ctx = (document.getElementById(this.chartId) as HTMLCanvasElement).getContext('2d');
        const labels = this.labels;
        const dataset = this.dataset;
  
        this.chart = new Chart(ctx!, {
          type: 'pie',
          data: {
            labels: labels, // Labels for pie slices
            datasets: dataset
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                display: true
              },
              tooltip: {
                enabled: true
              }
            }
          }
        });
      },
      destroyChart() {
        if (this.chart) {
          this.chart.destroy();
        }
      }
    },
    beforeUnmount() {
      // Destroy the chart instance before the component is destroyed
      this.destroyChart();
    }
  });
  </script>
  
<style scoped>
.chart-container {
    position: relative;
    max-width: 100%;
    max-height: 400px;
    height: auto;
    display: flex;
}
.chart-inner {
    margin: auto;
}
</style>
  