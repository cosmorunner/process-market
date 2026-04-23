<template>
    <div class="form-group m-2">
        <span class="text-muted" v-if="!roles.length">Der gewählte Prozess hat keine Rollen.</span>
        <div class="mb-3" v-if="roles.length">
            <span>Benutzer-Zugriffe</span>
            <div class="input-group input-group-sm mb-2 mt-1">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" aria-label="" :checked="accesses['demo@example.com']"
                               value="demo@example.com" @change="toggleAccess">
                    </div>
                </div>
                <div class="input-group-prepend">
                    <label class="input-group-text">Demo-Benutzer</label>
                </div>
                <select class="custom-select" v-model="selectedRoles['demo@example.com']"
                        @change="toggleAccessRole('demo@example.com')" :disabled="!editable">
                    <option v-for="role in roles" :value="role.id">{{ role.name }}</option>
                </select>
            </div>
            <div class="input-group input-group-sm mb-2 mt-1" v-for="user in users">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" aria-label="" :checked="accesses[user.id]" :value="user.id"
                               @change="toggleAccess">
                    </div>
                </div>
                <div class="input-group-prepend">
                    <label class="input-group-text">{{ user.first_name }} {{ user.last_name }}</label>
                </div>
                <select class="custom-select" v-model="selectedRoles[user.id]" @change="toggleAccessRole(user.id)">
                    <option v-for="role in roles" :value="role.id">{{ role.name }}</option>
                </select>
            </div>
            <span>Gruppen-Zugriffe</span>
            <span class="text-muted d-block" v-if="!groups.length">Es wurden keine Gruppen zur Simulationsumgebung hinzugefügt.</span>
            <div class="input-group input-group-sm mb-2 mt-1" v-for="group in groups">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" aria-label="" :checked="accesses[group.id]" :value="group.id"
                               @change="toggleAccess">
                    </div>
                </div>
                <div class="input-group-prepend">
                    <label class="input-group-text">{{ group.name }}</label>
                </div>
                <select class="custom-select" v-model="selectedRoles[group.id]" @change="toggleAccessRole(group.id)">
                    <option v-for="role in roles" :value="role.id">{{ role.name }}</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        roles: Array,
        groups: Array,
        users: Array,
        accessesProp: Object | Array,
        editable: Boolean
    },
    data() {
        let selectedRoles = {};

        if (this.roles.length) {
            this.groups.forEach(group => selectedRoles[group.id] = this.accessesProp[group.id] || this.roles[0].id);
            this.users.forEach(user => selectedRoles[user.id] = this.accessesProp[user.id] || this.roles[0].id);
            selectedRoles['demo@example.com'] = this.accessesProp['demo@example.com'] || this.roles[0].id;
        }

        return {
            selectedRoles: selectedRoles
        };
    },
    computed: {
        accesses() {
            return this.accessesProp;
        }
    },
    methods: {
        updateAccessOption(groupId, roleId) {
            this.$emit('update-access-option', groupId, roleId);
        },
        toggleAccess(e) {
            if (e.target.checked) {
                this.$emit('update-access-option', e.target.value, this.selectedRoles[e.target.value]);
            }
            else {
                this.$emit('delete-access-option', e.target.value);
            }
        },
        toggleAccessRole(recipientIdentifier) {
            if (this.accesses[recipientIdentifier]) {
                this.$emit('update-access-option', recipientIdentifier, this.selectedRoles[recipientIdentifier]);
            }
        }
    },
    watch: {
        roles() {
            let selectedRoles = {};

            if (this.roles.length) {
                this.groups.forEach(group => selectedRoles[group.id] = this.accesses[group.id] || this.roles[0].id);
                this.users.forEach(user => selectedRoles[user.id] = this.accesses[user.id] || this.roles[0].id);
                selectedRoles['demo@example.com'] = this.accesses['demo@example.com'] || this.roles[0].id;
            }

            this.selectedRoles = selectedRoles;
        }
    }
};
</script>
