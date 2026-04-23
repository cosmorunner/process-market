<template>
    <div class="form-group autocomplete-size-normal">
        <label>Tags</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <span v-if="!fetching" class="material-icons">label</span>
                    <img v-if="fetching" src="/img/loading.gif" alt="Loading" width="14.4" height="14.4"/>
                </span>
            </div>
            <vue-tags-input v-model="tag" :tags="tags" :autocomplete-items="autocompleteItems"
                            :autocomplete-min-length="1" :add-only-from-autocomplete="false"
                            :maxlength="20" :max-tags="3" :placeholder="''" @tags-changed="update"
                            :save-on-key="[13, ';']" :disabled="readonly"/>
        </div>
        <small class="text-muted">Tag mit "Enter" bestätigen. Maximal drei Tags.</small>
        <input type="hidden" v-model="tag" />
        <div class="invalid-feedback d-block" v-for="error in httpErrors">{{ error }}</div>
    </div>
</template>

<script>
import VueTagsInput from '@johmun/vue-tags-input';

export default {
    components: {
        VueTagsInput
    },
    props: {
        value: {
            type: String,
            default: ''
        },
        readonly: {
            type: Boolean,
            default: false
        },
    },
    data() {
        let tags = (this.value || '').split(';').filter(item => item !== '').map(function (item) {
            return {
                text: item,
                value: item
            }
        })

        return {
            fetching: false,
            httpErrors: [],
            autocompleteItems: [],
            debounce: null,
            tags: tags,
            tag: '',
            endpoint: '/api/tags'
        }
    },
    watch: {
        tag: 'initItems',
    },
    methods: {
        // Beim Hinzufügen/Löschen eines Tags
        update(newTags) {
            this.addHttpAutocompleteItems();
            this.$emit('tags-changed', newTags.map(item => (item.text || item.value).trim().toLowerCase()))
        },
        // Wenn Sich die Eingabe verändert.
        initItems() {
            this.httpErrors = []

            // Manualle Festlegung der Items und Items per Http mergen.
            this.addHttpAutocompleteItems();
        },
        addHttpAutocompleteItems: function () {
            if (this.tag.length < 2) {
                return
            }

            clearTimeout(this.debounce)

            this.debounce = setTimeout(() => {
                this.fetching = true
                axios.get(this.endpoint, {
                    params: {query: this.tag}
                }).then(response => {
                    this.fetching = false
                    this.autocompleteItems = [...response.data.map(item => {
                        return {
                            text: item.name,
                            color: item.color,
                            value: item.name
                        }
                    })]
                }).catch(error => {
                    this.fetching = false
                    this.httpErrors = [error]
                })
            }, 300)
        }
    },
}
</script>
