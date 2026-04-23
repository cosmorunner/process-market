<template>
    <div>
        <div class="form-group mb-2">
            <label class="mb-0">Öffentliche Rolle hinzufügen</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="isPublicRole" :disabled="!ui.editable"
                       :checked="options.is_public_role" @click="onClickIsPublicRole">
                <label class="custom-control-label" for="isPublicRole"></label>
            </div>
            <small class="text-muted">Alle Benutzer der Plattform erhalten Zugriff auf die Prozess-Instanz in dieser Rolle.</small>
        </div>
        <AutocompleteSelector v-if="!options.is_public_role" :items="options.recipients" :label="'Benutzer/Gruppen'"
                              :icon="'group'" :action-type="actionType" :outputs-from-actiontype-only="true"
                              :syntax-include="['process.outputs', 'action.outputs', 'auth', 'reference.outputs']"
                              :pipe-include="['environment_groups', 'environment_users', 'environment_bots']"
                              :editable="ui.editable" @items-changed="$emit('option-change', 'recipients', $event)"/>
        <AutocompleteSelector :items="options.role ? [options.role] : []" :label="'Rolle'" :icon="'local_offer'"
                              :action-type="actionType" :pipe-include="['roles']" :max-items="1" :editable="ui.editable"
                              @items-changed="$emit('option-change', 'role', $event.length ? $event[0] : null)"/>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import AutocompleteSelector from "../../utils/AutocompleteSelector";
import {mapGetters} from "vuex";

export default {
    components: {
        AutocompleteSelector
    },
    props: {
        options: Object,
        actionType: Object
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
    },
    methods: {
        ...utils,
        onClickIsPublicRole() {
            if (!this.options.is_public_role) {
                this.$emit('option-change', 'recipients', []);
            }
            this.$emit('option-change', 'is_public_role', !this.options.is_public_role);
        }
    }
};
</script>
