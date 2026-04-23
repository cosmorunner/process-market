<template>
    <div>
        <TemplateModalDataMappingTwig v-if="template.type !== 'mustache_list_column'" :template="template"
                                      v-on="$listeners"/>
        <TemplateModalDataMappingMustache v-else :template="template" v-on="$listeners"/>
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
import TemplateModalDataMappingTwig from "./TemplateModalDataMappingTwig.vue";
import TemplateModalDataMappingMustache from "./TemplateModalDataMappingMustache.vue";

export default {
    components: {
        TemplateModalDataMappingMustache,
        TemplateModalDataMappingTwig,
        AutocompleteSelector,
        GlobalTemplateData,
        AllowedHtmlTags,
        OptionBadges,
        CodeEditor
    },
    props: {
        template: Object
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
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
