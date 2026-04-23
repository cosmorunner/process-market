<template>
    <div>
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0"
             v-if="!simulation.running && ui.editable">
            <div class="card-header bg-white px-2 py-1 border-bottom-0">
                <div class="d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-primary" @click="handleOpenModal">
                            <span class="material-icons">add</span> Hinzufügen
                        </button>
                        <button class="btn btn-sm btn-outline-primary ml-2" @click="handleOpenBulkModal(null)"
                                v-if="ui.editable">
                            <span class="material-icons">add_to_photos</span>
                        </button>
                    </div>
                    <div class="d-flex">
                        <Docs article="rules-actions"/>
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
        <template v-for="actionType in sortedActionTypes">
            <ActionType :ref="actionType.id" :action-type="actionType" :show-rules="false" :hide-delete-buttons="true"
                        :editable="ui.editable" @setEditId="onUpdateEditId"/>
        </template>
        <div class="card mb-3 rounded-0 border-left-0 border-right-0" v-if="!action_types.length">
            <div class="card-body px-2 py-1">
                <span>Erstellen Sie eine neue Aktion mit einem Rechtsklick auf die freie Graphen-Fläche.</span>
            </div>
        </div>
    </div>
</template>

<script>

import ActionType from "./ActionType";
import utils from "../../develop-utils";
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../store/develop-and-config";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        ActionType,
    },
    props: {
        action_types: Array
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
        sortedActionTypes() {
            return [...this.action_types].sort((a, b) => a.name.toLowerCase().localeCompare(b.name.toLowerCase()))
                .filter((a) => !this.search ? true : a.name.toLowerCase().includes(this.search.toLowerCase()));
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        handleOpenModal() {
            this.openModal({
                componentName: 'ActionTypeModal',
                data: {
                    position: null,
                    actionTypeId: null
                }
            });
        },
        onUpdateEditId(actionTypeId) {
            if (actionTypeId && this.editingId) {
                // $refs returns an array of vue-components
                this.$refs[this.editingId][0].saveName();
            }
            this.editingId = actionTypeId;
        },
        handleOpenBulkModal() {
            this.openModal({
                componentName: 'BulkModalText',
                data: {
                    position: null,
                    title: 'Aktionen',
                    article: 'rules-actions',
                    section: 'bulk-create-action',
                    method: this.actionType ? 'UpdateActionTypeBulk' : 'StoreActionTypeBulk',
                    methodData: {
                        action_type_id: this.actionType ? this.actionType.id : null,
                    },
                    format: '<Aktions-Name>',
                    examples: [
                        {
                            syntax: 'Meine Aktion',
                            description: 'Aktion mit dem Namen "Meine Aktion".'
                        }
                    ],
                    requiresDeleteHtml: true,
                }
            });
        }
    }
};
</script>
