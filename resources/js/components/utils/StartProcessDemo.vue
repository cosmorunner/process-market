<template>
    <div>
        <form method="POST" :action="endpoint" @submit="start" class="mt-2">
            <input type="hidden" name="ref" :value="referenceUrl"/>
            <input type="hidden" name="organisation_id" :value="organisationId"/>
            <input type="hidden" name="license_id" :value="licenseId"/>
            <input type="hidden" name="_token" :value="csrf"/>
            <div class="form-group input-group-sm mb-3" v-if="environments.length">
                <label class="mb-0" for="environment_id">Simulations-Umgebung</label>
                <select class="form-control" id="environment_id" name="environment_id" v-model="environmentId"
                        @change="onChangeEnvironment">
                    <option value="">Keine Umgebung</option>
                    <option :value="environment.id" v-for="environment in environments">
                        {{ environment.name }}
                    </option>
                </select>
                <div v-for="error in (errors.environment_id || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group input-group-sm mb-3">
                <label class="mb-0" for="user_email">Benutzer</label>
                <select class="form-control" id="user_email" name="user_email" v-model="userEmail">
                    <option value="demo@example.com">Demo Benutzer</option>
                    <option :value="user.email" v-for="user in environmentUsers">
                        {{ user.first_name }} {{ user.last_name }}
                    </option>
                </select>
                <div v-for="error in (errors.user_email || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group input-group-sm mb-3"
                 v-if="!selectedEnvironment || !selectedEnvironment.initial_action_type_id">
                <label class="mb-0" for="role_id">Rolle</label>
                <select class="form-control" id="role_id" name="role_id">
                    <option :value="''">Keine Rolle</option>
                    <option :selected="role.id === defaultRoleId" :value="role.id" v-for="role in roles">
                        {{ role.name + (role.id === defaultRoleId ? ' - Standard-Rolle' : '') }}
                    </option>
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-success float-right" v-if="!starting">
                <span>Starten</span>
            </button>
            <button type="button" :class="starting ? 'd-inline-block' : 'd-none'"
                    class="btn btn-sm btn-link float-right">
                <img src="/img/loading.gif" alt="Loading" width="18" height="18"/>
            </button>

        </form>
    </div>
</template>
<script>

export default {
    props: {
        endpoint: String,
        csrf: String,
        referenceUrl: String,
        roles: Array,
        defaultRoleId: String | null,
        organisationId: String | null,
        licenseId: String | null,
        initialAction: String,
        environments: Array,
        errors: Array | Object
    },
    data() {
        let defaultEnvironment = this.environments.find(ele => ele.default);
        let environmentId = defaultEnvironment.id || '';
        let userEmail = 'demo@example.com';

        if (defaultEnvironment && defaultEnvironment.default_user) {
            let user = defaultEnvironment.blueprint.users.find(ele => ele.id === defaultEnvironment.default_user);

            if (user) {
                userEmail = user.email;
            }
        }

        return {
            role: this.defaultRoleId,
            starting: false,
            environmentId: environmentId,
            userEmail: userEmail,
            context: ''
        };
    },
    computed: {
        selectedEnvironment() {
            if (!this.environmentId) {
                return null;
            }

            return this.environments.find(ele => ele.id === this.environmentId);
        },
        environmentUsers() {
            if (!this.environmentId) {
                return [];
            }

            let environment = this.environments.find(ele => ele.id === this.environmentId);

            return environment.blueprint.users;
        }
    },
    methods: {
        start(e) {
            e.preventDefault();
            this.starting = true;
            e.target.submit();
        },
        onChangeEnvironment() {
            if (!this.environmentId) {
                this.userEmail = 'demo@example.com';
            }

            let environment = this.environments.find(ele => ele.id === this.environmentId);
            let defaultUser = this.environmentUsers.find(ele => ele.id === environment.default_user);

            if (defaultUser) {
                this.userEmail = defaultUser.email;
            }
            else {
                this.userEmail = 'demo@example.com';
            }
        }
    },
    watch: {
        environmentId: function (newVal) {
            if (!newVal) {
                this.context = '';
            }
        }
    }
};
</script>
