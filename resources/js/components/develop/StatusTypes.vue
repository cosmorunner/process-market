<template>
    <div class="control-panel-statuses">
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0"
             v-if="!simulation.running && ui.editable">
            <div class="card-header bg-white align-items-center px-2 py-1 border-bottom-0">
                <div class="d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-primary" @click="handleOpenModal">
                            <span class="material-icons">add</span> Hinzufügen
                        </button>
                        <button class="btn btn-sm btn-outline-primary ml-2" @click="handleOpenBulkModal(null)" v-if="ui.editable">
                            <span class="material-icons">add_to_photos</span>
                        </button>
                    </div>
                    <div class="d-flex">
                        <Docs article="rules-status"/>
                        <div class="input-group input-group-sm ml-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white">
                                    <span class="material-icons">search</span>
                                </span>
                            </div>
                            <input type="text" class="form-control" aria-label="Search" v-model="search"
                                   style="max-width:100px">
                            <div class="input-group-append" v-if="search">
                                <button class="btn btn-sm btn-outline-danger" @click="search = ''">
                                    <span class="material-icons text-danger">close</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <template v-for="statusType in sortedStatusTypes">
            <StatusType :ref="statusType.id" :status-type="statusType" :show-states="false" :editable="ui.editable"
                        @setEditId="onUpdateEditId"/>
        </template>
        <div class="card mb-3 rounded-0 border-left-0 border-right-0" v-if="!status_types.length">
            <div class="card-body px-2 py-1">
                <span>Erstellen Sie einen neuen Status mit einem Rechtsklick auf die Prozess-Fläche.</span>
            </div>
        </div>
    </div>
</template>

<script>

import StatusType from "./StatusType";
import utils from "../../develop-utils";
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../store/develop-and-config";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        StatusType
    },
    props: {
        status_types: Array
    },
    data() {
        return {
            editingId: null,
            search: ''
        };
    },
    computed: {
        ...mapGetters([
            'simulation',
            'ui'
        ]),
        sortedStatusTypes() {
            return [...this.status_types].sort((a, b) => a.name.toLowerCase().localeCompare(b.name.toLowerCase()))
                .filter((a) => !this.search ? true : a.name.toLowerCase().includes(this.search.toLowerCase()));
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        handleOpenModal() {
            this.openModal({
                componentName: 'StatusTypeModal',
                data: {
                    position: null,
                    statusTypeId: null
                }
            });
        },
        onUpdateEditId(statusTypeId) {
            if (statusTypeId && this.editingId) {
                // $refs returns an array of vue-components
                this.$refs[this.editingId][0].saveStatusTypeName();
            }
            this.editingId = statusTypeId;
        },
        handleOpenBulkModal(){
            this.openModal({
                componentName: 'BulkModalText',
                data: {
                    position: null,
                    title: 'Status',
                    article: 'rules-status',
                    section: 'bulk-create-status',
                    method: this.actionType ? 'UpdateStatusTypeBulk' : 'StoreStatusTypeBulk',
                    methodData: {
                        status_type_id: this.statusType ? this.statusType.id : null
                    },
                    format: '<Status-Name>;<?Initialwert>',
                    examples: [
                        {
                            syntax: 'Mein Status',
                            description: 'Status mit dem Namen "Mein Status" und einem Initial-Zustand "Start" mit dem Wert -1.'
                        },
                        {
                            syntax: 'Mein Status;10',
                            description: 'Status mit dem Namen "Mein Status" und einem Initial-Zustand "Start" mit dem Wert 10.'
                        }
                    ]
                }
            });
        }
    }
};
</script>
