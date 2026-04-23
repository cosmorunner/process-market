<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <ModalHeader :title="'Demo starten'" docs-article="simulation"/>
            <div class="modal-body py-2">
                <div class="row d-flex">
                    <div class="col">
                        <form>
                            <div class="form-group mb-2" v-if="environments.length">
                                <label for="environment">Umgebung</label>
                                <select class="form-control form-control-sm" id="environment"
                                        v-model="data.environment_id" @change="onChangeEnvironment">
                                    <option value="">Keine Umgebung</option>
                                    <option :value="environment.id" v-for="environment in environments">
                                        {{ environment.name }} {{ environment.default ? ' (Standard-Umgebung)' : '' }}
                                    </option>
                                </select>
                                <div v-for="error in (ui.validationErrors.category_id || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="role">Benutzer</label>
                                <select class="form-control form-control-sm" id="user_email" v-model="data.user_email">
                                    <option value="demo@example.com">Demo Benutzer</option>
                                    <option :value="user.email" v-for="user in environmentUsers">
                                        {{ user.first_name }} {{ user.last_name }}
                                    </option>
                                </select>
                                <div v-for="error in (ui.validationErrors.user_email || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2" v-if="!selectedEnvironment || !selectedEnvironment.initial_action_type_id">
                                <label for="role">Rolle</label>
                                <select class="form-control form-control-sm" id="role" v-model="data.role_id">
                                    <option :value="role.id" v-for="role in roles">{{ role.name }} {{
                                            role.id === definition.default_role_id ? ' (Standard-Rolle)' : ''
                                        }}
                                    </option>
                                </select>
                                <div v-for="error in (ui.validationErrors.category_id || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <ModalFooter :ui="uiWithStarting" v-on="$listeners" :on-save="onSave" :save-label="'Starten'"
                         @cancel="onCancel"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";

export default {
    components: {
        ModalHeader,
        ModalFooter
    },
    data() {
        return {
            data: {
                role_id: null,
                user_email: 'demo@example.com',
                environment_id: '',
                organisation_id: null,
            },
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'environments',
            'roles',
            'definition',
            'simulation',
            'active_action_type_ids',
            'inaccessible_action_type_ids',
            'active_state_ids'
        ]),
        selectedEnvironment() {
            if (!this.data.environment_id) {
                return null;
            }

            return this.environments.find(ele => ele.id === this.data.environment_id);
        },
        uiWithStarting() {
            return {
                ...this.ui,
                loading: this.simulation.starting,
                editable: true
            };
        },
        environmentUsers() {
            if (!this.data.environment_id) {
                return [];
            }
            let environment = this.environments.find(ele => ele.id === this.data.environment_id);

            return environment.blueprint.users;
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let promise = this.handleStartSim({}, {
                role_id: this.data.role_id,
                environment_id: this.data.environment_id,
                organisation_id: this.data.organisation_id,
                user_email: this.data.user_email
            });

            if (typeof promise === 'object') {
                Promise.resolve(promise).then(this.closeModal);
            }
        },
        onCancel() {
            this.clearError();
        },
        onChangeEnvironment() {
            if (!this.data.environment_id) {
                this.data.user_email = 'demo@example.com';
            }

            let defaultUser = this.environmentUsers.find(ele => ele.id === this.selectedEnvironment.default_user);

            if (defaultUser) {
                this.data.user_email = defaultUser.email;
            }
            else {
                this.data.user_email = 'demo@example.com';
            }
        }
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
    },
    mounted() {
        if (this.definition.default_role_id) {
            this.data.role_id = this.definition.default_role_id;
        }
        if (this.roles.length) {
            this.data.role_id = this.roles[0].id;
        }
        if (this.environments.length) {
            let defaultEnvironment = this.environments.find(ele => ele.default);
            this.data.environment_id = defaultEnvironment ? defaultEnvironment.id : '';

            if (defaultEnvironment && defaultEnvironment.default_user) {
                let user = defaultEnvironment.blueprint.users.find(ele => ele.id === defaultEnvironment.default_user);

                if (user) {
                    this.data.user_email = user.email;
                }
            }
        }

        // Gegebenenfalls Organisation als Kontext angeben.
        this.data.organisation_id = this.ui.modal.data.organisationId;
    }
};
</script>
