<!--suppress HtmlUnknownAttribute, XmlInvalidId -->
<template>
    <div class="container">
        <div class="row d-flex justify-content-center align-items-end">
            <div class="col-12 col-lg-8">
                <div class="row">
                    <div class="col">
                        <div>
                            <h3>Lizenzen {{ existingSolutionLicense ? 'aktualisieren' : 'kaufen' }}</h3>
                        </div>
                    </div>
                </div>
                <hr class="my-3"/>
                <div class="row">
                    <div class="col-12" v-if="unavailableVersion">
                        <div class="alert alert-info" role="alert">
                            Die angefragte Version ist nicht verfügbar. Aktuellste Version:
                            {{ latestPublicSolutionVersion.version }}
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <p><b>{{
                                    solution.name + ' - ' + solution.full_namespace + '@' + latestPublicSolutionVersion.version
                                }}</b></p>
                        </div>
                        <div :class="'card bg-light ' + (!existingSolutionLicense ? 'border-success' : 'border-info')">
                            <div class="card-header">
                                <span class="material-icons text-success">balance</span>
                                <span>Mixed</span>
                                <span class="badge badge-success" v-if="!existingSolutionLicense">Ausgewählt</span>
                                <span class="badge badge-info" v-else>Empfänger besitzt diese Lizenz bereits.</span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Erfordert für jeden Prozess eine Lizenz. Uneingeschränkte Nutzung
                                    der Live-Demo und Vervielfältigung der Lösungs-Konfiguration.</p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <a class="btn btn-link px-0" target="_blank" :href="urls.licenses_mixed">Details anzeigen</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <div class="mb-3">
                            <p><b>1. Empfänger der Lizenzen wählen:</b></p>
                        </div>
                        <div class="form-group">
                            <select class="form-control form-group" v-model="receiver">
                                <option value="">Bitte wählen...</option>
                                <optgroup label="Benutzer" v-if="solution.namespace !== userNamespace">
                                    <option :value="userNamespace">Sie - {{ userNamespace }}</option>
                                </optgroup>
                                <optgroup label="Organisation" v-if="filteredOrganisations.length">
                                    <option :value="organisation.namespace"
                                            v-for="organisation in filteredOrganisations">
                                        {{ organisation.name }} - {{ organisation.namespace }}
                                    </option>
                                </optgroup>
                            </select>
                            <div v-for="error in (errors.receiver || [])">
                                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <template v-if="receiver && licensesLoaded && latestPublicSolutionVersion.processes.length">
                    <div class="row">
                        <div class="col-12" v-if="existingSolutionLicense">
                            <div class="alert alert-info mb-0" role="alert">
                                <span>Der Empfänger besitzt bereits eine Lizenz zu dieser Lösung.</span>
                            </div>
                        </div>
                        <div class="col-12 mt-5">
                            <div class="mb-3">
                                <p><b>2. Prozess-Lizenzen wählen:</b></p>
                            </div>
                            <div class="form-group">
                                <div class="row mt-2" v-for="process in latestPublicSolutionVersion.processes">
                                    <div class="col-6 col-form-label">
                                        <span>{{ process.title + ' - ' + process.full_namespace }}</span>
                                    </div>
                                    <div class="col-5 ">
                                        <select class="custom-select w-100" id="inputGroupSelect01"
                                                @change="onSelectLicense(process.full_namespace, $event)"
                                                v-if="!ownerHasLicense(process.full_namespace) && process.namespace !== receiver">
                                            <option selected>Bitte wählen...</option>
                                            <option :value="license.level" v-for="license in getPublicLicenseOptions(process.license_options)">
                                                {{ license.level_name }}
                                            </option>
                                        </select>
                                        <template v-if="ownerHasLicense(process.full_namespace)">
                                            <span class="text-info d-block py-2">
                                                <span class="material-icons">done</span>
                                                <span>Empfänger besitzt bereits eine Lizenz.</span>
                                            </span>
                                        </template>
                                        <template v-if="process.namespace === receiver">
                                            <span class="text-info d-block py-2">
                                                <span class="material-icons">done</span>
                                                <span>Empfänger ist der Author.</span>
                                            </span>
                                        </template>

                                    </div>
                                    <div class="col-1 text-right col-form-label">
                                        <template v-if="getSelectedLicense(process.full_namespace)">
                                            <span>€ {{
                                                    getSelectedLicense(process.full_namespace).license.price
                                                }}</span>
                                        </template>
                                    </div>
                                </div>

                                <div v-for="error in (errors.process_licenses || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row mt-4 d-flex">
                        <div class="col-12">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="agbs" v-model="accept">
                                <label class="form-check-label" for="agbs">Ich akzeptiere die gewählten <a
                                    target="_blank" :href="urls.licenses">Lizenzbedingungen der Prozesse und der
                                    Lösung</a>.</label>
                            </div>
                            <div v-for="error in (errors.accept || [])">
                                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                            </div>
                        </div>
                    </div>
                </template>

                <hr/>
                <template v-if="receiver && licensesLoaded">
                    <div class="row pt-3 d-flex">
                        <div class="col-4">
                            Kosten
                        </div>
                        <div class="col-8 text-right">
                            <span>€ 0</span>
                        </div>
                    </div>
                    <hr/>
                </template>
                <div class="row mt-3 justify-content-between">
                    <div class="col-8">
                        <span v-if="error" class="text-danger">{{ errorMessage }}</span>
                    </div>
                    <div class="col-4 text-right">
                        <button class="btn btn-outline-danger" disabled v-if="error">Error</button>
                        <button class="btn btn-primary" v-if="!loading && !error" @click="purchase" :disabled="purchaseDisabled">
                            <span>Lizenz(en) {{ existingSolutionLicense ? 'aktualisieren' : 'kaufen' }}</span>
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
        solution: Object,
        latestPublicSolutionVersion: Object,
        queryNamespace: String | null,
        unavailableVersion: String,
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
            receiver: '',
            existingProcessLicenses: [],
            existingSolutionLicense: null,
            selectedProcessLicenses: [],
            licensesLoaded: false,
        };
    },
    computed: {
        filteredOrganisations() {
            return this.organisations.filter(ele => ele.namespace !== this.solution.namespace);
        },
        organisationNamespaces() {
            return this.filteredOrganisations.map(ele => ele.namespace);
        },
        purchaseDisabled() {
            return !this.receiver || (this.existingSolutionLicense && !this.selectedProcessLicenses.length) || !this.accept;
        },
    },
    methods: {
        onSelectLicense(fullNamespace, event) {
            let level = event.target.value;
            let process = this.latestPublicSolutionVersion.processes.find(ele => ele.full_namespace === fullNamespace);

            if (!process) {
                this.removeSelectedLicense(fullNamespace);

                return;
            }

            let licenseOption = process.license_options.find(ele => ele.level === level);

            if (!licenseOption) {
                this.removeSelectedLicense(fullNamespace);

                return;
            }


            this.selectedProcessLicenses = [
                ...this.selectedProcessLicenses,
                {
                    full_namespace: fullNamespace,
                    license: licenseOption
                }
            ];
        },
        purchase() {
            let that = this;

            that.errors = [];
            that.errorMessage = '';
            that.error = null;
            that.loading = true;

            axios.post(this.urls.store_license, {
                accept: this.accept,
                resource_id: that.solution.id,
                resource_version: that.latestPublicSolutionVersion.version,
                resource_type: 'App\\Models\\Solution',
                license: {level: 'mixed'},
                receiver: this.receiver,
                process_licenses: this.selectedProcessLicenses
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
        },
        fetchProcessLicenses() {
            if (!this.receiver) {
                this.licensesLoaded = false;
                this.existingProcessLicenses = [];

                return;
            }

            let that = this;

            that.errors = [];
            that.errorMessage = '';
            that.error = null;
            that.loading = true;
            that.selectedProcessLicenses = [];

            axios.get(this.urls.get_process_licenses.replace('#ownerNamespace#', this.receiver)).then(function (response) {
                that.existingProcessLicenses = response.data.process_licenses || [];
                that.existingSolutionLicense = response.data.solution_license || null;
                that.licensesLoaded = true;
                that.loading = false;
            }).catch(function (error) {
                that.loading = false;
                that.errors = error.response.data.errors || {};
                that.errorMessage = error.response.data.message;

                if (error.response.status !== 422) {
                    that.error = true;
                }
            });
        },
        removeSelectedLicense(fullNamespace) {
            this.selectedProcessLicenses = [...this.selectedProcessLicenses].filter((item) => item.full_namespace !== fullNamespace);
        },
        ownerHasLicense(fullNamespace) {
            return this.existingProcessLicenses.find(ele => ele.resource_full_namespace === fullNamespace);
        },
        getSelectedLicense(fullNamespace) {
            return this.selectedProcessLicenses.find(ele => ele.full_namespace === fullNamespace);
        },
        getPublicLicenseOptions(licenseOptions){
            return licenseOptions.filter(ele => ele.level !== 'private')
        }
    },
    watch: {
        receiver: {
            handler() {
                this.fetchProcessLicenses();
            },
            deep: true
        },
    },
    mounted() {
        let namespaces = [
            this.userNamespace,
            ...this.organisationNamespaces
        ];

        // Falls in der URL ein "namespace"-Parameter angegeben ist, wird für diesen Namespace bereits die Lizenzen geladen.
        if (this.queryNamespace && namespaces.includes(this.queryNamespace) && this.solution.namespace !== this.queryNamespace) {
            this.receiver = this.queryNamespace;
            this.fetchProcessLicenses();
        }
    }
};
</script>
