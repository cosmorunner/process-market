<template>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="selectSelection">Attribute
                        <span v-if="select.length" class="badge badge-light">{{ select.length }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="select = []">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="selectSelection" v-model="select" size="5">
                    <option v-for="item in allAttributes" :value="{data: item.column, alias: item.alias}">
                        {{ item.label + ' - ' + item.alias }}
                    </option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../../../config-utils';
import {reduxActions} from '../../../../store/develop-and-config';

export default {
    props: {
        supportData: Object | null,
        data: Object
    },
    data() {
        return {
            select: this.data.source.select,
            context: this.data.source.context,
            relation: this.data.source.relation
        };
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
        allAttributes() {
            let prefix = this.context + '_' + this.relation

            return this.supportData.allColumns.filter(ele => ele.alias.startsWith(prefix))
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions)
    },
    watch: {
        select() {
            this.$emit('update-source', 'select', [
                ...this.select
            ]);
        },
    }
};
</script>

