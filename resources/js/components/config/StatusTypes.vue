<template>
    <div>
        <LoadingSeparator :ui="ui" :clear-error="this.clearError"/>
        <div class="row" v-if="statusTypes.length">
            <div class="col">
                <div class="d-flex justify-content-end">
                    <Docs class="mr-2 mb-2" article="config-status"/>
                </div>
                <div class="row">
                    <template v-for="statusType in sortedStatusTypes">
                        <div :class="'mb-3 col-' + width(statusType.size)">
                            <div class="rounded-0 card h-100">
                                <div class="card-header px-2 py-1 d-flex justify-content-between border-primary">
                                    <span class="text-primary text-truncate disable-user-select">
                                        <span class="material-icons">{{ statusType.image }}</span>
                                        <span> {{ statusType.name }}</span>
                                        <span class="material-icons text-muted"
                                              v-if="statusType.hidden">visibility_off</span>
                                    </span>
                                </div>
                                <div class="card-body hover-pointer p-2" @click="onOpenOptionsModal(statusType)">
                                    <span class="text-muted">{{ statusType.namespace }}/{{
                                            statusType.identifier
                                        }}@{{ statusType.version }}</span>
                                    <template v-if="hasHiddenState(statusType)">
                                        <hr/>
                                        <span class="badge badge-light">Versteckte Zustände</span>
                                    </template>
                                </div>
                                <div class="card-footer d-flex justify-content-between px-2 py-1">
                                    <div class="text-nowrap">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm p-0 btn-light dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false" >
                                                <small class="text-muted">{{ widthLabel(width(statusType.size)) }}</small>
                                            </button>
                                            <div class="dropdown-menu">
                                                <button type="button" class="dropdown-item"
                                                        @click="changeWidth(statusType.id, 3)">1 / 4
                                                </button>
                                                <button type="button" class="dropdown-item"
                                                        @click="changeWidth(statusType.id, 4)">1 / 3
                                                </button>
                                                <button type="button" class="dropdown-item"
                                                        @click="changeWidth(statusType.id, 6)">1 / 2
                                                </button>
                                                <button type="button" class="dropdown-item"
                                                        @click="changeWidth(statusType.id, 8)">2 / 3
                                                </button>
                                                <button type="button" class="dropdown-item"
                                                        @click="changeWidth(statusType.id, 9)">3 / 4
                                                </button>
                                                <button type="button" class="dropdown-item"
                                                        @click="changeWidth(statusType.id,12)">1 / 1
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-nowrap" v-if="ui.editable">
                                        <button class="btn btn-sm btn-light p-0"
                                                @click="changeStatusTypeSort(statusType.id, 'left')">
                                            <span class="material-icons">arrow_left</span>
                                        </button>
                                        <button class="btn btn-sm btn-light p-0"
                                                @click="changeStatusTypeSort(statusType.id, 'right')">
                                            <span class="material-icons">arrow_right</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div v-else class="row">
            <div class="col">
                <span class="text-muted">Erstellen Sie zunächst bei <a :href="ui.urls.develop"><span
                    class="material-icons">device_hub</span> "Regeln & Daten"</a> einen Status: Rechtsklick auf die Regelwerk-Fläche.</span>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';
import utils from '../../config-utils';
import StatusTypeOptionsModal from "./StatusTypeOptionsModal";
import LoadingSeparator from "./LoadingSeparator";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        StatusTypeOptionsModal,
        LoadingSeparator
    },
    computed: {
        ...mapGetters([
            'definition',
            'ui'
        ]),
        statusTypes: function () {
            return [...this.definition.status_types] || [];
        },
        sortedStatusTypes: function () {
            return this.statusTypes.sort((a, b) => (a.sort > b.sort) ? 1 : ((b.sort > a.sort) ? -1 : 0));
        },
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        save(statusType) {
            this.patchDefinition('UpdateStatusType', statusType).catch(() => {
            });
        },
        toComponentName(namespace, identifier, version) {
            let versionIdentifier = version.replaceAll('.', '_');
            return namespace + '-statustype-' + identifier + '-v' + versionIdentifier + '-configuration-template';
        },
        width(size) {
            return +size.split('x')[0];
        },
        height(size) {
            return +size.split('x')[1];
        },
        onOpenOptionsModal(statusType) {
            this.openModal({
                componentName: 'StatusTypeOptionsModal',
                data: {
                    statusType: statusType
                }
            });
        },
        hasHiddenState(statusType) {
            return statusType.states.length > 0 && statusType.states.filter(ele => ele.hidden).length;
        },
        changeStatusTypeSort(statusTypeId, direction) {
            let statusTypes = [...this.statusTypes];
            let statusType = this.statusTypes.find(ele => ele.id === statusTypeId);
            let currentSort = statusType.sort;
            let maxSort = Math.max(...statusTypes.map(ele => ele.sort));
            let targetSort = direction === 'left' ? currentSort - 1 : currentSort + 1;
            let indexOfSwappingElement = statusTypes.findIndex(ele => ele.sort === targetSort);

            // Wenn bereits aussen und dann weiter nach aussen sortiert werden möchte
            if ((direction === 'left' && currentSort === 0) || (direction === 'right' && currentSort === maxSort)) {
                return;
            }

            // Spalte mit der Zielsortierung aktualisieren
            statusTypes = statusTypes.map((ele, index) => {
                if (index === indexOfSwappingElement) {
                    return {
                        ...ele,
                        sort: currentSort
                    };
                }
                return ele;
            });
            statusTypes[indexOfSwappingElement] = {
                ...statusTypes[indexOfSwappingElement],
                sort: currentSort
            };

            // Zu ändernde Spalte mit der neuen Sortierung aktualisieren
            statusTypes = statusTypes.map(ele => {
                if (ele.id === statusTypeId) {
                    return {
                        ...ele,
                        sort: targetSort
                    };
                }
                return ele;
            });

            this.patchDefinition('UpdateStatusTypes', {items: statusTypes}).catch(() => {
            });
        },
        changeWidth(statusTypeId, w) {
            let statusType = this.statusTypes.find(ele => ele.id === statusTypeId);

            if (!statusType) {
                return;
            }

            statusType = {
                ...statusType,
                size: w + 'x' + this.height(statusType.size)
            };

            this.save(statusType);
        },
        widthLabel(width) {
            let labels = {
                1: '1/12',
                2: '1/6',
                3: '1/4',
                4: '1/3',
                5: '5/12',
                6: '1/2',
                7: '7/12',
                8: '2/3',
                9: '3/4',
                10: '5/6',
                11: '11/12',
                12: '1/1'
            };

            return labels[width] || '?/?';
        },
    },
    watch: {
        data: {
            handler: function () {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    }
};
</script>

<style scoped>

.dropdown-toggle::after {
    content: none;
}

</style>
