<template>
    <div>
      <div v-show="!loading" class="chart-container">
        <canvas :id="chartId"></canvas>
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
      },
      lineDataset: {
        type: Object as PropType<any>,
        required: false,
        default: false
      },
      labelCallbackFunction: {
        type: Function as PropType<(tooltipItem: any) => string>,
        required: false,
        default: (tooltipItem: any) => `${tooltipItem.raw}`
      },
      titleCallbackFunction: {
        type: Function as PropType<(tooltipItem: any) => string>,
        required: false,
        default: (tooltipItem: any) => `${tooltipItem[0].label}`
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
                labels: labels, // Labels for x-axis (e.g., age groups)
                datasets: this.lineDataset != false ? [...this.dataset, ...this.lineDataset] : this.dataset // Combine existing bar datasets with the new line dataset
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                      callbacks: {
                        label: this.labelCallbackFunction,
                        title: this.titleCallbackFunction
                      }
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
    max-width: 100%;
    height: auto;
    display: flex;
  }
  </style>
  