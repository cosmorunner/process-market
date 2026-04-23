<template>
    <div>
        <LoadingSeparator :ui="ui" :clear-error="clearError"/>
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary btn-sm mb-2" @click="openAddModal" v-if="ui.editable">
                        Event anlegen
                    </button>
                    <Docs class="mr-2" article="events"/>
                </div>
                <div class="btn-group mb-2" role="group" aria-label="Button group with nested dropdown"
                     v-if="ui.editable && showPasteEventButton">
                    <button class="btn btn-sm btn-warning" @click="pasteEvent">
                        Einfügen
                    </button>
                    <button class="btn btn-sm btn-warning" @click="clearCopyElement">
                        <span class="material-icons">close</span>
                    </button>
                </div>
                <div class="row mb-2" v-for="event in sortedEvents">
                    <div class="col">
                        <div class="rounded-0 card h-100">
                            <div
                                class="card-header px-2 py-1 d-flex justify-content-between border-primary mouse-pointer">
                                <span class="text-primary flex-grow-1 text-truncate disable-user-select"
                                      @click="onOpenEditModal(event)">
                                    <span>{{ event.name }}</span>
                                </span>
                                <button class="btn btn-sm btn-light text-primary p-0 mr-2" @click="copyEvent(event)">
                                    <span class="material-icons">content_copy</span>
                                </button>
                                <button class="btn btn-sm btn-light text-danger p-0" @click="deleteEvent(event)"
                                        v-if="ui.editable">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                            <div class="card-body p-2 hover-pointer" @click="onOpenEditModal(event)">
                                <span class="text-muted d-block">{{ event.description || '&nbsp;' }}</span>
                                <span v-if="(event.data || []).length">
                                    <hr/>
                                    <template v-for="item in ([...event.data || []]).sort((a, b) => a.name > b.name)">
                                        <span class="d-block mb-1">
                                            <span>{{ item.name }}</span>
                                            <span>:</span>
                                            <span class="text-muted">{{ item.description }}</span>
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
            'events'
        ]),
        sortedEvents() {
            return [...this.events].sort((a, b) => a.name > b.name ? 1 : -1);
        },
        showPasteEventButton() {
            return this.getCopyElement()?.name === 'event';
        }
    },
    methods: {
        ...mapActions(reduxActions), ...utils,
        openAddModal() {
            this.openModal({
                componentName: 'EventModal',
                data: {
                    event: null
                }
            });
        },
        onOpenEditModal(event) {
            this.openModal({
                componentName: 'EventModal',
                data: {
                    event: event
                }
            });
        },
        deleteEvent(event) {
            this.patchDefinition('DeleteEvent', {
                id: event.id,
                name: event.name
            }).catch(() => {
            });
        },
        copyEvent(event) {
            this.saveCopyElement('event', event);
        },
        pasteEvent() {
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
