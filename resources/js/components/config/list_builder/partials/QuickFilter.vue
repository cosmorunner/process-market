<template>
    <div>
        <div class="card mb-2 mt-1" v-for="(filter, index) in quickFilter">
            <div class="card-header p-2">
                <div class="d-flex justify-content-between">
                    <div class="d-flex align-content-center">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control form-control-sm" :readonly="!editable" :value="filter.label" @change="updateLabel($event.target.value, index)"/>
                        </div>
                        <span class="text-muted ml-1">{{ filter.name }}</span>
                    </div>
                    <button class="btn btn-outline-danger btn-sm" @click="deleteFilter(filter.name)" v-if="editable">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>
            <div class="card-body p-2">
                <div class="d-flex justify-content-between mb-2" v-for="(item, index) in filter.where">
                    <div class="input-group input-group-sm input-group-btn">
                        <div class="input-group-append">
                            <button class="btn btn-sm dropdown-toggle rounded-left btn-outline-dark" type="button"
                                    :id="'left-' + index" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false" :disabled="!editable">
                                {{ aliasLabels[usedColumnAliases.find(ele => ele.column === item[0]) ? usedColumnAliases.find(ele => ele.column === item[0]).alias : null] || '#' }}
                            </button>
                            <div class="dropdown-menu dropdown-max-height-250" :aria-labelledby="'left-' + index">
                                <button type="button" v-for="item in usedColumnAliases" class="dropdown-item"
                                   @click="updateWhere(filter.name, index, 0, item.column)">{{ aliasLabels[item.alias] || item.alias }}</button>
                            </div>
                        </div>
                        <div class="input-group-append">
                            <button :class="'btn btn-sm dropdown-toggle btn-outline-primary'" type="button"
                                    :id="'operator-' + index" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false" :disabled="!editable">
                                {{ operatorToLocal(item[1]) }}
                            </button>
                            <div class="dropdown-menu" :aria-labelledby="'operator-' + index">
                                <button type="button" class="dropdown-item" @click="updateWhere(filter.name, index, 1, '=')">Ist gleich</button>
                                <button type="button" class="dropdown-item" @click="updateWhere(filter.name, index, 1, '!=')">Ist nicht gleich</button>
                                <button type="button" class="dropdown-item" @click="updateWhere(filter.name, index, 1, '>')">Ist größer als</button>
                                <button type="button" class="dropdown-item" @click="updateWhere(filter.name, index, 1, '>=')">Ist größer oder gleich</button>
                                <button type="button" class="dropdown-item" @click="updateWhere(filter.name, index, 1, '<=')">Ist kleiner oder gleich</button>
                                <button type="button" class="dropdown-item" @click="updateWhere(filter.name, index, 1, '<')">Ist kleiner als</button>
                            </div>
                        </div>
                        <div class="input-group-append">
                            <button :class="'btn btn-sm dropdown-toggle btn-outline-dark'" type="button"
                                    :disabled="!editable" :id="'right-' + index" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ aliasLabels[usedColumnAliases.find(ele => ele.column === item[2]) ? usedColumnAliases.find(ele => ele.column === item[2]).alias : null] || '#' }}
                            </button>
                            <div class="dropdown-menu dropdown-max-height-250" :aria-labelledby="'right-' + index">
                                <button type="button" class="dropdown-item" @click="updateWhere(filter.name, index, 2, '')">Eigener Wert</button>
                                <div class="dropdown-divider"></div>
                                <button type="button" v-for="item in usedColumnAliases" class="dropdown-item"
                                   @click="updateWhere(filter.name, index, 2, item.column)">{{ aliasLabels[item.alias] || item.alias }}</button>
                            </div>
                        </div>
                        <input v-if="!usedColumnAliases.map(ele => ele.column).includes(item[2])" :readonly="!editable" type="text" class="form-control" v-model="item[2]" @change="updateQuickFilter"
                        >
                    </div>
                    <button class="btn btn-sm btn-outline-light py-0 ml-2" @click="deleteWhere(filter.name, index)" v-if="editable">
                        <span class="material-icons text-danger">close</span>
                    </button>
                </div>
                <div class="d-flex justify-content-start" v-if="editable">
                    <button class="btn btn-sm btn-outline-primary" @click="addWhere(filter.name)">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-start mt-2" @click="addFilter" v-if="editable">
            <button class="btn btn-sm btn-outline-primary"><span class="material-icons">add</span></button>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        listConfig: Object,
        usedColumnAliases: Array | Object,
        aliasLabels: Object,
        quickFilter: Array,
        editable: Boolean
    },
    methods: {
        operatorToLocal(operator) {
            return {
                '=': 'Ist gleich',
                '!=': 'Ist nicht gleich',
                '>': 'Ist größer als',
                '>=': 'Ist größer oder gleich',
                '<=': 'Ist kleiner oder gleich',
                '<': 'Ist kleiner als'
            }[operator] || '?'
        },
        addFilter() {
            let quickFilter = [
                ...this.quickFilter, {
                    label: 'Quick Filter',
                    name: 'qf-' + (Math.floor(Math.random() * (999 - 111 + 1)) + 111),
                    where: [
                        [(this.usedColumnAliases.map(ele => ele.alias)[0] || ''), '=', '']
                    ]
                }
            ]
            this.$emit('update-data', 'quick_filter', quickFilter)
        },
        updateLabel(value, index){
            this.$emit('update-data', 'quick_filter', [...this.quickFilter].map((ele, i) => i === index ? {...ele, label: value} : ele))
        },
        updateQuickFilter() {
            this.$emit('update-data', 'quick_filter', [...this.quickFilter])
        },
        deleteFilter(filterName) {
            this.$emit('update-data', 'quick_filter', [...this.quickFilter.filter(ele => ele.name !== filterName)])
        },
        addWhere(filterName) {
            let quickFilter = [...this.quickFilter], that = this

            quickFilter = quickFilter.map(ele => {
                if (ele.name === filterName) {
                    ele.where.push([(that.usedColumnAliases.map(ele => ele.alias)[0] || ''), '=', ''])
                }
                return ele
            })

            this.$emit('update-data', 'quick_filter', quickFilter)
        },
        updateWhere(filterName, whereIndex, whereItemIndex, value) {
            let quickFilter = [...this.quickFilter]

            quickFilter = quickFilter.map(ele => {
                if (ele.name === filterName) {
                    ele.where[whereIndex][whereItemIndex] = value
                }
                return ele
            })

            this.$emit('update-data', 'quick_filter', quickFilter)
        },
        deleteWhere(filterName, index) {
            let quickFilter = [...this.quickFilter]

            quickFilter = quickFilter.map(ele => {
                if (ele.name === filterName) {
                    ele.where.splice(index, 1)
                }
                return ele
            })

            this.$emit('update-data', 'quick_filter', quickFilter)
        }
    }
}

</script>
