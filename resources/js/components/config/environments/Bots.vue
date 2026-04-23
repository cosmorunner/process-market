<template>
    <div>
        <ModalHeader :title="addMode ? 'Neue Umgebung' : 'Umgebung bearbeiten'" v-on="$listeners"/>
        <div class="modal-body py-2">
            <Navigation class="mb-4" :detail-component-name="detailComponentName" v-on="$listeners"/>
            <ul v-for="bot in blueprint.bots" class="list-group list-group-flush">
                <li class="list-group-item p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-1 p-2">
                                <span class="material-icons">smart_toy</span>
                            </div>
                            <div class="col-9 p-2">
                                <span>allisa/bot@1.0.0</span>
                                <span> - </span>
                                <span>{{ bot.first_name }}</span>
                                <small class="text-muted d-block">
                                    <template v-if="bot.aliases.length">
                                        <span class="d-inline-block">
                                            <span
                                                class="material-icons mr-1">local_offer</span><span>{{ bot.aliases.join() }}</span>
                                        </span>
                                    </template>
                                </small>
                            </div>
                            <div class="col-2 p-2">
                                <button class="btn btn-sm btn-light float-right" @click="onDeleteBot(bot.id)" v-if="ui.editable">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="row mt-2" v-if="ui.editable">
                <div class="col-5">Name</div>
                <div class="col-5">Alias</div>
                <div class="col-2"></div>
            </div>
            <div class="row" v-if="ui.editable">
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" v-model="newBot.first_name">
                    <div class="invalid-feedback d-block mt-0" v-for="error in [...ui.validationErrors.first_name || []]">
                        {{ error }}
                    </div>
                </div>
                <div class="col-5">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" v-model="newBot.aliases">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                    data-toggle="dropdown" aria-expanded="false">Demo
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" type="button" @click="newBot.aliases = 'demo_bot_1'">demo_bot_1</button>
                                <button class="dropdown-item" type="button" @click="newBot.aliases = 'demo_bot_2'">demo_bot_2</button>
                                <button class="dropdown-item" type="button" @click="newBot.aliases = 'demo_bot_3'">demo_bot_3</button>
                            </div>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block mt-0"
                         v-for="error in [...ui.validationErrors['aliases.0'] || []]">{{ error }}
                    </div>
                </div>
                <div class="col-2">
                    <button class="btn btn-sm btn-outline-primary" type="button" @click="onAddBot">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
        </div>
        <ModalFooter :ui="ui" :save-label="'Speichern'" v-on="$listeners"/>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Navigation from "./Navigation";
import ModalHeader from "../ModalHeader";
import ModalFooter from "../ModalFooter";
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
    data() {
        return {
            newBot: {
                first_name: '',
                aliases: ''
            }
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'graphs'
        ]),
        blueprint() {
            return this.environment.blueprint;
        },
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        navigationChange(to, process) {
            this.$emit('navigation-change', to, process);
        },
        onDeleteBot(id) {
            this.patchBlueprint('DeleteBot', {id}).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            });
        },
        onAddBot() {
            let bot = {
                ...this.newBot,
                aliases: [this.newBot.aliases]
            };

            this.patchBlueprint('StoreBot', bot).then((response) => {
                this.resetNewBot();
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            })
        },
        resetNewBot() {
            this.newBot = {
                first_name: '',
                last_name: '',
                aliases: ''
            };
        }
    },
    watch: {
        newBot: {
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
