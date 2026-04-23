<template>
    <div>
        <div class="modal-header py-2">
            <h5 class="modal-title" id="exampleModalLabel">
                <button class="btn btn-sm btn-primary mr-2" @click="$emit('navigation-change', 'Tasks')">
                    <span class="material-icons">keyboard_backspace</span>
                </button>
                <span>{{ addMode ? 'Aufgabe hinzufügen' : 'Aufgabe bearbeiten' }}</span>
            </h5>
            <button type="button" class="close" aria-label="Close" @click="$emit('cancel')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body py-2">
            <div class="form-group input-group-sm mb-3">
                <label class="mb-0" for="identifier">Identifier</label>
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" maxlength="40" required v-model="task.identifier" :readonly="!ui.editable">
                    <div class="input-group-append" v-if="ui.editable">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                data-toggle="dropdown" aria-expanded="false">Demo
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" type="button" @click="task.identifier = 'demo_aufgabe_1'">demo_aufgabe_1</button>
                            <button class="dropdown-item" type="button" @click="task.identifier = 'demo_aufgabe_2'">demo_aufgabe_2</button>
                            <button class="dropdown-item" type="button" @click="task.identifier = 'demo_aufgabe_3'">demo_aufgabe_3</button>
                        </div>
                    </div>
                </div>
                <div v-for="error in (ui.validationErrors.identifier || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group input-group-sm mb-3">
                <label class="mb-0" for="user">Bot</label>
                <select class="form-control form-control-sm" v-model="task.user">
                    <option value="">Bitte wählen...</option>
                    <option v-for="bot in blueprint.bots" :value="'user|' + bot.aliases[0]">{{ bot.first_name }}
                    </option>
                </select>
                <div v-for="error in (ui.validationErrors.user || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
        </div>
        <ModalFooter @save="onSave"
                     @cancel="onCancel"
                     :ui="ui"
                     :cancel-label="'Zurück'"
                     :save-label="addMode ? 'Hinzufügen' : 'Übernehmen'"
        />
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {v4 as uuidv4} from 'uuid';
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../../store/develop-and-config";
import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import CodeEditor from "../CodeEditor";

export default {
    components: {
        CodeEditor,
        ModalHeader,
        ModalFooter
    },
    props: {
        environment: Object,
        processVersionId: String,
        data: {
            required: true,
            default: null
        }
    },
    data() {
        let data = this.data;
        let addMode = this.data === null;
        let id = uuidv4();

        if (!this.data) {
            data = {
                id: id,
                identifier: '',
                user: '',
            };
        }

        return {
            task: {...data},
            addMode: addMode
        };
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
        blueprint() {
            return this.environment.blueprint;
        },
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
            let method = this.addMode ? 'StoreTask' : 'UpdateTask';
            this.patchBlueprint(method, this.task).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
                this.$emit('navigation-change', 'Tasks');
            });
        },
        onCancel() {
            this.clearError();
            this.$emit('navigation-change', 'Tasks');
        },
        navigationChange(to, task) {
            this.$emit('navigation-change', to, task);
        }
    },
    watch: {
        task: {
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
