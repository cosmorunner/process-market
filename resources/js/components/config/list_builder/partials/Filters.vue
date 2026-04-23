<template>
    <div>
        <div class="card mb-2 mt-1" v-for="(filter, index) in filters">
            <div class="card-header bg-light p-2">
                <div class="d-flex justify-content-between">
                    <div class="d-flex align-content-center">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control form-control-sm" :value="filter.label" :aria-label="filter.label" :readonly="!editable" @change="updateLabel($event.target.value, index)"/>
                        </div>
                        <small class="ml-1 py-1">{{ filter.name }}</small>
                    </div>
                    <button class="btn btn-outline-danger btn-sm" @click="deleteFilter(filter.name)" v-if="editable">
                        <span class="material-icons font-we">close</span>
                    </button>
                </div>
            </div>
            <div class="card-body p-2">
                <div class="d-flex flex-column justify-content-between mb-2">
                    <div class="input-group input-group-sm input-group-btn">
                        <div class="input-group-append">
                            <button class="btn btn-sm dropdown-toggle rounded-left btn-outline-dark" type="button" :disabled="!editable"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ aliasLabels[filter.data] || filter.data || '#' }}
                            </button>
                            <div class="dropdown-menu dropdown-max-height-250">
                                <button type="button" v-for="item in usedColumnAliases" class="dropdown-item"
                                        @click="updateFilter(filter,'data', item.alias)">{{ aliasLabels[item.alias] || item.alias }}
                                </button>
                            </div>
                        </div>
                        <div class="input-group-append">
                            <button :class="'btn btn-sm dropdown-toggle btn-outline-primary'" type="button" :disabled="!editable"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ typeLabels[filter.type] || '?' }}
                            </button>
                            <div class="dropdown-menu" :aria-labelledby="'type'">
                                <button v-for="(label, type) in typeLabels" type="button" class="dropdown-item" @click="updateFilter(filter,'type', type)">
                                    {{ label }}
                                </button>
                            </div>
                        </div>
                        <div v-if="filter.type == 'date'" class="input-group-append">
                            <button :class="'btn btn-sm dropdown-toggle btn-outline-dark'" type="button" :disabled="!editable"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ dateFormatLabels[filter.type_options.format] || '#' }}
                            </button>
                            <div class="dropdown-menu dropdown-max-height-250">
                                <button type="button" v-for="(label ,format) in dateFormatLabels" class="dropdown-item" :disabled="!editable"
                                        @click="updateFilterTypeOptions(filter,'format', format)">{{ label }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-inline-block justify-content-start mt-2" @click="addFilter" v-if="editable">
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
        filters: Array,
        editable: Boolean
    },
    data() {
        return {
            typeLabels: {
                'string': 'Zeichenkette',
                'number': 'Zahl',
                'date': 'Datum',
                'other': 'Andere',
            },
            dateFormatLabels: {
                'dd-mm-yyyy': 'DD-MM-YYYY (z.B. 03-02-1991)',
                'dd.mm.yyyy': 'DD.MM.YYYY (z.B. 03.02.1991)',
                'yyyy-mm-dd': 'YYYY-MM-DD (z.B. 1991-02-03)',
                'yyyy': 'YYYY (z.B. 1991)',
            }
        };
    },
    methods: {
        addFilter() {
            let filters = [
                ...this.filters, {
                    label: 'Filter',
                    name: 'f-' + (Math.floor(Math.random() * (999 - 111 + 1)) + 111),
                    type: 'string',
                    type_options: {},
                    data: ''
                }
            ];
            this.updateFiltersInListConfig(filters);
        },
        updateLabel(value, index){
            this.$emit('update-data', 'filters', [...this.filters].map((ele, i) => i === index ? {...ele, label: value} : ele))
        },
        updateFilter(filter, field, value) {
            filter[field] = value;
            // Reset type options when the type is changed.
            if (field == 'type') {
                filter.type_options = {};
            }
            this.updateFiltersInListConfig([...this.filters]);
        },
        updateFilterTypeOptions(filter, field, value) {
            filter['type_options'][field] = value;
            this.updateFiltersInListConfig([...this.filters]);
        },
        deleteFilter(filterName) {
            this.updateFiltersInListConfig([...this.filters.filter(ele => ele.name !== filterName)]);
        },
        updateFiltersInListConfig(filters) {
            this.$emit('update-data', 'filters', filters);
        }
    }
};

</script>
