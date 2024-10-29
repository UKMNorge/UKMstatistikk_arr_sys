<template>
    <div>
        <div v-show="hasMessage == true" class="as-margin-top-space-4">
            <div v-for="(kommune, index) in selectedKommuner" :key="index">
                <div class="as-margin-top-space-2" v-if="(getKommuneInfo(kommune)?.brukteKommuner?.length ?? 0) > 1">
                    <!-- Send this part to PermanentNotification component -->
                    <PermanentNotification 
                        :typeNotification="'primary'" 
                        :isHTML="true"
                        :tittel="kommune.title"
                        :description="generateDescription(kommune)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import type Kommune from '../../objects/Kommune'; // Ensure Kommune is imported correctly
import { summarizeData } from '../../utils/Kommuner';
import { PermanentNotification } from 'ukm-components-vue3';


export default defineComponent({
    props: {
        alleKommuner: {
            type : Array as () => any[],
            required: true,
            defualt: []
        },
        selectedKommuner: {
            type: Array as () => Kommune[],
            required: true,
            defualt: []
        },
    },
    components: {
      PermanentNotification,
    },
    data() {
        return {
            hasMessage: false,
            kommuneTittel: '',
        };
    },
    mounted() {

    },
    methods: {
        getKommuneInfo(kommune : Kommune) : { summary: { [range: string]: string }[], brukteKommuner: string[]} | null {
            let retObj: { summary: { [range: string]: string }[], brukteKommuner: string[] } = { summary: [], brukteKommuner: [] };
            let obj = summarizeData(this.alleKommuner[kommune.id]);

            if(obj.brukteKommuner.length > 1) {
                this.hasMessage = true;
            }
            retObj = obj;
            return retObj;
        },
        generateDescription(kommune : Kommune) : string {
            const kommuneTitle = kommune.title;
            const summaryInfo = this.getKommuneInfo(kommune)?.summary || [];

            // Convert summary info into HTML strings
            const summaryHtml = summaryInfo
                .map(
                    (summary) =>
                        `<p class="year-kommune-msg-flere-kommuner"><span class="year-item-flere-kommuner">${Object.keys(summary)[0]}:</span> <span>${Object.values(summary)[0]}<span></p>`
                )
                .join('');

            // Generate final HTML string for description
            return `<div class="as-margin-bottom-space-2"><p>Statistikken for ${kommuneTitle} er generert ved bruk av flere kommuner, på grunn av tidligere sammenslåinger og/eller navneendringer.</p></div>
                    ${summaryHtml}`;
        },
    }
});
</script>

<style>
.year-kommune-msg-flere-kommuner .year-item-flere-kommuner {
    font-weight: bold;
    margin-right: 5px;
}
</style>