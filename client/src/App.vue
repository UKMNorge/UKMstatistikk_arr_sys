<template>
    <div class="as-margin-top-space-2">
        <div class="as-container">
            <div class="container">
                <div class="as-margin-top-space-8 as-margin-bottom-space-8">
                    <h1 class="">Statistikk</h1>
                </div>
           
        
                <v-tabs align-tabs="center"
                    class="as-card-1 nosh-impt" 
                    fixed-tabs
                    bg-color="#fff"
                    v-model="tab">
                    <v-tab text="Kommunestatistikk"></v-tab>
                    <v-tab text="Fylkestatistikk"></v-tab>
                    <v-tab text="Generell statistikk"></v-tab>
                </v-tabs>
                
                <div class="as-margin-top-space-4">
                    <v-tabs-window v-model="tab">
                        <!-- Kommunestatistikk -->
                        <v-tabs-window-item>
                        <div class="as-containercontainer">
                                <KommuneStatistikk />
                            </div>
                        </v-tabs-window-item>
                        
                        <!-- Påmeldingssystem -->
                        <v-tabs-window-item>
                            <div class="as-containercontainer">
                                <PaameldingsystemStatistikk />
                            </div>
                        </v-tabs-window-item>
                        
                        <!-- Generell statistikk -->
                        <v-tabs-window-item>
                            <div class="as-containercontainer">
                                <GenerellStatistikk />
                            </div>
                        </v-tabs-window-item>
                    </v-tabs-window>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";

// Components
import KommuneStatistikk from './components/KommuneStatistikk.vue';
import PaameldingsystemStatistikk from './components/PaameldingsystemStatistikk.vue';
import GenerellStatistikk from './components/GenerellStatistikk.vue';

export default {

    data() {
        return {
            tab : null as any,
            spaInteraction : (<any>window).spaInteraction,
        }
    },

    components : {
        KommuneStatistikk : KommuneStatistikk,
        PaameldingsystemStatistikk : PaameldingsystemStatistikk,
        GenerellStatistikk : GenerellStatistikk,
    },
    

    mounted: function () {

    },
    created() {
        const router = useRouter();
        const route = useRoute();
        this.tab = route.query.tab || '1';
        
        watch(() => this.tab, (newTab) => {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', newTab);
            window.history.pushState({}, '', url.toString());
        });
    
        onMounted(() => {
            const url = new URL(window.location.href);
            this.tab = url.searchParams.get('tab') || '1';
        });
    },
    
    methods: {
        
    }
}
</script>
