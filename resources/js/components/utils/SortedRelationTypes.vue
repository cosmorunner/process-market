<template>
    <div>
        <label v-if="!hideLabel" for="relationType">{{ label }}</label>
        <select class="form-control form-control-sm" id="relationType" :value="currentValue"
                @change="onChangeRelationType">
            <option value="" v-if="showEmptyOption">Kein Verknüpfungstyp</option>
            <optgroup :label="ns" v-for="(relationTypes, ns) in relationTypeOptions">
                <option v-for="relationType in relationTypes"
                        :value="ns + '::relationType|' + relationType.id">
                    {{ relationType.name }}
                </option>
            </optgroup>
        </select>
        <small class="text-muted" v-if="helperText">{{ helperText }}</small>
    </div>
</template>

<script>
export default {
    props: {
        currentValue: String,
        currentRelationTypeName: String,
        namespace: String,
        nativeRelationTypes: Array,
        externalRelationTypes: Array,
        hideNativeRelationTypes: {
            type: Boolean,
            default: false
        },
        onlyLatest: {
            type: Boolean,
            default: true
        },
        hideLabel: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: 'Verknüpfungstyp'
        },
        showEmptyOption: {
            type: Boolean,
            default: false
        },
        helperText: {
            type: String,
            default: ''
        }
    },
    computed: {
        relationTypeOptions() {
            let native = {
                [this.namespace]: this.nativeRelationTypes.map(ele => ({
                    id: ele.id,
                    name: ele.name,
                    namespace: this.namespace
                }))
            };

            native = {
                ...native,
                ...this.groupedExternalRelationTypes
            };

            // Falls der aktuelle Verknüpfungstyp nicht in den nativen oder externen Verknüpfungstypen existiert,
            // wird dieser hier den Optionen hinzugefügt.
            if (this.currentValue && this.currentRelationTypeName) {
                let namespace = this.currentValue.includes('::') ? this.currentValue.split('::')[0] : this.namespace;
                let id = this.currentValue.split('|')[1];

                if (!native[namespace] || !native[namespace].find(ele => ele.id === id)) {
                    native[namespace] = [
                        ...native[namespace] || [],
                        {
                            id: id,
                            name: this.currentRelationTypeName,
                            namespace: namespace
                        }
                    ];
                }
            }

            return native;
        },
        /**
         * Gruppierte Verknüpfungstypen nach Namespace
         */
        groupedExternalRelationTypes() {
            let grouped = {};

            for (let relationType of this.externalRelationTypes) {
                let namespace = relationType.namespace.split('@')[0];
                let version = relationType.namespace.split('@')[1];

                // Nur Latest ausgeben
                if (this.onlyLatest && version !== 'latest') {
                    continue;
                }

                let key = this.onlyLatest ? namespace : relationType.namespace;

                if (grouped.hasOwnProperty(key)) {
                    grouped[key] = [
                        ...grouped[key],
                        relationType
                    ];
                }
                else {
                    grouped[key] = [relationType];
                }
            }

            return grouped;
        }
    },
    methods: {
        onChangeRelationType(e) {
            // Empty
            if (!e.target.value) {
                this.$emit('update-relation-type', null, null);
                return;
            }

            // Pipe-Notation z.B.: allisa/demo@1.0.0::relationType|55fe103c-c88e-4029-8d4f-b31e9db3738a
            let value = e.target.value;
            let namespace = value.includes('::') ? value.split('::')[0] || '' : '';
            let id = value.split('|')[1] || '';
            let relationType = null;
            let pipeNotation = null;

            // Nativer Verknüpfungstyp
            if (!namespace) {
                relationType = this.relationTypeOptions[this.namespace].find(ele => ele.id === id) || null;
                pipeNotation = 'relationType|' + relationType.id;
            }
            // Externer Verknüpfungstyp
            else {
                relationType = this.relationTypeOptions[namespace].find(ele => ele.id === id) || null;
                pipeNotation = namespace + '::' + 'relationType|' + relationType.id;
            }

            this.$emit('update-relation-type', pipeNotation, relationType.name);
        }
    }
};
</script>

