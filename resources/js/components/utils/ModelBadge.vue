<template>
    <BaseBadge :label="modelInstanceName" :disable-tooltip="disableTooltip" :tooltip="tooltip" :icon="modelIcon"/>
</template>

<script>

import {mapGetters} from "vuex";
import utils from "../../config-utils";
import BaseBadge from "./BaseBadge";

export default {
    components: {BaseBadge},
    props: {
        disableTooltip: {
            default: false,
            type: Boolean
        },
        value: String,
        model: {
            default: null,
            type: String | null
        },
        options: {
            type: Object,
            default: () => {}
        }
    },
    computed: {
        ...mapGetters([
            'ui',
            'outputs',
            'roles',
            'templates',
            'relation_types',
            'processors',
            'action_types',
            'list_configs',
            'events'
        ]),
        modelValue() {
            if (this.options.model) {
                return this.options.model;
            }
            // z.B. allisa/demo::role|eec38eda-bbdf-40d0-9402-4a6deaf30366
            if(this.value.includes('::') && this.value.includes('|')){
                let parts = this.value.split('::');

                return parts[1].split('|')[0] || ''
            }
        },
        modelName() {
            return {
                relationType: 'Verknüpfungstyp',
                template: 'Vorlage',
                role: 'Rolle',
                actionType: 'Aktion',
                process: 'Prozess',
                listConfig: 'Liste',
                processor: 'Prozessor',
                event: 'Event',
                user: 'Benutzer'
            }[this.modelValue] || this.modelValue;
        },
        modelInstanceName() {
            let model = this.modelValue;
            let instanceId = this.value.includes('|') ? this.value.split('|')[1] : this.value;
            let attribute = null;
            let parts = [];

            // Falls ein Attribut angegeben wurde.
            if (instanceId.includes('.')) {
                parts = instanceId.split('.');
                instanceId = parts[0];
                attribute = parts[1];
            }

            let repo = {
                relationType: 'relation_types',
                template: 'templates',
                role: 'roles',
                actionType: 'action_types',
                processor: 'processors',
                listConfig: 'list_configs',
                event: 'events',
            }[model] || model;

            let item = (this[repo] || []).find(ele => ele.id === instanceId) || null;

            if (model === 'processor' && item) {
                return (attribute ? this.attributeName(attribute) : 'Ergebnis') + ' von "' + this.processorNames(item.identifier) + '" - ID: ' + item.id.substring(0, 4);
            }
            else if (model === 'processor' && !item) {
                return this.value + ' (Prozessor existiert nicht mehr)';
            }

            return (item || {}).name || 'Unknown';

        },
        modelIcon() {
            return {
                relationType: 'settings_ethernet',
                template: 'wysiwyg',
                role: 'assignment_ind',
                actionType: 'flash_on',
                processor: 'settings',
                listConfig: 'list',
                event: 'flag'
            }[this.modelValue] || 'help';
        },
        tooltip() {
            return this.disableTooltip ? '' : this.modelName;
        }
    },
    methods: {
        ...utils,
        attributeName(attr) {
            return {
                id: 'Id',
                name: 'Name'
            }[attr] || attr;
        },
    }
};
</script>
