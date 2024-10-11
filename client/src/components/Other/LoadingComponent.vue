<template>
    <div class="main-loading-div as-margin-bottom-space-2">
        <div v-if="timeUnits % 2 === 0" class="chart-placholder doughnut phantom-loading">
            <div class="background-placeholder"></div>
        </div>

        <div v-else class="line-phantoms">
            <div class="chart-placholder lines as-border- phantom-loading">
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    export default {
        name: 'PhantomLoader',
        data() {
            return {
                intervalId: undefined as number | undefined,
                timeUnits: 0 as number
            }
        },
        mounted() {
            this.intervalId = setInterval(() => {
                if(Math.random() < 0.3) {
                    this.timeUnits++;
                }
            }, 2000);
        },
    }
</script>

<style scoped>
.main-loading-div {
    display: flex;
}
.line-phantoms {
    display: flex;
    margin: auto;
    width: 100% !important;
}

.chart-placholder {
    width: 200px;
    height: 200px;
    border: solid 23px #0000000a;
    border-radius: 50%;
    margin: auto;
}
.chart-placholder.lines {
    border-radius: 0;
    border: none;
    width: 100%;
    border-radius: var(--radius-normal);
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