<template>
    <div class="row">
        <div class="col">
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" :disabled="!editable">
                        <span class="material-icons">{{ iconForType(column.type) }}</span>
                        {{ columnLabels[column.type] }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <button v-for="(value, propertyName) in columnLabels" @click="onTypeChange(propertyName)"
                                class="dropdown-item" type="button">{{ value }}
                        </button>
                    </div>
                </div>
                <input type="text" class="form-control" aria-label="Label" name="label" @input="onPropertyChange"
                       :readonly="!editable" :value="column.label" :placeholder="'Spaltenlabel'">
            </div>
            <template v-if="column.type !== 'rowSelection'">
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="dataField">Primäres Datenfeld</label>
                    <select class="form-control" id="dataField" name="data" @change="onPropertyChange"
                            :value="column.data || ''" :disabled="!editable">
                        <option value="">-</option>
                        <template v-for="item in usedColumnAliases">
                            <option :value="item.alias">
                                {{
                                    aliasLabels[item.alias] ? (aliasLabels[item.alias] + ' - ' + item.alias) : item.alias
                                }}
                            </option>
                        </template>
                    </select>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="aliasField">Sekundäres Datenfeld (Alias)</label>
                    <select class="form-control" id="aliasField" name="alias" @change="onPropertyChange"
                            :value="column.alias || ''" :disabled="!editable">
                        <option value="">-</option>
                        <template v-for="item in usedColumnAliases">
                            <option :value="item.alias">{{
                                    aliasLabels[item.alias] ? (aliasLabels[item.alias] + ' - ' + item.alias) : item.alias
                                }}
                            </option>
                        </template>
                    </select>
                    <small class="text-muted">Wird genutzt, wenn das primäre Datenfeld leer ist.</small>
                </div>
                <template v-if="column.type === 'url' || column.type === 'text'">
                    <div class="form-group">
                        <label class="mb-0">Konkatenation</label>
                        <Concatenation :editable="editable" :concat="concat"
                                       :selectable-aliases="usedColumnAliases.map(i => i.alias)"
                                       :alias-labels="aliasLabels" @concat-updated="onConcatUpdated"/>
                    </div>
                    <div class="form-group">
                        <label class="mb-0">Subtitel</label>
                        <div class="input-group input-group-sm mb-2 input-group-btn">
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle rounded-left"
                                        type="button" id="subtitleDropdown" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false" :disabled="!editable">
                                    <span v-if="!subtitle.length">Deaktiviert</span>
                                    <span v-else-if="subtitle.length && (subtitle[0] === '' || subtitle[0] === null)">Leere Zeichenkette</span>
                                    <span
                                        v-else-if="subtitle.length && subtitle[0] === '_blank_space_'">Leerzeichen</span>
                                    <span v-else>"{{ subtitle[0].replaceAll('_blank_space_', ' ') }}"</span>
                                </button>
                                <div class="dropdown-menu scrollable-dropdown" aria-labelledby="subtitleDropdown">
                                    <button type="button" class="dropdown-item" @click="onChangeSubtitleSplit(null)">
                                        Deaktiviert
                                    </button>
                                    <div class="dropdown-divider"></div>
                                    <button type="button" class="dropdown-item" @click="onChangeSubtitleSplit('')">Leere
                                        Zeichenkette
                                    </button>
                                    <button type="button" class="dropdown-item"
                                            @click="onChangeSubtitleSplit('_blank_space_')">
                                        Leerzeichen
                                    </button>
                                    <button type="button" class="dropdown-item" @click="onChangeSubtitleSplit('-')">"-"
                                    </button>
                                    <button type="button" class="dropdown-item"
                                            @click="onChangeSubtitleSplit('_blank_space_-_blank_space_')">" - "
                                    </button>
                                    <button type="button" class="dropdown-item" @click="onChangeSubtitleSplit('_')">"_"
                                    </button>
                                    <button type="button" class="dropdown-item" @click="onChangeSubtitleSplit('/')">"/"
                                    </button>
                                    <button type="button" class="dropdown-item"
                                            @click="onChangeSubtitleSplit('_blank_space_/_blank_space_')">" / "
                                    </button>
                                </div>
                            </div>
                            <div class="input-group-append" v-if="subtitle.length > 1">
                                <button
                                    :class="'btn btn-sm dropdown-toggle btn-outline-' + (usedColumnAliases.map(ele => ele.alias).includes(subtitle[1]) ? 'success' : 'danger')"
                                    type="button" id="firstSubtitleDropdown" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false" :disabled="!editable">
                                    {{ (aliasLabels[subtitle[1]] || subtitle[1]) || ('1. Datenfeld') }}
                                </button>
                                <div class="dropdown-menu scrollable-dropdown" aria-labelledby="firstSubtitleDropdown">
                                    <a v-for="item in usedColumnAliases" class="dropdown-item" href="#"
                                       @click="onChangeSubtitleField(1, item.alias)">{{
                                            aliasLabels[item.alias] || item.alias
                                        }}</a>
                                </div>
                            </div>
                            <div class="input-group-append" v-if="subtitle.length > 1">
                                <button
                                    :class="'btn btn-sm dropdown-toggle btn-outline-' + (usedColumnAliases.map(ele => ele.alias).includes(subtitle[2]) ? 'success' : 'danger')"
                                    type="button" id="secondSubtitleDropdown" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" :disabled="!editable">
                                    {{ (aliasLabels[subtitle[2]] || subtitle[2]) || ('2. Datenfeld') }}
                                </button>
                                <div class="dropdown-menu scrollable-dropdown" aria-labelledby="secondSubtitleDropdown">
                                    <button type="button" class="dropdown-item" @click="onChangeSubtitleField(2, '')">
                                        Ohne Wert
                                    </button>
                                    <div class="dropdown-divider"></div>
                                    <button type="button" v-for="item in usedColumnAliases" class="dropdown-item"
                                       @click="onChangeSubtitleField(2, item.alias)">{{
                                            aliasLabels[item.alias] || item.alias
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-if="column.type !== 'icon' && column.type !== 'button'">
                    <div class="custom-control custom-switch mb-2" v-if="column.type !== 'progress'">
                        <input type="checkbox" class="custom-control-input" id="searchable" :checked="searchable"
                               @change="searchable = !searchable" :disabled="!editable">
                        <label class="custom-control-label" for="searchable">Durchsuchbar</label>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="sortable" :checked="sortable"
                               @change="sortable = !sortable" :disabled="!editable">
                        <label class="custom-control-label" for="sortable">Sortierbar</label>
                    </div>
                </template>
            </template>
            <div class="form-group mt-2 mb-0">
                <label class="mb-0" for="color">Hintergrundfarbe</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" aria-label="Url" v-model="color" id="color"
                           :readonly="!editable">
                    <div class="input-group-append" v-if="editable">
                        <button class="btn btn-outline-primary" @click="color = ''">
                            <span class="material-icons">close</span>
                        </button>
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">build</span>
                        </button>
                        <div class="dropdown-menu scrollable-dropdown">
                            <button type="button" class="dropdown-item" v-for="item in usedColumnAliases"
                                    @click="color = item.alias">{{ aliasLabels[item.alias] || item.alias }}
                            </button>
                        </div>
                    </div>
                </div>
                <small class="text-muted">HEX-Farbcode eingeben (mit "#") oder Datenfeld auswählen.</small>
            </div>
        </div>
    </div>
</template>

<script>

import Concatenation from "./partials/Concatenation.vue";

export default {
    components: {Concatenation},
    props: {
        column: Object,
        columns: Array,
        columnLabels: Object,
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        editable: Boolean
    },
    data() {
        return {
            color: this.column.color,
            searchable: this.column.searchable,
            sortable: this.column.sortable,
        };
    },
    computed: {
        concat: function () {
            if (this.column.type_options.hasOwnProperty('concat')) {
                return this.column.type_options.concat;
            }
            return [];
        },
        subtitle: function () {
            return this.column.type_options.subtitle || [];
        }
    },
    methods: {
        iconForType(type) {
            let icons = {
                button: 'touch_app',
                icon: 'star',
                progress: 'flag',
                tags: 'local_offer',
                text: 'title',
                currency: 'euro_symbol',
                url: 'link',
                input: 'border_color',
                select: 'view_list',
                rowSelection: 'check_circle',
                arrayData: 'format_list_bulleted'
            };

            return icons[type] || 'help';
        },
        onTypeChange(value) {
            this.$emit('type-change', value);
        },
        onPropertyChange(e) {
            this.$emit('property-change', e.target.name, e.target.value);
        },
        onChangeSubtitleSplit(split) {
            if (split === null) {
                this.$emit('type-options-change', 'subtitle', []);
            }
            else {
                this.$emit('type-options-change', 'subtitle', [
                    split,
                    (this.subtitle[1] || ''),
                    (this.subtitle[2] || '')
                ]);
            }
        },
        onChangeSubtitleField(fieldNr, value) {
            let subtitle = [...this.subtitle];
            subtitle[fieldNr] = value;

            this.$emit('type-options-change', 'subtitle', subtitle);
        },
        onConcatUpdated(concat) {
            this.$emit('type-options-change', 'concat', concat);
        }
    },
    watch: {
        searchable: function (newVal) {
            this.$emit('property-change', 'searchable', newVal);
        },
        sortable: function (newVal) {
            this.$emit('property-change', 'sortable', newVal);
        },
        color: function (newVal) {
            this.$emit('property-change', 'color', newVal);
        }
    }
};
</script>
