<template>
    <div class="row">
        <div class="col">
            <div class="form-group mb-2">
                <label class="mb-2">Optionen</label>
                <table :class="editable ? 'table table-hover' : 'table'" v-if="field.items.length">
                    <tbody>
                    <tr>
                        <td class="p-1 border-0">
                            <span class="text-muted">Label</span>
                        </td>
                        <td class="p-1 border-0">
                            <span class="text-muted">Wert</span>
                        </td>
                        <td class="p-1 border-0">
                        </td>
                    </tr>
                    <tr v-for="(item, index) in field.items" :class="editable ? 'mouse-pointer' : ''">
                        <template v-if="editing === index">
                            <td class="p-1 w-50">
                                <input class="form-control form-control-sm p-1" type="text" :value="editLabel" @input="onChangeText" required/>
                            </td>
                            <td class="p-1">
                                <input class="form-control form-control-sm p-1" type="text" :value="editValue" @input="onChangeValue" required/>
                            </td>
                            <td class="p-1">
                                <button class="btn btn-sm btn-outline-danger px-1 py-0" @click="onEditCancel">
                                    <span class="material-icons">close</span>
                                </button>
                                <button class="btn btn-sm btn-success px-1 py-0" @click="onOptionSave(index)">
                                    <span class="material-icons">done</span>
                                </button>
                            </td>
                        </template>
                        <template v-if="editing !== index">
                            <td class="p-1 w-50" @click="onOptionEdit(index)">{{ item.label }}</td>
                            <td class="p-1" @click="onOptionEdit(index)">
                                <span class="font-italic" v-if="item.value === ''">Leere Zeichenkette</span>
                                <span v-else>{{ item.value }}</span>
                            </td>
                            <td class="p-1">
                                <button v-if="editing === null && editable" class="btn btn-sm float-right btn-light px-1 py-0" @click="onDeleteOption(index)">
                                    <span class="material-icons text-danger">close</span>
                                </button>
                            </td>
                        </template>
                    </tr>
                    </tbody>
                </table>
                <div class="row" v-if="editable">
                    <div class="col-6">
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-label">Label</span>
                            </div>
                            <input type="text" class="form-control" v-model="newLabel" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-value">Wert</span>
                            </div>
                            <input type="text" class="form-control" v-model="newValue" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-outline-success" @click="onAddOption">
                                    <span class="material-icons">add</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        field: {
            required: true,
            type: Object
        },
        editable: Boolean
    },
    data() {
        return {
            editing: null,
            editLabel: '',
            editValue: '',
            newLabel: '',
            newValue: ''
        };
    },
    methods: {
        onOptionEdit(index) {
            if (this.editing !== null || !this.editable) {
                return;
            }

            let option = {...this.field.items[index]};
            this.editing = index;
            this.editLabel = option.label || '';
            this.editValue = option.value || '';
        },
        onOptionSave() {
            if(!this.editLabel.trim()) {
                return;
            }

            let items = [...this.field.items];

            items[this.editing] = {
                label: this.editLabel.trim(),
                value: this.editValue.trim()
            };

            this.$emit('property-change', 'items', items);
            this.editing = null;
            this.editLabel = '';
            this.editValue = '';
        },
        onAddOption(){
            let existingValues = this.field.items.map(ele => ele.value)
            let newValue = this.newValue.trim()

            // Keine doppelten Werte
            if(existingValues.includes(newValue)) {
                return;
            }

            let items = [...this.field.items, {
                label: this.newLabel,
                value: newValue
            }];

            this.newLabel = ''
            this.newValue = ''

            this.$emit('property-change', 'items', items);
        },
        onEditCancel() {
            this.editing = null;
            this.editLabel = '';
            this.editValue = '';
        },
        onDeleteOption(index){
            let items = [...this.field.items];
            items = items.filter((ele, eleIndex) => eleIndex !== index)

            this.$emit('property-change', 'items', items);
        },
        onChangeText(e){
            this.editLabel = e.target.value
        },
        onChangeValue(e){
            this.editValue = e.target.value
        }
    }
};
</script>
