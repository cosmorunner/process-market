<template>
    <div>
        <div class="form-group mb-2">
            <label class="mb-0">Öffentliche Rolle entfernen</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="isPublicRole" :disabled="!ui.editable"
                       :checked="options.is_public_role" @click="onClickIsPublicRole">
                <label class="custom-control-label" for="isPublicRole"></label>
            </div>
        </div>
        <AutocompleteSelector v-if="!options.is_public_role" :items="options.recipients"
                              :label="'Benutzer/Gruppen/Rollen'" :icon="'group'" :max-items="5"
                              :action-type="actionType" :outputs-from-actiontype-only="true"
                              :syntax-include="['process.outputs', 'action.outputs', 'auth', 'reference.outputs']"
                              :pipe-include="['environment_groups', 'environment_users', 'environment_bots', 'roles']"
                              :editable="ui.editable" @items-changed="$emit('option-change', 'recipients', $event)"/>
        <AutocompleteSelector :items="options.role ? [options.role] : []" :label="'Rollen-Filterung'"
                              :icon="'local_offer'" :action-type="actionType" :pipe-include="['roles']" :max-items="1"
                              :editable="ui.editable"
                              @items-changed="$emit('option-change', 'role', $event.length ? $event[0] : null)"/>
        <small class="text-muted" v-if="!options.is_public_role">Bestimmten Rollen-Zugriff eines Benutzers oder einer Gruppe löschen.</small>
        <small class="text-muted" v-if="options.is_public_role">Nach einer bestimmten öffentlichen Rolle filtern. Leer lassen um alle dynamisch hinzugefügten, öffentlichen Rollen zu entfernen.
            Die statische, öffentliche Rolle des Prozesses kann nicht entfernt werden.</small>
    </div>
</template>

<script>

import AutocompleteSelector from "../../utils/AutocompleteSelector";
import {mapGetters} from "vuex";

export default {
    components: {AutocompleteSelector},
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
        onClickIsPublicRole() {
            if (!this.options.is_public_role) {
                this.$emit('option-change', 'recipients', []);
            }
            this.$emit('option-change', 'is_public_role', !this.options.is_public_role);
        }
    }
};
</script>
