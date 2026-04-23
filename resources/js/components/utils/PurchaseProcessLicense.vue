<!--suppress HtmlUnknownAttribute, XmlInvalidId -->
<template>
    <div class="container">
        <div class="row d-flex justify-content-center align-items-end">
            <div class="col-12 col-lg-8">
                <div class="row">
                    <div class="col">
                        <div>
                            <h3>Lizenzkauf</h3>
                        </div>
                    </div>
                </div>
                <hr class="my-3"/>
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <p>Lizenz Optionen für Prozess: <b>{{ process.title }} - {{ process.full_namespace }}</b>
                            </p>
                        </div>
                        <div :class="'card bg-light ' + (openSourceSelected ? 'border-success' : '')">
                            <div class="card-header">
                                <span class="material-icons text-danger">favorite</span>
                                <span>Open-Source</span>
                                <span class="badge badge-success" v-if="openSourceSelected">Ausgewählt</span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Kostenlos. Uneingeschränkte Rechte zur Nutzung, Bearbeitung und Vervielfältigung.</p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <a class="btn btn-link px-0" target="_blank" :href="urls.licenses_open_source">Details anzeigen</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col">
                        <div class="mb-3">
                            <p>Wählen Sie den Empfänger der Lizenz.</p>
                        </div>
                        <div class="form-group">
                            <select class="form-control form-group" v-model="receiver">
                                <option value="">Bitte wählen...</option>
                                <optgroup label="Benutzer" v-if="process.namespace !== userNamespace">
                                    <option :value="userNamespace">Sie - {{ userNamespace }}</option>
                                </optgroup>
                                <optgroup label="Organisation" v-if="filteredOrganisations.length">
                                    <option :value="organisation.namespace" v-for="organisation in filteredOrganisations">
                                        {{ organisation.name }} - {{ organisation.namespace }}
                                    </option>
                                </optgroup>
                            </select>
                            <span class="text-danger" v-if="process.namespace === userNamespace && !filteredOrganisations.length">Es besteht kein gültiger Empfänger.</span>
                            <div v-for="error in (errors.receiver || [])">
                                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 d-flex">
                    <div class="col-12">
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="agbs" v-model="accept">
                            <label class="form-check-label" for="agbs">
                                <span>Ich akzeptiere die</span>
                                <a target="_blank" :href="urls.license_open_source">Lizenzbedingungen (MIT-Lizenz).</a>
                            </label>
                        </div>
                        <div v-for="error in (errors.accept || [])">
                            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row mt-2 py-2 d-flex" v-if="openSourceSelected">
                    <div class="col text-right">
                        <span><b>Kostenlos</b></span>
                    </div>
                </div>
                <div class="row mt-2 pt-3 d-flex" v-else>
                    <div class="col-4">
                        Kosten
                    </div>
                    <div class="col-8 text-right">
                        <span>€ 0</span>
                    </div>
                </div>
                <hr/>
                <div class="row mt-3 justify-content-between">
                    <div class="col-8">
                        <span v-if="error" class="text-danger">{{ errorMessage }}</span>
                    </div>
                    <div class="col-4 text-right">
                        <button class="btn btn-outline-danger" disabled v-if="error">Error</button>
                        <button class="btn btn-primary" v-if="!loading && !error" @click="purchase">Lizenz erhalten
                        </button>
                        <button class="btn btn-outline-primary" v-if="loading">
                            <img src="/img/loading.gif" alt="Loading" width="18" height="18"/>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        urls: Object,
        userNamespace: String,
        organisations: Array,
        process: Object,
        level: {
            required: false,
            type: String,
            default: ''
        }
    },
    data() {
        return {
            error: null,
            errors: [],
            errorMessage: '',
            loading: false,
            accept: false,
            license: {
                level: this.level
            },
            receiver: ''
        };
    },
    computed: {
        openSourceSelected() {
            return this.license.level === 'open-source';
        },
        filteredOrganisations() {
            return this.organisations.filter(ele => ele.namespace !== this.process.namespace);
        }
    },
    methods: {
        purchase() {
            let that = this;

            that.errors = [];
            that.errorMessage = '';
            that.error = null;
            that.loading = true;

            axios.post(this.urls.store_license, {
                accept: this.accept,
                resource_id: that.process.id,
                resource_type: 'App\\Models\\Process',
                license: this.license,
                receiver: this.receiver
            }).then(function (response) {
                window.location.href = response.data.redirect_url;
            }).catch(function (error) {
                that.loading = false;
                that.errors = error.response.data.errors || {};
                that.errorMessage = error.response.data.message;

                if (error.response.status !== 422) {
                    that.error = true;
                }
            });
        }
    },
};
</script>
