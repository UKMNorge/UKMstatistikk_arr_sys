<template>
    <div>
      <canvas ref="lineChart"></canvas>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent, ref, onMounted } from 'vue';
  import { Chart, LineController, LineElement, PointElement, LinearScale, Title, CategoryScale } from 'chart.js';
  import type { PropType } from 'vue';  // Use type-only import for PropType
  
  
  // Register the required Chart.js components
  Chart.register(LineController, LineElement, PointElement, LinearScale, Title, CategoryScale);
  
  export default defineComponent({
    name: 'LineChart',
    props: {
      labels: {
        type: Array as PropType<number[]>,
        required: true,
      },
      datasets: {
        type: Array as PropType<
          Array<{
            label: string;
            borderColor: string;
            backgroundColor: string;
            data: number[];
            fill: boolean;
          }>
        >,
        required: true,
      },
    },
    data() {
      return {
        chartInstance: null as any, // Store the chart instance
    };
    },
    mounted() {
        this.renderChart()
    },
    methods: {
      renderChart() {
        const labels = this.labels;

        if (this.$refs.lineChart) {
          this.chartInstance = new Chart(this.$refs.lineChart as HTMLCanvasElement, {
            type: 'line',
            data: {
                labels: this.labels,
                datasets: this.datasets.map(dataset => ({
                  ...dataset,
                  tension: 0.2, 
                })),
            },
            options: {
            responsive: true,
            scales: {
                x: {
                  beginAtZero: true,
                },
                y: {
                  beginAtZero: true,
                },
              },
            },
          });
        }
      },
      updateChart(newData: number[]) {
        if (this.chartInstance) {
          this.chartInstance.data.datasets[0].data = newData;
          this.chartInstance.update(); // Re-render the chart
        }
      },
      destroyChart() {
        if (this.chartInstance) {
          this.chartInstance.destroy(); // Clean up the chart instance
        }
      },
    },
  });
  </script>
  
  <style scoped>
  canvas {
    max-width: 100%;
    height: auto;
  }
  </style>
  