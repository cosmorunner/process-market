<!--suppress HtmlUnknownAttribute, XmlInvalidId -->
<template>
    <div>
        <div class="form-group">
            <label for="title">Name</label>
            <input type="text"
                   id="title"
                   v-model="solution.name"
                   class="form-control"
                   name="title"
                   aria-describedby="titleHelp"
                   placeholder=""
                   @input="createIdentifier">
            <div v-for="error in (errors.name || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <div class="form-group">
            <label for="title">Namespace & Identifikation</label>
            <div class="input-group">
                <select class="custom-select w-40" v-model="solution.namespace">
                    <optgroup label="Benutzer">
                        <option :value="namespaces.user">{{ namespaces.user }}</option>
                    </optgroup>
                    <optgroup label="Organisationen">
                        <option :value="organisation" v-for="organisation in namespaces.organisations">
                            {{ organisation }}
                        </option>
                    </optgroup>
                </select>
                <input type="text" class="form-control w-60" v-model="solution.identifier"/>
            </div>
            <small class="text-muted">Legt die Zugehörigkeit und einzigartige Identifikation der Lösung fest.</small>
            <small class="text-muted d-block">Identifikation: Nur a-z, 0-9 und Bindestrich. Mit einem Buchstaben
                beginnen.</small>
            <div v-for="error in (errors.namespace || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
            <div v-for="error in (errors.identifier || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <div class="form-group">
            <label for="description">Beschreibung</label>
            <textarea class="form-control" name="description" id="description" v-model="solution.description"
                      rows="2"></textarea>
            <small class="form-text text-muted">
                Lösungen mit einer Beschreibung werden besser gefunden.
            </small>
            <div v-for="error in (errors.description || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <AutocompleteTags @tags-changed="onTagsChanged"/>
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between">
                    <span class="text-danger">{{ errorMessage }}</span>
                    <button type="submit" :disabled="loading" class="btn btn-sm btn-success" @click="submit">Erstellen
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import AutocompleteTags from "./AutocompleteTags";

export default {
    components: {AutocompleteTags},
    props: {
        namespaces: Object,
        selectedNamespace: String,
    },
    data() {
        return {
            error: null,
            errors: [],
            errorMessage: '',
            loading: false,
            solution: {
                name: '',
                description: '',
                namespace: this.selectedNamespace || '',
                identifier: '',
                tags: [],
            }
        };
    },
    methods: {
        submit() {
            let that = this;

            this.error = false;
            this.errorMessage = '';
            this.errors = [];
            this.loading = true;

            axios.post('/api/solutions', this.solution)
                .then(function (response) {
                    that.loading = false;
                    if (response.status === 201) {
                        window.location.href = response.data.redirect;
                    }
                })
                .catch((error) => {
                    that.error = true;
                    that.loading = false;
                    that.errorMessage = error.response.data.message;
                    that.errors = error.response.data.errors || [];
                });
        },
        onTagsChanged(tags) {
            this.solution.tags = tags;
        },
        createIdentifier(e) {
            let value = e.target.value.toLowerCase();

            value = value.replace(/ä/g, 'ae');
            value = value.replace(/ö/g, 'oe');
            value = value.replace(/ü/g, 'ue');
            value = value.replace(/ß/g, 'ss');

            this.solution.identifier = value.replace(/[^a-z\d+]+/gi, '-').trim();
        }
    }
};
</script>
