<template>
    <div class="row" v-if="actionType">
        <div class="col">ActionType Settings</div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';
import utils from '../../config-utils';

export default {
    props: {
        actionType: Object,
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
        ]),
    },
    methods: {
        ...mapActions(reduxActions),
        ...utils,
    }
};
</script>
