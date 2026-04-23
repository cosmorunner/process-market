<template>
    <div>
        <ModalHeader :title="addMode ? 'Neue Umgebung' : 'Umgebung bearbeiten'" v-on="$listeners"/>
        <div class="modal-body py-2">
            <Navigation class="mb-4" :detail-component-name="detailComponentName" v-on="$listeners"/>
            <ul v-if="blueprint.processes.length" v-for="process in blueprint.processes"
                class="list-group list-group-flush">
                <li class="list-group-item p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-10 hover-pointer p-2" @click="navigationChange('Process', process)">
                                <span>{{ process.process_type }} - {{ process.name }}</span>
                                <span class="badge badge-light mr-1" v-if="Object.keys(process.initial_situation).length">Situation</span>
                                <span class="badge badge-light mr-1" v-if="Object.keys(process.initial_data).length">Daten</span>
                                <span class="badge badge-light mr-1" v-if="Object.keys(process.accesses).length">Zugriffe</span>
                            </div>
                            <div class="col-2 p-2">
                                <button class="btn btn-sm btn-light float-right" @click="onDeleteProcess(process.id)" v-if="ui.editable">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <button class="btn btn-sm my-2 btn-outline-primary" @click="navigationChange('Process')" v-if="ui.editable">
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
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        navigationChange(to, process) {
            this.$emit('navigation-change', to, process);
        },
        onDeleteProcess(id) {
            this.patchBlueprint('DeleteProcess', {id}).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            });
        }
    }
};
</script>
