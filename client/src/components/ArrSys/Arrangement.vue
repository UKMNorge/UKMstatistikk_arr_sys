<template>
    <div>
        <!-- Deltakere -->
        <div class="as-margin-bottom-space-2 as-display-flex">
            <h4 class="as-margin-auto as-margin-left-none">Deltakere</h4>
        </div>
        <div class="as-display-flex-wrap main-div-deltakere">
            <div class="as-card-2 stat-item as-margin-right-space-4 as-padding-space-2 as-margin-top-space-2 nosh-impt as-card-lightest-color">
                <h5 class="title">Antall deltakere</h5>
                <div class="content as-display-flex as-margin-top-spcae-4">
                    <h2 class="as-margin-auto-impt antall-deltakere">127</h2>
                </div>
            </div>
            <div class="as-card-2 stat-item as-margin-right-space-4 as-padding-space-2 as-margin-top-space-2 nosh-impt as-card-lightest-color">
                <h5 class="title">Relatert til Stranda Kommune</h5>
                <div class="content as-display-flex as-margin-top-spcae-4">
                    <div class="as-margin-auto-impt lines-chart">
                        <Line 
                            :title="'100 Møre og Romsdal'" 
                            :lines="[{navn : '100 deltakere', 'value' : ((100/127)*100), 'color' : 'grey'}]"
                        />
                            
                        <Line class="as-margin-top-space-2" 
                            :title="'127 Stranda Kommune'" 
                            :lines="[{navn : '127 deltakere', 'value' : 100, 'color' : 'grey'}]"
                        />
                    </div>
                </div>
            </div>
            <div class="as-card-2 stat-item as-padding-space-2 as-margin-top-space-2 nosh-impt as-card-lightest-color">
                <h5 class="title">Relatert til Møre og Romsdal</h5>
                <div class="content as-display-flex as-margin-top-spcae-4">
                    <div class="as-margin-auto-impt lines-chart">
                        <Line 
                            :title="'100 Møre og Romsdal'" 
                            :lines="[{navn : '100 deltakere', 'value' : ((100/127)*100), 'color' : 'grey'}]"
                        />
                            
                        <Line class="as-margin-top-space-2" 
                            :title="'127 Stranda Kommune'" 
                            :lines="[{navn : '127 deltakere', 'value' : 100, 'color' : 'grey'}]"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Aldersfordeling -->
        <div class="as-margin-top-space-8 as-margin-bottom-space-2 as-display-flex">
            <h4 class="as-margin-auto as-margin-left-none">Aldersfordeling</h4>
        </div>
        <div class="as-display-flex-wrap main-div-deltakere">
            <div class="as-card-2 stat-item as-margin-right-space-4 as-padding-space-2 as-margin-top-space-2 nosh-impt as-card-lightest-color">
                <h5 class="title">Festivalen i Stranda</h5>
                <div class="content as-display-flex as-margin-top-spcae-4">
                    <Doughnut 
                        class="as-margin-auto-impt" 
                        ref="antall-brukere" 
                        :loading="false"
                        :barColors="['#726a6a', '#404040', '#bebebe', 'grey']"
                        :labels="['10 år', '11 år', '12 år', '13 år']" :chartData="[10, 20, 50, 32]" />
                </div>
            </div>
            
            <div class="as-card-2 stat-item as-margin-right-space-4 as-padding-space-2 as-margin-top-space-2 nosh-impt as-card-lightest-color">
                <h5 class="title">Festivalen i Stranda</h5>
                <div class="content as-display-flex as-margin-top-spcae-4">
                    <Doughnut 
                        class="as-margin-auto-impt" 
                        ref="antall-brukere" 
                        :loading="false"
                        :barColors="['magenta', 'green', 'yellow', 'lime']"
                        :labels="['10 år', '11 år', '12 år', '13 år']" :chartData="[10, 20, 50, 32]" />
                </div>
            </div>
        </div>

        <!-- Kjønnsfordeling -->
        <div class="as-margin-top-space-8 as-margin-bottom-space-2 as-display-flex">
            <h4 class="as-margin-auto as-margin-left-none">Kjønnsfordeling</h4>
        </div>
        <div class="as-display-flex-wrap main-div-deltakere">
            <div class="as-card-2 stat-item as-margin-right-space-4 as-padding-space-2 as-margin-top-space-2 nosh-impt as-card-lightest-color">
                <h5 class="title">Festivalen i Stranda</h5>
                <div class="content as-display-flex as-margin-top-spcae-4">
                    <Doughnut 
                        class="as-margin-auto-impt" 
                        ref="antall-brukere" 
                        :loading="false"
                        :barColors="['#726a6a', '#404040']"
                        :labels="['F', 'M']" :chartData="[47, 53]" />
                </div>
            </div>
            
            <div class="as-card-2 stat-item as-margin-right-space-4 as-padding-space-2 as-margin-top-space-2 nosh-impt as-card-lightest-color">
                <h5 class="title">Relatert til Møre og Romsdal</h5>
                <div class="content as-display-flex as-margin-top-spcae-4">
                    <div class="as-margin-auto-impt lines-chart">
                        <Line 
                            :title="'100 Møre og Romsdal'" 
                            :lines="[{navn : '127 M', 'value' : 100, 'color' : 'grey'}, {navn : '20 F', 'value' : 97, 'color' : '#bebebe'}]"
                        />
                            
                        <Line class="as-margin-top-space-2"
                            :title="'100 Møre og Romsdal'" 
                            :lines="[{navn : '127 M', 'value' : 70, 'color' : 'grey'}, {navn : '20 F', 'value' : 100, 'color' : '#bebebe'}]"
                        />
                    </div>
                </div>
            </div>
        </div>


        <!-- Sjangerfordeling -->
        <div class="as-margin-top-space-8 as-margin-bottom-space-2 as-display-flex">
            <h4 class="as-margin-auto as-margin-left-none">Sjangerfordeling</h4>
        </div>
        <div class="as-display-flex-wrap main-div-deltakere">
            <div class="as-card-2 stat-item as-margin-right-space-4 as-padding-space-2 as-margin-top-space-2 nosh-impt as-card-lightest-color">
                <h5 class="title">Festivalen i Stranda</h5>
                <div class="content as-display-flex as-margin-top-spcae-4">
                    <Doughnut 
                        class="as-margin-auto-impt" 
                        ref="antall-brukere" 
                        :loading="false"
                        :barColors="['#726a6a', '#404040', '#bebebe', 'grey', 'magenta', 'green', 'yellow', 'lime', 'blue', 'purple']"
                        :labels="['F', 'M']" :chartData="[17, 9, 15, 6, 7, 25, 67, 4, 12, 3]" />
                </div>
            </div>
            
            <div class="as-card-2 stat-item as-margin-right-space-4 as-padding-space-2 as-margin-top-space-2 nosh-impt as-card-lightest-color">
                <h5 class="title">Relatert til Møre og Romsdal</h5>
                <div class="content as-display-flex as-margin-top-spcae-4">
                    <Doughnut 
                        class="as-margin-auto-impt" 
                        ref="antall-brukere" 
                        :loading="false"
                        :barColors="['#726a6a', '#404040', '#bebebe', 'grey', 'magenta', 'green', 'yellow', 'lime', 'blue', 'purple']"
                        :labels="['F', 'M']" :chartData="[17, 9, 15, 6, 7, 25, 67, 4, 12, 3]" />
                </div>
            </div>
        </div>


    </div>
</template>

<script lang="ts">
import Line from '../charts/Line.vue';
import Doughnut from '../charts/Doughnut.vue';



export default {
    mounted() {

    },
    components: {
        Doughnut : Doughnut,
        Line : Line
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
.stat-item {
    width: 31.2%;
    min-height: 300px;
}
.stat-item .content{
    height: 100%;
}
.lines-chart {
    width: 100%;
}
.antall-deltakere {
    font-size: 50px;
    font-weight: 100;
}
</style>


