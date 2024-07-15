<template>
    <div>
        <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2"> 
            <div class="as-margin-bottom-space-2 as-display-flex">
                <h4 class="as-margin-auto as-margin-left-none">Velg Statistikk</h4>
            </div>
            <div class="as-margin-top-space-4">
                <v-autocomplete variant="outlined" label="Velg type" class="v-autocomplete-arr-sys" :items="['Arrangement', 'Kommune', 'Fylke']" v-model="selectedType"></v-autocomplete>
            </div>
            <div class="as-margin-top-space-1">
                <v-autocomplete variant="outlined" label="Velg type" class="v-autocomplete-arr-sys" :items="['Arrangement', 'Kommune', 'Fylke']" v-model="selectedItem"></v-autocomplete>
            </div>
        </div>

        <div class="as-card-1 as-padding-space-3 as-margin-top-space-7"> 
            <Arrangement />
        </div>
        
        
    </div>
</template>

<script lang="ts">
import Arrangement from './ArrSys/Arrangement.vue';


export default {
    props: {
        mobile: {
            type: Array,
            default: () => []
        },
        message: {
            type: String,
            default: ""
        }
    },
    components: {
        Arrangement : Arrangement
    },
    mounted() {
        console.log('mounted on ArrangorsystemStatistikk.vue');
        // this.showLoading();
    },
    data() {
        return {
            selectedType : '' as any,
            selectedItem : '' as any,
        }
    },
    methods: {

    }
}
</script>


<style scoped>

</style>


