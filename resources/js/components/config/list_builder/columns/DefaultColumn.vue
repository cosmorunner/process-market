<template>
    <div>
        <div class="text-nowrap">
            <span
                :class="'text-truncate d-block ' + (usedColumnAliases.map(ele => ele.alias).includes(column.data) || !column.data ? 'text-success' : 'text-danger')">
            <span class="material-icons">looks_one</span> {{ aliasLabels[column.data] || column.data || ' - ' }}
        </span>
        </div>
        <div class="text-nowrap" v-if="column.alias">
            <span
                :class="'text-truncate d-block ' + (usedColumnAliases.map(ele => ele.alias).includes(column.alias) || !column.alias ? 'text-success' : 'text-danger')"><span
                class="material-icons">looks_two</span> {{ aliasLabels[column.alias] || column.alias || ' - ' }}</span>
        </div>
        <span class="text-muted d-block" v-if="column.searchable"><span class="material-icons">search</span> Durchsuchbar</span>
        <span class="text-muted d-block" v-if="column.sortable"><span class="material-icons">sort_by_alpha</span> Sortierbar</span>
        <div v-if="column.color && column.color.length">
            <span class="text-muted">
                <span class="material-icons">palette</span>
                <span v-if="column.color.startsWith('#')" :style="'color:' + (column.color)">{{ column.color }} <span
                    class="material-icons">lens</span></span>
                <span v-if="!column.color.startsWith('#') && usedColumnAliases.map(ele => ele.alias).includes(column.color)"
                      class="text-success">{{ aliasLabels[column.color] || column.color }}</span>
                <span v-if="!column.color.startsWith('#') && !usedColumnAliases.map(ele => ele.alias).includes(column.color)"
                      class="text-danger">{{ aliasLabels[column.color] || column.color }}</span>
            </span>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        column: Object,
        usedColumnAliases: Array | Object,
        aliasLabels: Object
    }
};
</script>
