<template>
    <div>
        <LoadingSeparator :ui="ui" :clear-error="clearError"/>
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary btn-sm mb-2" @click="openAddModal" v-if="ui.editable">
                        Listener anlegen
                </button>
                    <Docs class="mr-2" article="listeners"/>
                </div>
                <div class="row mb-2" v-for="listener in listeners">
                    <div class="col">
                        <div class="rounded-0 card h-100">
                            <div
                                class="card-header px-2 py-1 d-flex justify-content-between border-primary mouse-pointer">
                                <span class="text-primary flex-grow-1 text-truncate disable-user-select"
                                      @click="onOpenEditModal(listener)">
                                    <span>&nbsp;</span>
                                </span>
                                <button class="btn btn-sm btn-light text-danger p-0" @click="deleteListener(listener)"
                                        v-if="ui.editable">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                            <div class="card-body p-2 hover-pointer" @click="onOpenEditModal(listener)">
                                <span class="text-muted d-block mb-2"
                                      v-if="listener.description.trim().length">{{ listener.description }}</span>
                                <div class="mb-2">
                                    <span>Events:</span>
                                    <template v-for="event in listener.events">
                                        <OptionBadges :value="event" :manual-icon="'sell'" :manual-label="event"
                                                      :disable-tooltip="true"/>
                                    </template>
                                </div>
                                <ul class="list-group list-group-flush mb-0"
                                    v-if="typeof listener.relation_type === 'string'">
                                    <li class="list-group-item pl-0 pr-1 py-1 bg-transparent">
                                        <span>Verknüpfungstyp-Kondition:</span>
                                        <OptionBadges :value="listener.relation_type"/>
                                    </li>
                                </ul>
                                <template v-if="listener.conditions.length">
                                    <div class="pt-1">
                                        <span>Konditionen</span>
                                        <div class="mb-2">
                                            <ConditionsTable :conditions="[...listener.conditions]" :editable="false"/>
                                        </div>
                                    </div>
                                </template>
                                <div class="py-1" v-if="listener.type_options.action_type">
                                    <span>Aktion:</span>
                                    <OptionBadges :value="listener.type_options.action_type"/>
                                </div>
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
import ConditionsTable from "./partials/ConditionsTable";
import Template from "../../../plugins/Allisa/ActionTypeComponent/Form/v1_0_0/configuration/Template";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        Template,
        ConditionsTable,
        OptionBadges,
        LoadingSeparator
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
            'listeners'
        ])
    },
    methods: {
        ...mapActions(reduxActions), ...utils,
        openAddModal() {
            this.openModal({
                componentName: 'ListenerModal',
                data: {
                    listener: null
                }
            });
        },
        onOpenEditModal(listener) {
            this.openModal({
                componentName: 'ListenerModal',
                data: {
                    listener: listener
                }
            });
        },
        deleteListener(listener) {
            this.patchDefinition('DeleteListener', {
                id: listener.id
            }).catch(() => {
            });
        }
    },
};
</script>
