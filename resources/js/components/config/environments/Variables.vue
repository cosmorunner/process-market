<template>
    <div>
        <ModalHeader :title="addMode ? 'Neue Umgebung' : 'Umgebung bearbeiten'" v-on="$listeners"/>
        <div class="modal-body py-2">
            <Navigation class="mb-4" :detail-component-name="detailComponentName" v-on="$listeners"/>
            <ul v-if="(blueprint.variables || []).length" v-for="variable in sortedVariables"
                class="list-group list-group-flush">
                <li class="list-group-item p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-10 hover-pointer p-2" @click="navigationChange('Variable', variable)">
                                <span>{{ variable.identifier }}</span>
                            </div>
                            <div class="col-2 p-2">
                                <button class="btn btn-sm btn-light float-right" @click="onDeleteVariable(variable.identifier)" v-if="ui.editable">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <button class="btn btn-sm my-2 btn-outline-primary" @click="navigationChange('Variable')" v-if="ui.editable">
                <span class="material-icons">add</span>
            </button>
        </div>
        <ModalFooter :ui="ui" :save-label="'Speichern'" v-on="$listeners"/>
    </div>
</template>

<script>

import {mapActions, mapGetters} from "vuex";
import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import Navigation from "./Navigation";
import utils from "../../../config-utils";
import {reduxActions} from "../../../store/develop-and-config";

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
        ]),
        blueprint() {
            return this.environment.blueprint;
        },
        sortedVariables() {
            return [...this.blueprint.variables].sort((a, b) => a.identifier > b.identifier ? 1 : -1)
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        navigationChange(to, connector) {
            this.$emit('navigation-change', to, connector);
        },
        onDeleteVariable(identifier) {
            this.patchBlueprint('DeleteVariable', {identifier}).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            });
        },
    }
};
</script>
