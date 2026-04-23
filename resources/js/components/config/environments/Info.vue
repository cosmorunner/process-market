<template>
    <div>
        <ModalHeader :title="addMode ? 'Standard' : 'Umgebung bearbeiten'" v-on="$listeners"/>
        <div class="modal-body py-2">
            <Navigation class="mb-4" :detail-component-name="detailComponentName" v-on="$listeners"/>
            <div>
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" class="custom-control-input" id="default" :checked="environment.default" @click="toggleDefault" :disabled="!ui.editable">
                    <label class="custom-control-label" for="default">Standard</label>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="environment.name" maxlength="40" @change="changeAttribute" :readonly="!ui.editable"/>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="name">Beschreibung</label>
                    <textarea id="description" class="form-control" name="description" maxlength="200"
                              v-model="environment.description" :readonly="!ui.editable"
                              @change="changeAttribute">{{ environment.description }}</textarea>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="name">Initiale Aktion</label>
                    <select class="form-control form-control-sm mt-2" id="action_type_id" name="initial_action_type_id"
                            :value="environment.initial_action_type_id" :disabled="!ui.editable"
                            @change="changeAttribute">
                        <option :value="''">Keine Initial-Aktion</option>
                        <option :value="actionType.id" v-for="actionType in action_types">
                            {{ actionType.name }}
                        </option>
                    </select>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="name">Standard-Benutzer</label>
                    <select class="form-control form-control-sm mt-2" id="default_user" name="default_user"
                            :value="environment.default_user" :disabled="!ui.editable" @change="changeAttribute">
                        <option :value="null">Demo Benutzer</option>
                        <option :value="user.id" v-for="user in environment_users">
                            {{ user.first_name }} {{ user.last_name }}
                        </option>
                    </select>
                </div>
                <div class="form-group input-group-sm mb-2" v-if="environment.initial_action_type_id">
                    <label class="mb-0" for="name">URL "context" Parameter</label>
                    <select class="form-control form-control-sm mt-2" id="query_context" name="query_context"
                            :value="environment.query_context" :disabled="!ui.editable"
                            @change="changeAttribute">
                        <option :value="''">Kein "context" Parameter</option>
                        <option :value="'process|' + process.id + '[Prozess - ' + process.name + ']'"
                                v-for="process in environmentProcesses">
                            {{ process.name }}
                        </option>
                    </select>
                    <small class="text-muted">Der URL "context" Parameter kann bei Initial-Aktionen genutzt werden, um Daten von einem
                        Prozess vorzuladen. Der ausgewählte Prozess wird als Model-Pipe-Notation zum "context" Parameter
                        hinzugefügt.</small>
                </div>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="public" :checked="environment.public" @click="togglePublic" :disabled="!ui.editable">
                    <label class="custom-control-label" for="public">Öffentlich</label>
                </div>
                <small class="text-muted">Für öffentliche Demos freischalten.</small>
            </div>
        </div>
        <ModalFooter :ui="ui" :save-label="'Speichern'" v-on="$listeners"/>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../../store/develop-and-config";
import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import Navigation from "./Navigation";

export default {
    components: {
        Navigation,
        ModalHeader,
        ModalFooter,
    },
    props: {
        detailComponentName: String,
        environment: Object,
        addMode: Boolean,
        processVersionId: String
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
            'environment_users'
        ]),
        blueprint() {
            return this.environment.blueprint;
        },
        environmentProcesses() {
            return this.blueprint.processes;
        },
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        toggleDefault(e) {
            this.$emit('change-attribute', 'default', e.target.checked);
        },
        togglePublic(e) {
            this.$emit('change-attribute', 'public', e.target.checked);
        },
        changeAttribute(e) {
            this.$emit('change-attribute', e.target.name, e.target.value);

            // query_context leeren wenn keine Initialaktion gewählt wurde.
            if (e.target.name === 'initial_action_type_id' && !e.target.value) {
                this.$emit('change-attribute', 'query_context', '');
            }
        },
        navigationChange(to, process) {
            this.$emit('navigation-change', to, process);
        }
    }
};
</script>
