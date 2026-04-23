<!--suppress HtmlUnknownAttribute, XmlInvalidId -->
<template>
    <div>
        <template v-if="processes.length || licensesProcesses.length">
            <div class="form-group">
                <label for="template">Vorlage</label>
                <select class="form-control" v-model="process.template">
                    <option value="">Keine Vorlage</option>
                    <optgroup label="Benutzer-Prozesse" v-if="processes.length">
                        <option :value="process.full_namespace" v-for="process in processes">
                            {{ process.title }} - {{ process.full_namespace }}
                        </option>
                    </optgroup>
                    <optgroup label="Benutzer-Lizenzen" v-if="licensesProcesses.length && process.namespace === namespaces.user">
                        <option :value="licensedProcess.full_namespace" v-for="licensedProcess in licensesProcesses">
                            {{ licensedProcess.title }} - {{ licensedProcess.full_namespace }}
                        </option>
                    </optgroup>
                    <optgroup label="Organisation-Prozesse"
                              v-if="organisationNamespaces.includes(process.namespace) && organisationProcesses.length">
                        <option :value="process.full_namespace" v-for="process in organisationProcesses">
                            {{ process.title }} - {{ process.full_namespace }}
                        </option>
                    </optgroup>
                    <optgroup label="Organisation-Lizenzen"
                              v-if="organisationNamespaces.includes(process.namespace) && organisationLicensesProcesses.length && process.namespace !== namespaces.user">
                        <option :value="licensedProcess.full_namespace" v-for="licensedProcess in organisationLicensesProcesses">
                            {{ licensedProcess.title }} - {{ licensedProcess.full_namespace }}
                        </option>
                    </optgroup>
                </select>
                <small class="text-muted">Die aktuellste, fertiggestellte Version des Prozesses wird als Vorlage genutzt.</small>
                <div v-for="error in (errors.template || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <hr class="my-3">
        </template>
        <div class="form-group">
            <label for="title">Titel</label>
            <input type="text"
                   id="title"
                   v-model="process.title"
                   class="form-control"
                   name="title"
                   aria-describedby="titleHelp"
                   placeholder=""
                   @input="createIdentifier">
            <div v-for="error in (errors.title || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <div class="form-group">
            <label for="title">Namespace & Identifikation</label>
            <div class="input-group">
                <select class="custom-select w-40" v-model="process.namespace">
                    <optgroup label="Benutzer">
                        <option :value="namespaces.user">{{ namespaces.user }}</option>
                    </optgroup>
                    <optgroup label="Organisationen" v-if="organisationNamespaces.length">
                        <option :value="organisation" v-for="organisation in organisationNamespaces">
                            {{ organisation }}
                        </option>
                    </optgroup>
                </select>
                <input type="text" class="form-control w-60" v-model="process.identifier"/>
            </div>
            <small class="text-muted">Legt die Zugehörigkeit und einzigartige Identifikation des Prozesses fest.</small>
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
            <textarea class="form-control" name="description" id="description" v-model="process.description" rows="2"></textarea>
            <small class="form-text text-muted">
                Prozesse mit einer Beschreibung werden besser gefunden.
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
        processes: Array,
        licensesProcesses: Array,
        organisations: Object | Array
    },
    data() {
        return {
            error: null,
            errors: [],
            errorMessage: '',
            loading: false,
            process: {
                title: '',
                namespace: this.selectedNamespace || '',
                identifier: '',
                description: '',
                tags: [],
                template: ''
            }
        };
    },
    computed: {
        organisationNamespaces() {
            return Object.keys(this.organisations);
        },
        organisationProcesses() {
            if (!this.organisationNamespaces.includes(this.process.namespace)) {
                return [];
            }

            return this.organisations[this.process.namespace].processes || [];
        },
        organisationLicensesProcesses() {
            if (!this.organisationNamespaces.includes(this.process.namespace)) {
                return [];
            }

            return this.organisations[this.process.namespace].licensesProcesses || [];
        }
    },
    methods: {
        submit() {
            let that = this;

            this.error = false;
            this.errorMessage = '';
            this.errors = [];
            this.loading = true;

            axios.post('/api/processes', this.process)
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
            this.process.tags = tags;
        },
        createIdentifier(e) {
            let value = e.target.value.toLowerCase();

            value = value.replace(/ä/g, 'ae');
            value = value.replace(/ö/g, 'oe');
            value = value.replace(/ü/g, 'ue');
            value = value.replace(/ß/g, 'ss');

            this.process.identifier = value.replace(/[^a-z\d+]+/gi, '-').trim();
        }
    },
    mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        const template = urlParams.get('template');

        if (template) {
            this.process.template = template.replace('_', '/');
        }
    },
    watch: {
        'process.namespace': function () {
            this.process.template = '';
        }
    }
};
</script>
