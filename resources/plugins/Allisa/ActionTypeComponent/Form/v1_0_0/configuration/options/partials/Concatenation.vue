<template>
    <div class="input-group input-group-sm mb-2 ">
        <div class="input-group-append">
            <button class="btn btn-outline-primary btn-sm dropdown-toggle rounded-left" type="button"
                    id="concatDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    :disabled="!editable">
                <span v-if="!concat.length">Deaktiviert</span>
                <span v-else-if="concat.length && (concat[0] === '' || concat[0] === null)">Leere Zeichenkette</span>
                <span v-else-if="concat.length && concat[0] === '_blank_space_'">Leerzeichen</span>
                <span v-else>"{{ concat[0].replaceAll('_blank_space_', ' ') }}"</span>
            </button>
            <div class="dropdown-menu scrollable-dropdown" aria-labelledby="concatDropdown">
                <button type="button" class="dropdown-item" v-for="option in concatOptions"
                        @click="onChangeConcatSplit(option.key)">
                    {{ option.value }}
                </button>
            </div>
        </div>
        <div class="input-group-append" v-if="concat.length > 1">
            <button
                class="btn btn-sm dropdown-toggle btn-outline-secondary"
                type="button" id="firstConcatDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                :disabled="!editable">
                {{ (aliasLabels[concat[1]] || concat[1]) || ('1. Datenfeld') }}
            </button>
            <div class="dropdown-menu scrollable-dropdown" aria-labelledby="firstConcatDropdown">
                <button type="button" v-for="alias in selectableAliases" class="dropdown-item"
                        @click="onChangeConcatField(1, alias)">{{ aliasLabels[alias] || alias }}
                </button>
            </div>
        </div>
        <div class="input-group-append" v-if="concat.length > 1">
            <button
                class="btn btn-sm dropdown-toggle btn-outline-secondary"
                type="button" id="secondConcatDropdown" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false" :disabled="!editable">
                {{ (aliasLabels[concat[2]] || concat[2]) || ('2. Datenfeld') }}
            </button>
            <div class="dropdown-menu scrollable-dropdown" aria-labelledby="secondConcatDropdown">
                <button class="dropdown-item" type="button" @click="onChangeConcatField(2, '')">Ohne Wert</button>
                <div class="dropdown-divider"></div>
                <button v-for="alias in selectableAliases" class="dropdown-item" type="button"
                   @click="onChangeConcatField(2, alias)">{{ aliasLabels[alias] || alias }}</button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        editable: Boolean,
        concat: Array,
        selectableAliases: {
            type: Array,
            default: function() {
                return  [];
            }
        },
        aliasLabels: {
            type: Object,
            default: function() {
                return  {};
            }
        },
    },
    data() {
        return {
            concatOptions: [
                {
                    key: '_blank_space_',
                    value: 'Leerzeichen'
                },
                {
                    key: '-',
                    value: '\"-\"'
                },
                {
                    key: '_blank_space_-_blank_space_',
                    value: '\" - \"'
                },
                {
                    key: '_',
                    value: '\"_\"'
                },
                {
                    key: '/',
                    value: '\"/\"'
                },
                {
                    key: '_blank_space_/_blank_space_',
                    value: '\" / \"'
                }
            ]
        };
    },
    methods: {
        onChangeConcatSplit(split) {
            if (split === null) {
                this.$emit('concat-updated', []);
                return;
            }

            this.$emit('concat-updated', [
                split,
                (this.concat[1] || ''),
                (this.concat[2] || '')
            ]);

        },
        onChangeConcatField(fieldNr, value) {
            let concat = [...this.concat];
            concat[fieldNr] = value;

            this.$emit('concat-updated', concat);
        },
    }
};
</script>