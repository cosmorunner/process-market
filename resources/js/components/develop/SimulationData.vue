<template>
    <div>
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Meta-Daten</span>
            </div>
            <div class="card-body px-2 py-0" v-if="data.length">
                <table class="table mb-0">
                    <tbody>
                    <tr>
                        <td class="px-2 py-1 text-break border-top-0">Name</td>
                        <td class="px-2 py-1 text-break border-top-0">{{ metaData.name }}</td>
                    </tr>
                    <tr>
                        <td class="px-2 py-1 text-break">Beschreibung</td>
                        <td class="px-2 py-1 text-break">{{ trimmed(metaData.description, 50) }}</td>
                    </tr>
                    <tr>
                        <td class="px-2 py-1 text-break">Referenz</td>
                        <td class="px-2 py-1 text-break">{{ metaData.reference }}</td>
                    </tr>
                    <tr>
                        <td class="px-2 py-1 text-break">Tags</td>
                        <td>{{ metaData.tags.length ? metaData.tags.join(', ') : '' }}</td>
                    </tr>
                    <tr>
                        <td class="px-2 py-1 text-break">Icon</td>
                        <td>
                            <span class="material-icons" v-if="metaData.image">{{ metaData.image }}</span>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
            <div class="card-body px-2 py-1" v-if="!data.length">
                <span>Keine Prozess-Daten vorhanden.</span>
            </div>
        </div>
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Prozess-Daten</span>
            </div>
            <div class="card-body px-2 py-1" v-if="data.length">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th scope="col" class="px-2 py-0 pb-1 border-0 font-weight-normal text-muted">
                            <small>Name</small></th>
                        <th scope="col" class="px-2 py-0 pb-1 border-0 font-weight-normal text-muted">
                            <small>Wert</small></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in sortedData">
                        <td class="px-2 py-1 text-break">
                            <span>{{ item.name }}</span>
                        </td>
                        <td class="px-2 py-1 text-break">
                            <OptionBadges
                                v-if="typeof item.value === 'string' || typeof item.value === 'number' || item.value === null || item.value === 'undefined'"
                                :value="item.value" :disable-tooltip="true"/>
                            <span
                                v-if="typeof item.value === 'boolean'"><i>{{ item.value ? 'TRUE' : 'FALSE' }}</i></span>
                            <CodeEditor :code="JSON.stringify(item.value, null, 4)" :editable="false"
                                        v-if="Array.isArray(item.value) || typeof item.value === 'object'"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-body px-2 py-1" v-if="!data.length">
                <span>Keine Prozess-Daten vorhanden.</span>
            </div>
        </div>
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-body px-2 py-1">
                <small class="text-muted">Möglicherweise werden manche Daten aufgrund fehlender Rollen-Berechtigung
                    nicht angezeigt.</small>
            </div>
        </div>
    </div>
</template>

<script>

import {mapGetters} from "vuex";
import OptionBadges from "../utils/OptionBadges";
import CodeEditor from "../utils/CodeEditor.vue";

export default {
    components: {
        CodeEditor,
        OptionBadges
    },
    props: {
        data: Array,
        metaData: Object,
    }, ...mapGetters([
        'simulation',
    ]),
    methods: {
        trimmed(value, length = 100) {
            let trimmed = value || '';

            return trimmed.length > length ? trimmed.substring(0, length) + '...' : trimmed;
        }
    },
    computed: {
        sortedData() {
            // sorting copy instead of using the data directly as it would mutate the original dataset
            return [...this.data].sort((a, b) => {
                return a.name.localeCompare(b.name);
            });
        }
    }
};
</script>
