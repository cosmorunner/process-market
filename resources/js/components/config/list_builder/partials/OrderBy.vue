<template>
    <div class="row mb-3">
        <div class="col-12">
                <span class="d-flex justify-content-between mb-2">
                    <span>
                        <span>Sortierung</span>
                        <span class="badge badge-light" v-if="orderBy.length > 0">{{ orderBy.length }}</span>
                    </span>
                </span>
            <template v-if="orderBy.length">
                <div class="p-1">
                    <table class="table table-sm bg-light table-borderless m-0 mb-2" v-for="(item, index) in orderBy">
                        <tbody>
                        <template>
                            <tr class="d-flex">
                                <td class="py-1 w-40 mb-0">
                                        <span :class="selectedColumnExists(item[0]) ? 'text-success' : 'text-danger'">
                                            <span>{{ labelByAlias(item[0]) }}</span>
                                        </span>
                                </td>
                                <td class="py-1 w-40 mb-0">{{ operatorLabels[item[1]] || '???' }}</td>
                                <td class="py-1 w-20 mb-0">
                                    <button class="btn btn-sm btn-light float-right" @click="deleteItem(index)" v-if="editable">
                                        <span class="material-icons text-danger">close</span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
            </template>

            <div class="input-group input-group-sm input-group-sm my-3" v-if="editable">
                <div class="input-group-prepend">
                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="orderByColumnAlias"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ labelByAlias(newItem[0]) || '#' }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown" aria-labelledby="orderByColumnAlias">
                        <button type="button" class="dropdown-item" v-for="item in usedColumnAliases"
                                @click="changeValue($event, 0, item.alias)">
                            <span class="text-muted">Datenfeld: </span>{{ aliasLabels[item.alias] || item.alias }}
                        </button>
                    </div>
                </div>
                <select class="custom-select" id="inputGroupSelect01" v-model="newItem[1]">
                    <option value="desc">Absteigend</option>
                    <option value="asc">Aufsteigend</option>
                </select>
                <div class="input-group-append">
                    <button class="btn btn-sm rounded-right btn-outline-success" @click="addItem">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        supportData: Object | null,
        aliasLabels: Object,
        data: Object,
        usedColumnAliases: Array | Object,
        editable: Boolean
    },
    data() {
        return {
            orderBy: this.data.source.orderBy || [],
            newItem: [
                '',
                'desc',
            ],
            operatorLabels: {
                'desc': 'Absteigend',
                'asc': 'Aufsteigend',
            }
        };
    },
    methods: {
        changeValue(e, index, value) {
            let newItem = [...this.newItem];

            newItem[index] = value || e.target.value;
            this.newItem = newItem;
        },
        addItem() {
            if (this.newItem[0] === '') {
                return;
            }

            this.orderBy = [...this.orderBy, this.newItem]

            // Reset
            this.newItem = [
                '',
                'desc',
            ];
        },
        labelByAlias(alias) {
            let aliasItem = this.supportData.allColumns.find(ele => ele.alias === alias);

            return aliasItem ? aliasItem.label : alias;
        },
        deleteItem(ruleIndex) {
            let orderBy = [...this.orderBy];
            this.orderBy = [...orderBy.filter((ele, index) => index !== ruleIndex)]
        },
        selectedColumnExists(alias){
            return this.usedColumnAliases.find(ele => ele.alias === alias)
        }
    },
    watch: {
        orderBy() {
            this.$emit('update-source', 'orderBy', [...this.orderBy]);
        }
    },
};
</script>
