<template>
    <div class="row">
        <div v-if="groupAliasAlreadyExists" class="col-12">
            <div class="alert alert-warning">
                <b>Hinweis:</b> Der Gruppen-Alias wurde bereits gewählt.
            </div>
        </div>
        <div v-if="groupAliasEmpty" class="col-12">
            <div class="alert alert-warning">
                <b>Hinweis:</b> Bitte einen Gruppen-Alias angeben.
            </div>
        </div>
        <div class="col-12">
            <div class="form-group mb-3">
                <hr class="my-3">
                <label for="groupSelection">Gruppen
                    <span v-if="groups.length" class="badge badge-light">{{ groups.length }}</span>
                </label>
                <div id="groupSelection">
                    <template v-for="(group, index) in groups">
                        <div v-if="ui.editable" class="row mb-1">
                            <div class="col-5">
                                <span>{{ group.name }}</span>
                            </div>
                            <div class="col-5">
                                <span v-if="group.roles?.length">{{ group.roles.join(', ') }}</span>
                                <span v-else class="text-muted">
                                    <i>Mitglieder aller Gruppen-Rollen</i>
                                </span>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-sm btn-outline-danger rounded-left ml-1"
                                        @click="onDeleteParameter(index)">
                                    <span class="material-icons">delete</span>
                                </button>
                            </div>
                        </div>
                    </template>
                    <div v-if="ui.editable" class="row mt-3">
                        <div class="col-5">
                            <input v-model="newGroupAlias" aria-label="Gruppen-Alias"
                                   class="form-control form-control-sm" placeholder="Gruppen-Alias..."/>
                        </div>
                        <div class="col-5">
                            <input v-model="newRoleAlias" aria-label="Rollen-Alias" class="form-control form-control-sm"
                                   placeholder="Rollen-Aliase..."/>
                            <small class="text-muted">Optional, kommasepariert</small>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-sm btn-outline-success rounded-left ml-1" @click="onAddParameter">
                                <span class="material-icons">add</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from "vuex";
import utils from "../../../../config-utils";
import {reduxActions} from "../../../../store/develop-and-config";
import DropdownSelector from "../../../utils/DropdownSelector.vue";
import OptionBadgesWithText from "../../../utils/OptionBadgesWithText.vue";

export default {
    components: {
        OptionBadgesWithText,
        DropdownSelector
    },
    props: {
        supportData: Object | null,
        data: Object
    },
    data() {
        return {
            groups: [...this.data.source.items],
            newGroupAlias: '',
            newRoleAlias: '',
            groupAliasEmpty: false,
            groupAliasAlreadyExists: false
        };
    },
    computed: {
        ...mapGetters([
            'ui',
        ])
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onAddParameter() {
            this.groupAliasEmpty = false;
            this.groupAliasAlreadyExists = false;

            if (!this.newGroupAlias.trim()) {
                this.groupAliasEmpty = true;
                return;
            }

            if (this.groups.some(group => group.name === this.newGroupAlias.trim())) {
                this.groupAliasAlreadyExists = true;
                return;
            }

            const newRoles = this.newRoleAlias.split(',').map(item => item.trim()).filter(item => item !== '');

            this.groups.push({
                name: this.newGroupAlias.trim(),
                roles: newRoles,
            });

            this.$emit('update-source', 'items', this.groups);

            this.newGroupAlias = '';
            this.newRoleAlias = '';
        },
        onDeleteParameter(index) {
            this.groups.splice(index, 1);

            this.$emit('update-source', 'items', this.groups);
        },
    }
};
</script>