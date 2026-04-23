<template>
    <div class="card mb-0 rounded-0 border-left-0 border-right-0 border-bottom-0">
        <div class="card-header border-bottom-0 d-flex align-items-center bg-white justify-content-between px-2 py-1">
            <div>
                <button type="button" class="btn btn-link p-0 mr-2" v-if="!simulation.running"
                        @click="showRoleDetail(role.id)">{{ role.name }}
                </button>
                <span class="badge badge-secondary mr-2">{{ role.permissions.length }} Rechte</span>
                <span v-if="simulation.running">{{ role.name }}</span>
            </div>
            <div class="d-flex justify-content-end" v-if="ui.editable">
                <div>
                    <button class="btn btn-sm btn-light" @click="handleOpenModal(role)">
                        <span class="material-icons text-warning">edit</span>
                    </button>
                </div>
                <div class="dropdown d-inline-block ml-1">
                    <button class="btn btn-sm btn-light" type="button" id="actionDropDownButton" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <span class="material-icons">more_vert</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="actionDropDownButton">
                        <button v-on:click="onCopyRole(role.id)" class="dropdown-item" type="button">
                            Kopieren
                        </button>
                        <button @click="onDeleteRole(role.id)" class="dropdown-item text-danger" type="button">Löschen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';

export default {
    props: {
        role: Object
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        handleOpenModal(role) {
            this.openModal({
                componentName: 'RoleModal',
                data: {
                    role: role,
                    method: 'UpdateRole'
                }
            });
        }
    },
    computed: {
        ...mapGetters([
            'ui',
            'simulation'
        ])
    }
};
</script>