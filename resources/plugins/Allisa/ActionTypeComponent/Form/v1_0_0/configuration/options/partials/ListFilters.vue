<template>
    <div>
        <div>
            <template v-for="(filters, groupKey) in filtersByGroup">
                <div class="p-1">
                    <span class="p-1 text-primary">
                        <small>{{ groupLabels[groupKey] }}</small>
                    </span>
                    <table class="table table-sm table-borderless m-0">
                        <tbody>
                        <template v-for="(filter, index) in filters">
                            <tr class="d-flex">
                                <td class="py-0 w-30 mb-0">
                                    <span>{{ displayFilterLabel(filter.name) }}</span>
                                </td>
                                <td class="py-0 w-30 mb-0">{{ displayFilterOperator(filter.name, filter.operator) }}</td>
                                <td class="py-0 w-30 mb-0">
                                    <span v-if="filter.value.trim() === ''"><i>Leere Zeichenkette</i></span>
                                    <span v-else>{{ filter.value }}</span>
                                </td>
                                <td class="py-0 w-10 mb-0">
                                    <button class="btn btn-sm btn-light float-right" @click="deleteFilter(filter)" v-if="editable">
                                        <span class="material-icons text-danger">close</span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="index !== filters.length - 1">
                                <td class="text-muted p-0 pl-1 py-1">
                                    <small>und</small>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
                <span v-if="!lastIndex(Object.keys(filtersByGroup), groupKey)"
                      class="text-muted d-block pl-3 py-1 border-top border-bottom">
                    <small>oder</small>
                </span>
            </template>
        </div>
        <div class="input-group input-group-sm my-2 d-flex align-items-stretch" v-if="editable">
            <div class="input-group-prepend">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ groupLabels[newFilter.group] }}
                </button>
                <div class="dropdown-menu scrollable-dropdown">
                    <button v-for="index in 5" :key="index" type="button" class="dropdown-item" @click="changeValue('group','g_' + index)">
                        Gruppe {{ index }}
                    </button>
                </div>
            </div>
            <div class="input-group-append">
                <button class="btn btn-sm dropdown-toggle btn-outline-primary" type="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ newFilter.filter.label || '#' }}
                </button>
                <div class="dropdown-menu scrollable-dropdown">
                    <button type="button" class="dropdown-item" v-for="filter in listConfigFilters"
                            @click="changeFilter(filter)">
                        {{ filter.label }}
                    </button>
                </div>
            </div>
            <div v-if="newFilter.filter.type === 'number'" class="input-group-append">
                <button :class="'btn btn-sm dropdown-toggle btn-outline-dark border-right-0'" type="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ numberOperatorLabels[newFilter.operator] || '#' }}
                </button>
                <div class="dropdown-menu dropdown-max-height-250">
                    <button type="button" class="dropdown-item" v-for="(label, operator) in numberOperatorLabels"
                            @click="changeValue('operator', operator)">{{ label }}
                    </button>
                </div>
            </div>
            <div v-if="newFilter.filter.type === 'string'" class="input-group-append">
                <button :class="'btn btn-sm dropdown-toggle btn-outline-dark border-right-0'" type="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ stringOperatorLabels[newFilter.operator] || '#' }}
                </button>
                <div class="dropdown-menu dropdown-max-height-250">
                    <button type="button" class="dropdown-item" v-for="(label, operator) in stringOperatorLabels"
                            @click="changeValue('operator', operator)">{{ label }}
                    </button>
                </div>
            </div>
            <div v-if="newFilter.filter.type == 'date'" class="input-group-append">
                <button :class="'btn btn-sm dropdown-toggle btn-outline-dark border-right-0'" type="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ dateOperatorLabels[newFilter.operator] || '#' }}
                </button>
                <div class="dropdown-menu dropdown-max-height-250">
                    <button type="button" class="dropdown-item" v-for="(label, operator) in dateOperatorLabels"
                            @click="changeValue('operator', operator)">{{ label }}
                    </button>
                </div>
            </div>
            <div v-if="newFilter.filter.type == 'other'" class="input-group-append">
                <button :class="'btn btn-sm dropdown-toggle btn-outline-dark'" type="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ otherOperatorLabels[newFilter.operator] || '#' }}
                </button>
                <div class="dropdown-menu dropdown-max-height-250">
                    <button type="button" class="dropdown-item" v-for="(label, operator) in otherOperatorLabels"
                            @click="changeValue('operator', operator)">{{ label }}
                    </button>
                </div>
            </div>
            <select class="form-control border-dark" v-if="displayInputValue || displayInputSelect" v-model="newFilter.value">
                <option value="">Bitte wählen...</option>
                <option v-for="name in inputOutputNames">{{ name }}</option>
            </select>
            <select class="form-control border-dark" v-if="displayFirstInputDate" v-model="inBetweenDate[0]">
                <option value="">Bitte wählen...</option>
                <option v-for="name in inputOutputNames">{{ name }}</option>
            </select>
            <select class="form-control border-dark" v-if="displaySecondInputDate" v-model="inBetweenDate[1]">
                <option value="">Bitte wählen...</option>
                <option v-for="name in inputOutputNames">{{ name }}</option>
            </select>
            <div class="input-group-append">
                <button class="btn btn-sm rounded-right btn-outline-success" @click="addFilter">
                    <span class="material-icons">add</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    components: {
        VueTagsInput: () => import('@johmun/vue-tags-input'),
    },
    props: {
        listConfigFilters: Array | Object,
        inputOutputNames: Array,
        currentFilters: Array | Object,
        editable: Boolean
    },
    data() {
        return {
            newFilter: {
                'group': 'g_1',
                'filter': '',
                'operator': '',
                'value': '',
            },
            groupLabels: {
                'g_1': 'Gruppe 1',
                'g_2': 'Gruppe 2',
                'g_3': 'Gruppe 3',
                'g_4': 'Gruppe 4',
                'g_5': 'Gruppe 5',
            },
            numberOperatorLabels: {
                'equals': 'Ist gleich',
                'not_equal': 'Ist nicht gleich',
                'gt': 'Größer als',
                'gte': 'Größer oder gleich',
                'lt': 'Kleiner als',
                'lte': 'Kleiner oder gleich'
            },
            stringOperatorLabels: {
                'equals': 'Ist gleich',
                'start_with': 'Beginnt mit',
                'end_with': 'Endet mit',
                'contains': 'Enthält'
            },
            dateOperatorLabels: {
                'equal_age': 'Gleich (Datum)',
                'not_equal_age': 'Ist nicht gleich (Datum)',
                'younger': 'Jünger als',
                'younger_or_equal': 'Jünger or gleich alt',
                'older': 'Älter als',
                'older_or_equal': 'Älter oder gleich alt',
                'between': 'Ist zwischen'
            },
            otherOperatorLabels: {
                'where_in': 'Ist entweder oder',
            },
            inBetweenDate: ['', ''],
            selectValues: [],
            selectObject: {
                debounce: null,
                httpFetching: false,
                tag: '',
                tags: [],
                autocompleteItems: [],
                httpErrors: [],
            }
        };
    },
    computed: {
        filtersByGroup() {
            let grouped = {};

            for (let i = 0; i < this.currentFilters.length; i++) {
                let groupName = this.currentFilters[i]['group'];

                if (grouped.hasOwnProperty(groupName)) {
                    grouped[groupName].push(this.currentFilters[i]);
                }
                else {
                    grouped[groupName] = [this.currentFilters[i]];
                }
            }

            // Sort by group number
            grouped = Object.keys(grouped).sort().reduce((obj, key) => ({
                ...obj,
                [key]: grouped[key]
            }), {});

            return grouped;
        },
        displayInputValue() {
            let type = this.newFilter.filter.type || '';
            return (type == 'number' || type == 'string');
        },
        displayFirstInputDate() {
            let type = this.newFilter.filter.type || '';
            return type == 'date';
        },
        displaySecondInputDate() {
            let type = this.newFilter.filter.type || '';
            let operator = this.newFilter.operator || '';
            return (type == 'date' && operator == 'between');
        },
        displayInputSelect() {
            let type = this.newFilter.filter.type || '';
            let operator = this.newFilter.operator || '';
            return (type == 'other' && operator == 'where_in');
        }
    },
    methods: {
        changeValue(field, value) {
            this.newFilter[field] = value;
        },
        changeFilter(filter) {
            this.newFilter.filter = filter;

            // Reset fields on the filter's change.
            this.newFilter.operator = '';
            this.newFilter.value = '';
            this.inBetweenDate = ['', ''];
            this.selectValues = [];
        },
        addFilter() {
            if (this.newFilter.name == '' || this.newFilter.operator == '') {
                return;
            }

            this.handleNewFilter();

            this.$emit('update-filters', [
                ...this.currentFilters, {
                    name: this.newFilter.filter.name,
                    group: this.newFilter.group,
                    operator: this.newFilter.operator,
                    value: this.newFilter.value
                }
            ]);

            // Reset
            this.newFilter = {
                'group': 'g_1',
                'filter': '',
                'operator': '',
                'value': '',
            };
        },
        displayFilterLabel(name) {
            let filterConfig = this.listConfigFilters.find(ele => ele.name === name);

            return filterConfig.label || '';
        },
        displayFilterOperator(name, operator) {
            let filterConfig = this.listConfigFilters.find(ele => ele.name === name);

            switch (filterConfig.type) {
                case 'number':
                    return this.numberOperatorLabels[operator] || '#';
                case 'string':
                    return this.stringOperatorLabels[operator] || '#';
                case 'date':
                    return this.dateOperatorLabels[operator] || '#';
                case 'other':
                    return this.otherOperatorLabels[operator] || '#';
                default:
                    return '??';
            }
        },
        lastIndex(items, key) {
            return items.indexOf(key) === items.length - 1;
        },
        deleteFilter(filter) {
            this.$emit('update-filters', [...this.currentFilters.filter(ele => ele.name !== filter.name
                || ele.group !== filter.group
                || ele.value !== filter.value
                || ele.operator !== filter.operator)]);
        },
        handleNewFilter() {
            let type = this.newFilter.filter.type;

            if (type == 'date') {
                if (this.newFilter.operator == 'between') {
                    this.newFilter.value = this.inBetweenDate.join(', ');
                }
                else {
                    this.newFilter.value = this.inBetweenDate[0];
                }
            }
        },
    }
};
</script>

