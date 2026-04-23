<template>
    <div>
        <div class="d-flex justify-content-between border-bottom align-content-center">
            <nav>
                <div class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link p-2 active mouse-pointer" v-on:click="clearElementDetailView" href="#">
                        <span class="material-icons text-white">arrow_back</span>
                    </a>
                    <span class="nav-item nav-link p-2">
                        <span>{{ statusType.name.length >= 30 ? statusType.name.substring(0, 30) + '...' : statusType.name  }}</span>
                        <span class="badge badge-primary" v-if="isSmart">Smart-Status</span>
                    </span>
                </div>
            </nav>
            <div class="d-flex align-content-center pr-2 py-1" v-if="ui.editable">
                <button class="btn btn-sm btn-light" @click="onEditStatusType">
                    <span class="material-icons text-warning">edit</span>
                </button>
            </div>
        </div>
        <div class="detail-content panel-content-max-vh overflow-auto">
            <StatusType :status-type="statusType" :hide-footer="true" :hide-header="true" :editable="ui.editable"/>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import StatusType from './StatusType';

export default {
    components: {
        StatusType
    },
    props: {
        statusTypeId: String
    },
    computed: {
        ...mapGetters([
            'action_types',
            'status_types',
            'ui',
            'definition'
        ]),
        statusType() {
            return this.status_types.find(ele => ele.id === this.statusTypeId);
        },
        isSmart(){
            if(this.statusType) {
                return Object.keys(this.statusType.smart || {}).length
            }

            return false;
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onEditStatusType() {
            this.openModal({
                componentName: 'StatusTypeModal',
                data: {
                    position: null,
                    statusTypeId: this.statusType.id
                }
            });
        }
    },
};
</script>
