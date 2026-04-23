<template>
    <div>
        <div class="d-flex justify-content-between border-bottom align-content-center">
            <nav>
                <div class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link p-2 active mouse-pointer" v-on:click="showStatusTypeDetail(statusType.id)" href="#">
                        <span class="material-icons text-white">arrow_back</span>
                    </a>
                    <span class="nav-item nav-link p-2">
                        {{ statusType.name }}
                    </span>
                </div>
            </nav>
            <div class="dropdown px-2" v-if="ui.editable">
                <button class="btn btn-sm h-100" type="button" id="actionDropDownButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <span class="material-icons">more_vert</span>
                </button>
                <div class="dropdown-menu" aria-labelledby="actionDropDownButton">
                    <button v-on:click="onEditState" class="dropdown-item" type="button">Bearbeiten</button>
                    <div class="dropdown-divider"></div>
                    <button v-on:click="onDeleteState(state.status_type_id, state.id)" class="dropdown-item text-danger"
                       type="button">Löschen</button>
                </div>
            </div>
        </div>
        <div class="detail-content panel-content-max-vh overflow-auto">
            <StatesTableSimple :states="[state]" />
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import StatusType from "./StatusType";
import StatesTableSimple from "./StatesTableSimple";

export default {
    components: {
        StatesTableSimple,
        StatusType
    },
    props: {
        stateId: String,
        lastItem: {
            default: false
        }
    },
    computed: {
        ...mapGetters([
            'status_types',
            'action_types',
            'ui',
            'definition',
            'states'
        ]),
        state: function () {
            return this.states.find(ele => ele.id === this.stateId);
        },
        statusType: function () {
            return this.status_types.find(ele => ele.id === this.state.status_type_id);
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onEditState() {
            this.openModal({
                componentName: 'StateModal',
                data: {
                    position: null,
                    stateId: this.state.id,
                    statusTypeId: this.state.status_type_id
                }
            });
        }
    },
};
</script>
