<template>
    <div class="bg-light p-1">
        <table class="table table-sm table-borderless m-0">
            <tbody>
            <tr class="d-flex">
                <td class="py-0 w-35 mb-0">
                    <span>{{ labelByColumn(whereInItem[0]) }}</span>
                </td>
                <td class="py-0 w-10 mb-0">
                    <span>in</span>
                </td>
                <td class="py-0 w-45 mb-0">{{whereInItem[1].join(', ')}}</td>
                <td class="py-0 w-10 mb-0">
                    <button class="btn btn-sm btn-light float-right" @click="$emit('delete-item', ruleIndex, index)" v-if="editable">
                        <span class="material-icons text-danger">close</span>
                    </button>
                    <button class="btn btn-sm btn-light float-right" v-else disabled>
                        <span class="material-icons">lock</span>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

export default {
    props: {
        allColumns: Array,
        connectingOperator: String,
        ruleIndex: Number,
        whereInItem: Array,
        editable: {
            default: true,
            type: Boolean
        }
    },
    methods: {
        labelByColumn(column) {
            let item = this.allColumns.find(ele => ele.column === column);

            if (item) {
                return item.label;
            }

            return column;
        }
    }
};
</script>
