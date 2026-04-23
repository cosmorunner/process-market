<template>
    <div>
        <div class="d-flex justify-content-between align-content-center">
            <nav>
                <div class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link p-2 active mouse-pointer" v-on:click="clearElementDetails">
                        <span class="material-icons text-white">arrow_back</span>
                    </a>
                    <a class="nav-item nav-link p-2">
                        Start-Situation
                    </a>
                </div>
            </nav>
        </div>
        <div class="detail-content panel-content-max-vh overflow-auto">
            <template v-for="statusType in this.status_types">
                <StatusType :status-type="statusType" :hide-footer="true" :only-initial="true"/>
            </template>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import StatusType from "./StatusType";

export default {
    components: {
        StatusType
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
    },
    computed: {
        ...mapGetters([
            'status_types',
        ])
    },
};
</script>
