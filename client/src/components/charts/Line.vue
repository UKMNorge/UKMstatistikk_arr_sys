<template>
    <div class="chart-lines as-display-flex">
        <div class="half-size as-margin-right-space-2">
            <div v-for="line of lines" class="as-display-flex line-div as-margin-top-space-1 as-margin-bottom-space-1 as-margin-left-none" :style="{ width: line.value + '%' }">
                <v-tooltip>
                    <template v-slot:activator="{ props }">
                        <div v-bind="props" class="chart-line as-margin-auto" :style="{ backgroundColor: line.color }"></div>
                    </template>
                </v-tooltip>
            </div>
        </div>
        <div class="half-size as-display-flex">
            <p class="title as-margin-auto">{{ title }}</p>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import type { PropType } from 'vue';


export default defineComponent({
    props: {
        lines: {
            type: Array as PropType<{navn : String, value : Number, color : String}[]>,
            required: true
        },
        title: {
            type: String,
            required: true
        },
    },
    data() {
        return {
          
        };
    },
    mounted() {
        
    },
    methods: {
        
    }
});
</script>

<style scoped>
.chart-lines {
    position: relative;
    width: 100%;
}
.chart-line {
    width: 100%;
    height: 10px;
    opacity: .8; 
    border-radius: var(--radius-max);
}
.half-size {
    width: 50%;
}
.title {
    font-weight: 200;
    margin: auto !important;
    margin-left: 0;
}

</style>
