<template>
    <div class="row">
        <div class="col">
            <div class="form-group mb-0">
                <label class="mb-2">POST Request Parameter</label>
                <table :class="editable ? 'table table-hover' : 'table'" v-if="items.length">
                    <tbody>
                    <tr>
                        <td class="p-1 border-0">
                            <span class="text-muted">Name</span>
                        </td>
                        <td class="p-1 border-0">
                            <span class="text-muted">Wert</span>
                        </td>
                        <td class="p-1 border-0">
                        </td>
                    </tr>
                    <tr v-for="(item, index) in items" :class="editable ? 'mouse-pointer' : ''">
                        <template v-if="editing === index">
                            <td class="p-1 w-50">
                                <input class="form-control form-control-sm p-1" type="text" :value="editName" @input="onChangeText" required/>
                            </td>
                            <td class="p-1">
                                <div class="input-group input-group-sm">
                                    <input class="form-control form-control-sm p-1" type="text" :value="editValue" @input="onChangeValue" required/>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                            <span class="material-icons">storage</span>
                                        </button>
                                        <div class="dropdown-menu scrollable-dropdown">
                                            <button type="button" class="dropdown-item" v-for="(label, key) in bindingValueLabels"
                                                    @click="editValue = key">{{ bindingValueLabels[key] || key }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                            <td class="p-1 w-50" @click="editable ? () => onOptionEdit(index) : () => {}">{{ item.name }}</td>
                            <td class="p-1" @click="editable ? () => onOptionEdit(index) : () => {}">
                                <span class="font-italic" v-if="!item.value">Leere Zeichenkette</span>
                                <span v-if="Object.keys(bindingValueLabels).includes(item.value)" class="text-success">
                                    <span class="material-icons">dns</span>
                                    <span>{{ bindingValueLabels[item.value] }}</span>
                                </span>
                                <span v-else :class="Object.keys(bindingValueLabels).includes(item.value) ? 'text-success' : ''">{{ item.value }}</span>
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
                                <span class="input-group-text" id="inputGroup-label">Name</span>
                            </div>
                            <input type="text" class="form-control" v-model="newName" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-value">Wert</span>
                            </div>
                            <input type="text" class="form-control" v-model="newValue" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <span class="material-icons">dns</span>
                                </button>
                                <div class="dropdown-menu scrollable-dropdown">
                                    <button type="button" class="dropdown-item" v-for="(label, key) in bindingValueLabels"
                                            @click="newValue = key">{{ bindingValueLabels[key] || key }}
                                    </button>
                                </div>
                            </div>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-outline-success" @click="onAddOption" :disabled="newName === ''">
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
        bindingValueLabels: Object | Array,
        items: {
            required: true,
            type: Array
        },
        editable: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            editing: null,
            editName: '',
            editValue: '',
            newName: '',
            newValue: ''
        };
    },
    methods: {
        onOptionEdit(index) {
            if (this.editing !== null) {
                return;
            }
            let option = {...this.items[index]};
            this.editing = index;
            this.editName = option.name || '';
            this.editValue = option.value || '';
        },
        onOptionSave() {
            if(!this.editName.trim()) {
                return;
            }

            let items = [...this.items];

            items[this.editing] = {
                name: this.editName.trim(),
                value: this.editValue.trim()
            };

            this.$emit('items-change', items);
            this.editing = null;
            this.editName = '';
            this.editValue = '';
        },
        onAddOption(){
            let existingNames = this.items.map(ele => ele.name)
            let newName = this.newName.trim()

            // Keine doppelten Werte
            if(existingNames.includes(newName)) {
                return;
            }

            let items = [...this.items, {
                name: newName,
                value: this.newValue
            }];

            this.newName = ''
            this.newValue = ''

            this.$emit('items-change', items);
        },
        onEditCancel() {
            this.editing = null;
            this.editName = '';
            this.editValue = '';
        },
        onDeleteOption(index){
            let items = [...this.items];
            items = items.filter((ele, eleIndex) => eleIndex !== index)

            this.$emit('items-change', items);
        },
        onChangeText(e){
            this.editName = e.target.value
        },
        onChangeValue(e){
            this.editValue = e.target.value
        }
    }
};
</script>
