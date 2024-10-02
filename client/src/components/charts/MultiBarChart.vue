<template>
    <div>
      <div v-show="!loading" class="chart-container">
        <canvas :id="chartId"></canvas>
      </div>
      <!-- Placeholder for loading -->
      <div v-show="loading" class="loading-placeholder">
        Loading...
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent } from 'vue';
  import type { PropType } from 'vue';  // Use type-only import for PropType
  import { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';
  import { v4 as uuidv4 } from 'uuid';
  
  // Register the necessary components from chart.js
  Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);
  
  export default defineComponent({
    props: {
      barColors: {
        type: Array as PropType<string[]>,
        required: false,
        default: () => ['#FF6384', '#36A2EB', '#FFCE56'] // Default colors for each dataset (year)
      },
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
        if (this.chart) {
          this.chart.destroy(); // Destroy any existing chart before creating a new one
        }
  
        const ctx = (document.getElementById(this.chartId) as HTMLCanvasElement).getContext('2d');
        const labels = this.labels;
        const barColors = this.barColors;
        const dataset = this.dataset;
  
        this.chart = new Chart(ctx!, {
          type: 'bar',
          data: {
            labels: labels, // Labels for x-axis (e.g., Bergen, Melhus, etc.)
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
            },
            scales: {
              x: {
                beginAtZero: true
              },
              y: {
                beginAtZero: true
              }
            }
          }
        });
      }
    },
    beforeUnmount() {
      // Destroy the chart instance before the component is destroyed
      if (this.chart) {
        this.chart.destroy();
      }
    }
  });
  </script>
  
  <style scoped>
  .chart-container {
    position: relative;
    width: 600px;
    height: 400px;
  }
  
  .loading-placeholder {
    text-align: center;
    font-size: 1.5rem;
    color: #999;
  }
  </style>
  