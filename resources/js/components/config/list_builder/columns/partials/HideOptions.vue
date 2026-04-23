<template>
    <div>
        <div class="form-group">
            <label class="mb-0">Ausblenden wenn...</label>
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-append">
                    <button
                        :class="'btn btn-sm dropdown-toggle rounded-left btn-outline-' + (usedColumnAliases.map(ele => ele.alias).includes(left) ? 'success' : 'primary')"
                        type="button" id="compare-field-left"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" :disabled="!editable">
                        {{ hide.length === 0 ? 'Deaktiviert' : (aliasLabels[left] || left) }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="compare-field-left">
                        <button type="button" class="dropdown-item" @click="onDeleteHide">Deaktiviert</button>
                        <div role="separator" class="dropdown-divider"></div>
                        <button type="button" v-for="item in usedColumnAliases" class="dropdown-item"
                                @click="onChangeHide(item.alias, operator, right)">
                            {{ aliasLabels[item.alias] || item.alias }}
                        </button>
                    </div>
                </div>
                <div class="input-group-append" v-if="left">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="operator"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" :disabled="!editable">
                        {{ labelForOperator(operator) }}
                    </button>
                    <input type="text" class="form-control form-control-sm rounded-0" aria-label="Label" v-if="!dropDownItems.map(ele => ele.value).includes(right)"
                           :value="right" @input="onInputRight" :data-color="right">
                    <div class="dropdown-menu" aria-labelledby="operator">
                        <button type="button" class="dropdown-item" @click="onChangeHide(left, '=', right)">Gleich
                        </button>
                        <button type="button" class="dropdown-item" @click="onChangeHide(left, '!=', right)">Nicht
                            gleich
                        </button>
                    </div>
                </div>
                <div class="input-group-append" v-if="left">
                    <button
                        class="btn btn-sm dropdown-toggle btn-outline-primary"
                        type="button" id="compare-field-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" :disabled="!editable">
                        <span v-if="right === null">NULL</span>
                        <span v-else-if="right === true">TRUE</span>
                        <span v-else-if="right === false">FALSE</span>
                        <span v-else>{{ (aliasLabels[right] || '#') }}</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="compare-field-right">
                        <button type="button" class="dropdown-item" @click="onChangeHide(left, operator, '')">Eigener Wert</button>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item" @click="onChangeHide(left, operator, item.value)" v-for="item in dropDownItems">{{item.label}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        hide: Array,
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        editable: Boolean
    },
    computed: {
        left: function () {
            return this.hide[0] || null;
        },
        operator: function () {
            return this.hide[1] || '=';
        },
        right: function () {
            return typeof this.hide[2] === 'boolean' ? this.hide[2] : this.hide[2];
        },
        dropDownItems(){
            let items = this.usedColumnAliases.map(ele => ({
                label: this.aliasLabels[ele.alias] || ele.alias,
                value: ele.alias
            }))

            return [...items, ...[
                {
                    label: 'NULL',
                    value: null,
                },
                {
                    label: 'TRUE',
                    value: true,
                },
                {
                    label: 'FALSE',
                    value: false,
                }
            ]]
        }
    },
    methods: {
        onInputRight(e) {
            this.$emit('type-options-change', 'hide', [
                this.left,
                this.operator,
                e.target.value
            ]);
        },
        onChangeHide(leftField, operator, rightField) {
            this.$emit('type-options-change', 'hide', [
                leftField,
                operator,
                rightField
            ]);
        },
        onDeleteHide() {
            this.$emit('type-options-change', 'hide', []);
        },
        labelForOperator(operator) {
            return {
                '=': 'Gleich',
                '!=': 'Nicht gleich'
            }[operator] || 'unknown';
        }
    }
};
</script>
