<template>
    <div class="form-group m-2">
        <div v-for="(value, statusTypeReference) in situation" class="row mb-1" v-if="statusTypeByReference(statusTypeReference)">
            <div class="col-4">
                {{ statusTypeByReference(statusTypeReference).name }}
            </div>
            <div class="col-7">
                <span class="badge badge-light" v-if="valueIsUuid(value) && getStateIcon(statusTypeReference, value)">
                    <span class="material-icons">{{ getStateIcon(statusTypeReference, value) }}</span>
                </span>
                <span>{{ getSituationValue(statusTypeReference, value) }}</span>
            </div>
            <div class="col-1">
                <button class="btn btn-sm btn-light float-right" @click="$emit('delete-situation-option', statusTypeReference)" v-if="editable">
                    <span class="material-icons text-danger">close</span>
                </button>
            </div>
        </div>
        <span class="text-muted" v-if="!statusTypes.length">Der gewählte Prozess hat keine Status.</span>
        <div v-if="newOption && unusedStatusTypes.length && editable" class="mb-3 mt-2">
            <div class="input-group input-group-sm mb-0 mt-1">
                <div class="input-group-prepend">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ newOption.name }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown">
                        <button v-for="statusType in unusedStatusTypes" type="button" class="dropdown-item"
                                @click="switchStatusType(statusType.reference)">{{ statusType.name }}
                        </button>
                    </div>
                </div>
                <div class="input-group-prepend">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ newOption.stateDescription || '#' }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown">
                        <button v-for="state in newOption.states.filter(ele => ele.min === ele.max)" type="button"
                                class="dropdown-item" @click="switchState(state)">{{ state.description }}
                        </button>
                    </div>
                </div>
                <input type="number" step="0.001" class="form-control" aria-label="Manual value" placeholder="...oder Wert eingeben." :value="newOption.value" @input="onInputValue">
                <div class="input-group-append">
                    <button class="btn btn-sm btn-outline-success" @click="addSituationOption">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
            <small class="text-muted">Format-Beispiele für manuellen Wert: "0", "123", "1.337", "-10.5".</small>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        statusTypes: Array,
        situationProp: Object | Array,
        editable: Boolean
    },
    data() {
        return {
            newOption: null
        };
    },
    computed: {
        unusedStatusTypes() {
            if (this.situation === null) {
                return this.statusTypes;
            }

            return this.statusTypes.filter(ele => !Object.keys(this.situation).includes(ele.reference));
        },
        situation() {
            return this.situationProp;
        }
    },
    methods: {
        switchStatusType(reference) {
            this.resetNewOption(this.unusedStatusTypes.find(ele => ele.reference === reference));
        },
        switchState(state) {
            this.updateNewOption({
                ...this.newOption,
                stateDescription: state.description,
                stateId: state.id,
                value: ''
            });
        },
        onInputValue(e) {
            this.updateNewOption({
                ...this.newOption,
                stateDescription: null,
                stateId: null,
                value: e.target.value
            });
        },
        updateNewOption(part, value) {
            // Wenn "value" undefined ist, ist "part" das komplette "newOption" Objekt.
            if (typeof value === 'undefined') {
                this.newOption = part;
            }
            else {
                this.newOption = {
                    ...this.newOption,
                    [part]: value
                };
            }
        },
        resetNewOption(statusType = null) {
            statusType = statusType || this.unusedStatusTypes[0] || null;

            if (statusType === null) {
                this.newOption = null;
                return null;
            }

            let state = statusType.states.filter(ele => ele.min === ele.max)[0] || null;

            this.newOption = {
                id: statusType.reference,
                name: statusType.name,
                states: statusType.states,
                stateDescription: state !== null ? state.description : null,
                stateId: state !== null ? state.id : null,
                value: ''
            };
        },
        addSituationOption() {
            let value = this.newOption.stateId ? this.newOption.stateId : this.newOption.value;
            if (!value) {
                return;
            }
            this.$emit('add-situation-option', this.newOption.id, value);
        },
        statusTypeByReference(reference) {
            return this.statusTypes.find(ele => ele.reference === reference);
        },
        getSituationValue(statusTypeReference, value) {
            let statusType = this.statusTypes.find(ele => ele.reference === statusTypeReference);

            if (this.valueIsUuid(value)) { // UUID
                let state = statusType.states.find(ele => ele.id === value);

                if(state) {
                    return (state.description || 'unknown') + ' - ' + (state.min === state.max ? state.min : state.min + ' - ' + state.max);
                }

                return statusType.states.find(ele => ele.id === value).description || '';
            }

            return value;
        },
        valueIsUuid(value) {
            return value.includes('-') && value.length === 36;
        },
        getStateIcon(statusTypeReference, stateId) {
            let statusType = this.statusTypes.find(ele => ele.reference === statusTypeReference);
            let state = statusType.states.find(ele => ele.id === stateId);
            return state.image || '';
        }
    },
    watch: {
        situation() {
            this.resetNewOption();
        },
        statusTypes() {
            this.resetNewOption();
            this.$emit('clear-situation-option');
        }
    },
    mounted() {
        this.resetNewOption();
    }
};
</script>
