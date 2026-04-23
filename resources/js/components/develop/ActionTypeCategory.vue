<template>
    <div>
        <div class="p-2">
            <span class="font-weight-bold">{{ category.name }}</span>
        </div>
        <template v-for="actionType in category.action_types">
            <ActionType :action-type="actionTypeWithRules(actionType.id)"/>
        </template>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import utils from '../../develop-utils';
import ActionType from "./ActionType";

export default {
    components: {
        ActionType,
    },
    props: {
        category: Object
    },
    computed: {
        ...mapGetters([
            'simulation',
            'action_types'
        ])
    },
    methods: {
        ...utils,
        actionTypeWithRules(id) {
            for (let i = 0; i < this.action_types.length; i++) {
                if (this.action_types[i].id === id) {
                    return this.action_types[i];
                }
            }
            return null;
        }
    }
};
</script>
