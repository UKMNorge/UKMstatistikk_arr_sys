<template>
    <div class="box-statistikk">
        <div v-show="!loading" class="chart-div" :class="chartType">
            <canvas :id="chartId" class="box-statstikk-100" style="width: 200px; height: 200px"></canvas>
        </div>
        <!--- Placeholder loading -->
        <div v-show="loading">
            <div class="chart-placholder phantom-loading"><div class="background-placeholder"></div></div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import type { PropType } from 'vue';
import { Chart, DoughnutController, ArcElement, Tooltip, Legend } from 'chart.js';
import { v4 as uuidv4 } from 'uuid';

Chart.register(DoughnutController, ArcElement, Tooltip, Legend);

export default defineComponent({
    props: {
        barColors: {
            type: Array as PropType<string[]>,
            required: false
        },
        labels: {
            type: Array as PropType<string[]>,
            required: true
        },
        chartData: {
            type: Array as PropType<number[]>,
            required: false
        },
        loading: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            initialized: false,
            spaInteraction: (<any>window).spaInteraction, // Definert i main.ts
            deltaDates: [] as any[],
            chart: null as any,
            antall: 0,
            chartType: 'doughnut' as any,
            chartId: uuidv4(),
        };
    },
    mounted() {
        this.genererChart();
    },
    methods: {
        genererChart() {
            var data = this.chartData;
            const barColors = this.barColors;

            this.chart = new Chart(this.chartId, {
                type: this.chartType,
                data: {
                    labels: this.labels,
                    datasets: [{
                        label: 'hide',
                        backgroundColor: barColors,
                        data: data,
                        lineTension: 0.2,
                        pointRadius: 4,
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                },
            });
            console.log(this.chart);
        }
    }
});
</script>

<style scoped>
.chart-placholder {
    width: 200px;
    height: 200px;
    border: solid 23px #0000000a;
    border-radius: 50%;
    margin: auto;
    margin-top: 0;
    margin-left: 30px;
}
.chart-placholder .background-placeholder {
    background: #fff !important;
    height: 200px;
    width: 200px;
}
.box-statstikk-100 {
    width: 200px !important;
    height: 200px !important;
}
</style>
