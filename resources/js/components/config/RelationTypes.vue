<template>
    <div>
        <LoadingSeparator :ui="ui" :clear-error="clearError"/>
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary btn-sm mb-2" @click="openAddRelationTypeModal" v-if="ui.editable">
                        Verknüpfungstyp anlegen
                    </button>
                    <Docs class="mr-2" article="relationtypes"/>
                </div>
                <div class="btn-group mb-2" role="group" aria-label="Button group with nested dropdown" v-if="ui.editable && showPasteRelationTypeButton">
                    <button class="btn btn-sm btn-warning"
                            @click="pasteRelationType">
                        Einfügen
                    </button>
                    <button class="btn btn-sm btn-warning" @click="clearCopyElement">
                        <span class="material-icons">close</span>
                    </button>
                </div>
                <div class="row mb-2" v-for="relationType in sortedRelationTypes">
                    <div class="col">
                        <div class="rounded-0 card h-100">
                            <div
                                class="card-header px-2 py-1 d-flex justify-content-between border-primary mouse-pointer">
                                <span class="text-primary flex-grow-1 text-truncate disable-user-select"
                                      @click="onOpenEditModal(relationType)">
                                    <span>{{ relationType.name }}</span>
                                    <span class="badge badge-primary">{{
                                            getConnectionTypeLabel(relationType.connection_type)
                                        }}</span>
                                    <small class="text-muted" v-if="relationType.reference">
                                        <span>- Referenz: {{ relationType.reference }}</span>
                                    </small>
                                </span>
                                <button class="btn btn-sm btn-light text-primary p-0 mr-2"
                                        @click="copyRelationType(relationType)">
                                    <span class="material-icons">content_copy</span>
                                </button>
                                <button class="btn btn-sm btn-light text-danger p-0"
                                        @click="deleteRelationType(relationType.id)" v-if="ui.editable">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                            <div class="card-body p-2 hover-pointer" @click="onOpenEditModal(relationType)">
                                <span class="text-muted d-block">{{ relationType.description || '&nbsp;' }}</span>
                                <span v-if="Object.keys(relationType.default ||{}).length">
                                    <hr/>
                                    <template v-for="key in (Object.keys(relationType.default ||{}).sort())">
                                        <span class="d-block mb-1">
                                            <span>{{ key }}</span>
                                            <span>&#x27A1;</span>
                                            <OptionBadges :value="relationType.default[key] || ''"/>
                                        </span>
                                    </template>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import LoadingSeparator from "./LoadingSeparator";
import OptionBadges from "../utils/OptionBadges";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        OptionBadges,
        LoadingSeparator
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
            'relation_types'
        ]),
        sortedRelationTypes() {
            return [...this.relation_types].sort((a, b) => a.name > b.name ? 1 : -1);
        },
        showPasteRelationTypeButton() {
            return this.getCopyElement()?.name === 'relation_type';
        }
    },
    methods: {
        ...mapActions(reduxActions), ...utils,
        openAddRelationTypeModal() {
            this.openModal({
                componentName: 'RelationTypeModal',
                data: {
                    relationType: null
                }
            });
        },
        onOpenEditModal(relationType) {
            this.openModal({
                componentName: 'RelationTypeModal',
                data: {
                    relationType: relationType
                }
            });
        },
        deleteRelationType(id) {
            this.patchDefinition('DeleteRelationType', {id}).catch(() => {
            });
        },
        getConnectionTypeLabel(connectionType) {
            let label = '';
            switch (connectionType) {
                case '1-1':
                    label = '1:1';
                    break;
                case '1-n':
                    label = '1:N';
                    break;
                case 'n-1':
                    label = 'N:1';
                    break;
                case 'n-n':
                    label = 'N:M';
                    break;
                default:
                    label = '';
            }
            return label;
        },
        copyRelationType(relationType) {
            this.saveCopyElement('relation_type', relationType);
        },
        pasteRelationType() {
            let data = {
                name: this.getCopyElement()?.name,
                options: {}
            };
            this.patchDefinition('PasteElement', data).catch(() => {
            });
        }
    },
};
</script>
