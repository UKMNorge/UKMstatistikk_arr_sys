<template>
    <div>
       <h1>paa.sys</h1>

    </div>
</template>

<script lang="ts">


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
    mounted() {
        console.log('mounted on PaameldingsystemStatistikk.vue');
        // this.showLoading();
    },
    data() {
        return {
     
        }
    },
    methods: {

    }
}
</script>


<style scoped>

</style>


