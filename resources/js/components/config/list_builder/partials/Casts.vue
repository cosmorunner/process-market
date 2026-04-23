<template>
    <div class="row">
        <div class="col-12">
                 <span class="d-flex justify-content-between mb-2">
                    <span>
                        <span>Casts</span>
                        <span class="badge badge-light" v-if="casts.length > 0">{{ casts.length }}</span>
                    </span>
                </span>
            <template v-if="casts.length">
                <div class="p-1">
                    <table class="table table-sm bg-light table-borderless m-0 mb-2" v-for="(item, index) in casts">
                        <tbody>
                        <template>
                            <tr class="d-flex">
                                <td class="py-1 w-40 mb-0">
                                        <span
                                            :class="selectedAliasExists(item.alias) ? 'text-success' : 'text-danger'">
                                            <span>{{ aliasLabels[item.alias] || item.alias }}</span>
                                        </span>
                                </td>
                                <td class="py-1 w-20 mb-0">{{ typeLabels[item.type] || '???' }}</td>
                                <td class="py-1 w-20 mb-0">{{ (item.type_options || {}).format || '' }}</td>
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
                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="castColumnAlias"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ labelByAlias(newItem.alias) || '#' }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown" aria-labelledby="castColumnAlias">
                        <button type="button" class="dropdown-item" v-for="item in usedColumnAliases"
                                @click="changeValue($event, 'alias', item.alias)">
                            <span class="text-muted">Datenfeld: </span>{{ aliasLabels[item.alias] || item.alias }}
                        </button>
                    </div>
                </div>
                <select class="custom-select" id="castTypeSelect" v-model="newItem.type">
                    <option value="string">Zeichenkette</option>
                    <option value="number">Zahl</option>
                    <option value="datetime">Datum mit Uhrzeit</option>
                    <option value="date">Datum</option>
                    <option value="time">Uhrzeit - mm:hh</option>
                </select>
                <div class="input-group-append" v-if="newItem.type === 'date'">
                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dateFormat"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ (newItem.type_options || {}).format || '#' }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown" aria-labelledby="dateFormat">
                        <button type="button" class="dropdown-item" v-for="date in dateFormats"
                                @click="changeTypeFormat(date.format)">
                            {{ date.label }}
                        </button>
                    </div>
                </div>
                <div class="input-group-append" v-if="newItem.type === 'datetime'">
                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dateTimeFormat"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ (newItem.type_options || {}).format || '#' }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown" aria-labelledby="dateTimeFormat">
                        <button type="button" class="dropdown-item" v-for="dateTime in dateTimeFormats"
                                @click="changeTypeFormat(dateTime.format)">
                            {{ dateTime.label }}
                        </button>
                    </div>
                </div>
                <div class="input-group-prepend">
                    <button class="btn btn-sm rounded-right btn-outline-success" @click="addCast">
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
        data: Object,
        locals: Object,
        usedColumnAliases: Array | Object,
        aliasLabels: Object,
        editable: Boolean
    },
    data() {
        return {
            casts: this.data.source.casts || [],
            newItem: {
                'alias': '',
                'type': 'string',
                'type_options': {}
            },
            typeLabels: {
                'string': 'Zeichenkette',
                'number': 'Zahl',
                'datetime': 'Datum mit Uhrzeit',
                'date': 'Datum',
                'time': 'Uhrzeit',
            },
            dateFormats: [
                {
                    'format': 'dd.mm.yyyy',
                    'label': 'DD.MM.YYYY (z.B. 03.02.1991)',
                },
                {
                    'format': 'dd-mm-yyyy',
                    'label': 'DD-MM-YYYY (z.B. 03-02-1991)',
                }
            ],
            dateTimeFormats: [
                {
                    'format': 'dd.mm.yyyy hh:mi:ss',
                    'label': 'DD.MM.YYYY H:M:S (z.B. 03.02.1991 13:37:00)',
                },
                {
                    'format': 'dd-mm-yyyy hh:mi:ss',
                    'label': 'DD-MM-YYYY H:M:S (z.B. 03-02-1991 13:37:00)',
                },
                {
                    'format': 'dd.mm.yyyy hh:mi',
                    'label': 'DD.MM.YYYY H:M (z.B. 03.02.1991 13:37)',
                },
                {
                    'format': 'dd-mm-yyyy hh:mi',
                    'label': 'DD-MM-YYYY H:M (z.B. 03-02-1991 13:37)',
                }
            ],
        };
    },
    methods: {
        addCast() {
            if (this.newItem.alias === '') {
                return;
            }

            this.casts.push({...this.newItem});
            // Reset
            this.newItem.alias = '';
            this.newItem.type = 'string';
            this.$delete(this.newItem, 'type_options');
        },
        labelByAlias(alias) {
            let columnItem = this.supportData.allColumns.find(ele => ele.alias === alias);

            return columnItem ? columnItem.label : alias;
        },
        changeValue(e, field, value) {
            this.newItem[field] = value || e.target.value;
        },
        selectedAliasExists(alias) {
            return this.usedColumnAliases.find(ele => ele.alias === alias);
        },
        deleteItem(index) {
            this.casts.splice(index, 1);
        },
        changeTypeFormat(format) {
            this.newItem.type_options.format = format;
        }
    },
    watch: {
        casts() {
            this.$emit('update-source', 'casts', [...this.casts]);
        },
        'newItem.type': function (newValue) {
            // Set default date format when the type is date.
            if (newValue === 'date') {
                this.$set(this.newItem, 'type_options', {'format': this.dateFormats[0].format});
            }
            // Set default date time format when the type is datetime.
            else if (newValue === 'datetime') {
                this.$set(this.newItem, 'type_options', {'format': this.dateTimeFormats[0].format});
            }
            else {
                // We only need type_options field when the type is date.
                this.$delete(this.newItem, 'type_options');
            }
        },
    },
};

</script>
