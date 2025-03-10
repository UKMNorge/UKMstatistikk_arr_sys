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
                    <v-tab text="Arrangementsstatistikk"></v-tab>
                    <v-tab text="Kommunestatistikk"></v-tab>
                    <v-tab v-if="isFylkeAdmin" text="Fylkestatistikk"></v-tab>
                    <v-tab v-if="isSuperadmin" text="Nasjonal statistikk"></v-tab>
                </v-tabs>
                
                <div class="as-margin-top-space-4">
                    <v-tabs-window v-model="tab">

                        <!-- Arrangementstatistikk -->
                        <v-tabs-window-item>
                            <div class="as-containercontainer">
                                <Arrangementstatistikk />
                            </div>
                        </v-tabs-window-item>

                        <!-- Kommunestatistikk -->
                        <v-tabs-window-item>
                        <div class="as-containercontainer">
                                <KommuneStatistikk />
                            </div>
                        </v-tabs-window-item>
                        
                        <!-- Påmeldingssystem -->
                        <v-tabs-window-item v-if="isFylkeAdmin">
                            <div class="as-containercontainer">
                                <FylkeStatistikk />
                            </div>
                        </v-tabs-window-item>
                        
                        <!-- Generell statistikk -->
                        <v-tabs-window-item v-if="isSuperadmin">
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
import Arrangementstatistikk from './components/Arrangementstatistikk.vue';
import KommuneStatistikk from './components/KommuneStatistikk.vue';
import FylkeStatistikk from './components/FylkeStatistikk.vue';
import GenerellStatistikk from './components/GenerellStatistikk.vue';

export default {

    data() {
        return {
            tab : null as any,
            spaInteraction : (<any>window).spaInteraction,
            isFylkeAdmin : false as boolean,
            isSuperadmin : false as boolean,
        }
    },

    components : {
        Arrangementstatistikk : Arrangementstatistikk,
        KommuneStatistikk : KommuneStatistikk,
        FylkeStatistikk : FylkeStatistikk,
        GenerellStatistikk : GenerellStatistikk,
    },
    

    mounted: function () {
        this.initAuthorizations();
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
        initAuthorizations() {
            this.isFylkeAdmin = this.erFylkeAdmin();
            this.isSuperadmin = this.erSuperAdmin();
        },
        erFylkeAdmin() : boolean {
            for(let omradeItem of (<any>window).ukm_statistikk_klient.omrade) {
                if(omradeItem.type == 'fylke') {
                    return true;
                }
            }
            return false;
        },
        erSuperAdmin() : boolean {
            if((<any>window).ukm_statistikk_klient.is_superadmin) {
                return true;
            }
            return false;
        }
    }
}
</script>

<style>
.statistikk-feedback {
    display: flex;
}
.statistikk-feedback .as-container.container {
    padding: 0;
}

</style>