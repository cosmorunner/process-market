<template>
    <div>
        <div class="form-group input-group-sm mb-0 flex-grow-1" ref="codeEditorWrapper"
             v-if="template.hasOwnProperty('data')" :style="'height:' + (ui.modal.bodyHeight - 100) + 'px'">
            <CodeEditor :code="template.mapping.js.value" @update-code="updateJs" :editable="ui.editable"
                        :max-height="ui.modal.bodyHeight - 100" :fill-empty-lines-up-to="50"/>
            <p class="my-2">
                <span class="material-icons text-primary">info</span>
                <span>Das JavaScript muss ein JSON-Objekt zurückgeben. Die Mustache JS Listenspalten-Vorlage wird mit diesem JSON-Objekt gerendert.
                    Mit der Variable "row" kann auf Beispielwerte einer Listenzeile zugegriffen werden, siehe "Vorschau -> Werte".</span>
            </p>
            <div v-for="error in (ui.validationErrors.name || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import CodeEditor from "./CodeEditor";
import OptionBadges from "../utils/OptionBadges";
import GlobalTemplateData from "./partials/GlobalTemplateData";
import AllowedHtmlTags from "./partials/AllowedHtmlTags.vue";
import AutocompleteSelector from "../utils/AutocompleteSelector";

export default {
    components: {
        AutocompleteSelector,
        GlobalTemplateData,
        AllowedHtmlTags,
        OptionBadges,
        CodeEditor
    },
    props: {
        template: Object
    },
    data() {
        return {
            loading: false,
            error: null,
            errorMessage: '',
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'list_configs',
        ]),
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        updateJs(string) {
            this.$emit('update-property', 'mapping', {
                'js': {
                    ...this.template.mapping.js,
                    'value': string.trim()
                }
            });
        },
    },
    watch: {
        template: {
            handler() {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    }

};
</script>
