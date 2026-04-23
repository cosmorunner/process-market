<template>
    <div>
        <LoadingSeparator :ui="ui" :clear-error="clearError"/>
        <div class="row">
            <div class="col">
                <div class="row justify-content-between">
                    <div class="col-3">
                        <button class="btn btn-primary btn-sm mb-2" @click="openAddTemplateModal" v-if="ui.editable">
                            Vorlage anlegen
                        </button>
                        <div class="btn-group mb-2" role="group" aria-label="Button group with nested dropdown" v-if="ui.editable && showPasteTemplateButton">
                            <button class="btn btn-sm btn-warning"
                                    @click="pasteTemplate">
                                Einfügen
                            </button>
                            <button class="btn btn-sm btn-warning" @click="clearCopyElement">
                                <span class="material-icons">close</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-3 d-flex">
                        <div class="input-group input-group-sm max-width-250" style="height: 29px">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <span class="material-icons">search</span>
                                </span>
                            </div>
                            <input type="text" placeholder="Suche..." aria-label="Search"
                                   aria-describedby="search-button" class="form-control" v-model="search">
                            <div class="input-group-append" v-if="search.trim()">
                                <button type="button" class="btn btn-outline-danger" @click="search = ''"><span
                                    class="material-icons">clear</span>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex">
                            <Docs class="mr-2 ml-2" article="templates"/>
                        </div>
                    </div>
                </div>
                <div class="row processors mb-2" v-for="template in sortedTemplates">
                    <div class="col">
                        <div class="rounded-0 card h-100">
                            <div
                                class="card-header border-bottom-0 px-2 py-1 d-flex justify-content-between border-primary hover-pointer">
                                <span class="flex-grow-1" @click="onOpenEditModal(template)">
                                    <span class="text-primary text-truncate disable-user-select">{{
                                            template.name
                                        }}</span>
                                    <span :class="'d-inline-block p-1 badge badge-' + templateTypeColor(template)">{{
                                            templateTypeLabel(template)
                                        }}</span>
                                </span>
                                <div v-if="ui.editable">
                                    <button class="btn btn-sm btn-light text-primary p-0 px-1 mr-1"
                                            @click="copyTemplate(template)">
                                        <span class="material-icons">content_copy</span>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger p-0 px-1"
                                            @click="deleteTemplate(template.id)">
                                        <span class="material-icons">close</span>
                                    </button>
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
import TemplateModal from "./TemplateModal";
import LoadingSeparator from "./LoadingSeparator";
import OptionBadges from "../utils/OptionBadges";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        OptionBadges,
        LoadingSeparator
    },
    data() {
        return {
            search: ''
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'templates'
        ]),
        sortedTemplates() {
            let sorted = [...this.templates].sort((a, b) => a.name.toLowerCase() > b.name.toLowerCase() ? 1 : -1);
            let search = this.search.trim();

            if (!search) {
                return sorted;
            }

            return sorted.filter(ele => ele.name.toLowerCase().includes(search.toLowerCase()));
        },
        showPasteTemplateButton() {
            return this.getCopyElement()?.name === 'template';
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        openAddTemplateModal() {
            this.openModal({
                componentName: 'TemplateModal',
                data: {
                    template: null
                }
            });
        },
        onOpenEditModal(template) {
            this.openModal({
                componentName: 'TemplateModal',
                data: {
                    template
                }
            });
        },
        deleteTemplate(id) {
            this.patchDefinition('DeleteTemplate', {id}).catch(() => {
            });
        },
        copyTemplate(template) {
            this.saveCopyElement('template', template);
        },
        templateTypeColor(template) {
            if (template.type === 'html') {
                return 'info';
            }
            if (template.type === 'custom_logic') {
                return 'secondary';
            }
            if (template.type === 'mustache_list_column') {
                return 'warning';
            }

            return 'info';
        },
        templateTypeLabel(template) {
            if (template.type === 'html') {
                return 'HTML';
            }
            if (template.type === 'custom_logic') {
                return 'EIGENE LOGIK';
            }
            if (template.type === 'mustache_list_column') {
                return 'HTML - MUSTACHE JS';
            }

            return '';
        },
        pasteTemplate() {
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
