<template>
    <div>
        <div class="mb-2">
            <label class="mb-0">Icon / Wert Mapping</label>
            <div class="mb-1">
                <small class="text-muted">Semikolon-separierte Liste an Werten bei denen das gewählte Icon angezeigt
                    werden soll. "*" für beliebige Werte.</small>
            </div>
            <template v-for="(values, icon) in mapping">
                <div class="input-group input-group-sm mb-2">
                    <IconSelection :selected="icon" :require-selection="true"
                                   @on-select-icon="onChangeIconValueMapping($event, icon)" :editable="editable"/>
                    <input type="text" class="form-control" aria-label="Label" :placeholder="'Leere Zeichenkette'"
                           :value="values.join('; ')" @input="onInputIconValueMapping" :data-icon="icon" :readonly="!editable">
                    <div class="input-group-append" v-if="editable">
                        <button class="btn btn-outline-danger" @click="onDeleteIconValueMapping(icon)">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                </div>
            </template>
            <div class="d-flex justify-content-end" v-if="editable">
                <button class="btn btn-sm btn-outline-success" @click="onAddIconValueMapping">
                    <span class="material-icons">add</span>
                </button>
            </div>
        </div>
        <div>
            <label class="mb-0">Farbe / Wert Mapping</label>
            <div class="mb-1">
                <small class="text-muted">Semikolon-separierte Liste an Werten bei denen das Icon die gewählte Farbe
                    haben soll. "*" für beliebige Werte.</small>
            </div>
            <template v-for="(values, color) in colors">
                <div class="input-group input-group-sm mb-2">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" :disabled="!editable"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span :class="'material-icons text-' + color">lens</span>
                        </button>
                        <div class="dropdown-menu scrollable-dropdown">
                            <button type="button" class="dropdown-item" v-for="selectColor in colorSelection"
                                    @click="onChangeColorValueMapping(selectColor, color)">
                                <span :class="'material-icons mi-2x text-' + selectColor">lens</span>
                            </button>
                        </div>
                    </div>
                    <input type="text" class="form-control" aria-label="Label" :placeholder="'Leere Zeichenkette'" :readonly="!editable"
                           :value="values.join('; ')" @input="onInputColorValueMapping" :data-color="color">
                    <div class="input-group-append" v-if="editable">
                        <button class="btn btn-outline-danger" @click="onDeleteColorValueMapping(color)">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                </div>
            </template>
            <div class="d-flex justify-content-end" v-if="usableColors.length && editable">
                <button class="btn btn-sm btn-outline-success" @click="onAddColorValueMapping">
                    <span class="material-icons">add</span>
                </button>
            </div>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="dataField">Größe</label>
            <select class="form-control" id="dataField" @change="onSizeChange" :value="size" :disabled="!editable">
                <option value="normal">Normal</option>
                <option value="small">Klein</option>
            </select>
        </div>
        <div class="form-group mt-2 mb-0">
            <HideOptions :alias-labels="aliasLabels"
                         :used-column-aliases="usedColumnAliases"
                         :hide="hide"
                         :editable="editable"
                         v-on="$listeners"
            />
        </div>
    </div>
</template>

<script>

import {icons, favorites} from '../icons';
import HideOptions from "./partials/HideOptions";
import IconSelection from "../partials/IconSelection";

export default {
    components: {
        IconSelection,
        HideOptions
    },
    props: {
        column: Object,
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        editable: Boolean
    },
    data() {
        return {
            icons: icons,
            favorites: favorites,
            colorSelection: [
                'primary',
                'secondary',
                'success',
                'danger',
                'warning',
                'info'
            ]
        };
    },
    computed: {
        size: function () {
            return this.column.type_options.size || 'normal';
        },
        mapping: function () {
            return this.column.type_options.mapping || {};
        },
        colors: function () {
            return this.column.type_options.colors || {};
        },
        hide: function () {
            return this.column.type_options.hide || [];
        },
        usableColors: function () {
            return this.colorSelection.filter(ele => !Object.keys(this.colors).includes(ele));
        }
    },
    methods: {
        onSizeChange(e) {
            this.$emit('type-options-change', 'size', e.target.value);
        },
        onAddIconValueMapping() {
            let icon = icons.filter(ele => !Object.keys(this.mapping).includes(ele))[0];
            let mapping = {
                ...this.mapping,
                [icon]: []
            };
            this.$emit('type-options-change', 'mapping', mapping);
        },
        onChangeIconValueMapping(newIcon, oldIcon) {
            if (Object.keys(this.mapping).includes(newIcon)) {
                return;
            }

            // Altes icon (property) an genau der alten Stelle mit neuem Icon ersetzen.
            let mapping = Object.keys(this.mapping).reduce((acc, val) => {
                if (val === oldIcon) {
                    acc[newIcon] = this.mapping[oldIcon];
                }
                else {
                    acc[val] = this.mapping[val];
                }
                return acc;
            }, {});

            this.$emit('type-options-change', 'mapping', mapping);
        },
        onInputIconValueMapping(e) {
            let icon = e.target.dataset.icon;
            let values = e.target.value.split(';');

            this.$emit('type-options-change', 'mapping', {
                ...this.mapping,
                [icon]: values
            });
        },
        onDeleteIconValueMapping(icon) {
            let mapping = {...this.mapping};
            delete mapping[icon];
            this.$emit('type-options-change', 'mapping', mapping);
        },
        onAddColorValueMapping() {
            if (!this.usableColors.length) {
                return;
            }

            let colors = {
                ...this.colors,
                [this.usableColors[0]]: []
            };
            this.$emit('type-options-change', 'colors', colors);
        },
        onInputColorValueMapping(e) {
            let color = e.target.dataset.color;
            let values = e.target.value.split(';');

            this.$emit('type-options-change', 'colors', {
                ...this.colors,
                [color]: values
            });
        },
        onChangeColorValueMapping(newColor, oldColor) {
            if (Object.keys(this.colors).includes(newColor)) {
                return;
            }

            // Altes icon (property)an genau der alten Stelle mit neuem Icon ersetzen.
            let colors = Object.keys(this.colors).reduce((acc, val) => {
                if (val === oldColor) {
                    acc[newColor] = this.colors[oldColor];
                }
                else {
                    acc[val] = this.colors[val];
                }
                return acc;
            }, {});

            this.$emit('type-options-change', 'colors', colors);
        },
        onDeleteColorValueMapping(icon) {
            let colors = {...this.colors};
            delete colors[icon];
            this.$emit('type-options-change', 'colors', colors);
        },
    }
};
</script>
