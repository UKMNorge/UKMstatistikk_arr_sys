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
    width: 100%;
    height: 400px;
    display: flex;
  }
  
  .loading-placeholder {
    text-align: center;
    font-size: 1.5rem;
    color: #999;
  }
  </style>
  