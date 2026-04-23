<!--suppress HtmlUnknownAttribute, XmlInvalidId -->
<template>
    <div>
        <div v-if="!platformStored">
            <div>
                <p>Mit der Allisa Plattform können Sie die in der Prozessfabrik digitalisierten Prozesse nutzen.</p>
                <ul class="list-group list-group-flush text-muted">
                    <li class="list-group-item">4 Wochen kostenlos testen.</li>
                    <li class="list-group-item">Kein Zahlungsmittel erforderlich.</li>
                    <li class="list-group-item">Keine automatische Verlängerung.</li>
                </ul>
            </div>
            <div class="form-group bg-light mb-0 mt-4 p-2">
                <label>Name</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">https://</span>
                    </div>
                    <input type="text" class="form-control" @input="onInputIdentifier" :value="identifier">
                    <div class="input-group-append">
                        <span class="input-group-text">.example.com</span>
                    </div>
                    <div class="input-group-append">
                    <span class="input-group-text bg-white" style="width:43px;">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"
                             v-if="checkingExistance">
                          <span class="sr-only">...</span>
                        </div>
                        <span class="material-icons text-success"
                              v-if="!checkingExistance && !exists && !invalid && !error">done</span>
                        <span class="material-icons text-danger"
                              v-if="!checkingExistance && (exists || invalid || error)">close</span>
                    </span>
                    </div>
                </div>
                <div v-for="error in (errors.identifier || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
                <div v-if="exists">
                    <div class="invalid-feedback d-block mt-0">Der Name ist bereits vergeben.</div>
                </div>
                <div v-if="invalid">
                    <div class="invalid-feedback d-block mt-0">Mindestens 3 Zeichen. Nur a-z, 0-9 und Bindestrich. Mit
                        Buchstaben beginnen.
                    </div>
                </div>
                <div v-if="!invalid && !exists">
                    <div class="valid-feedback d-block mt-0">Alles klar!</div>
                </div>
            </div>
            <div class="form-group mt-3">
                <div class="row">
                    <div class="col-6">
                        <label for="first_name">Vorname</label>
                        <input type="text" class="form-control form-control-sm" id="first_name" required
                               v-model="first_name">
                        <div v-for="error in (errors.first_name || [])">
                            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="last_name">Nachname</label>
                        <input type="text" class="form-control form-control-sm" id="last_name" required
                               v-model="last_name">
                        <div v-for="error in (errors.last_name || [])">
                            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mt-4">
                <div class="row">
                    <div class="col-2">
                        <input type="checkbox" name="terms" id="terms" class="form-control" v-model="terms">
                        <label for="terms" class="col-md-4 col-form-label text-md-right"></label>
                    </div>
                    <div class="col-10">
                    <span class="text-muted">Ich akzeptiere die <a :href="allisaTerms" target="_blank">AGBs</a>
                        und <a :href="allisaPrivacy" target="_blank">Datenschutzbestimmungen</a> der Allisa Software GmbH.</span>
                    </div>
                </div>
                <div v-for="error in (errors.terms || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="d-flex justify-content-end">
                        <button type="submit" :disabled="checkingExistance || exists || !identifier || invalid"
                                class="btn btn-sm btn-success" @click="submit" v-if="!storingPlatform && !error">
                            Erstellen
                        </button>
                        <button class="btn btn-sm btn-danger" disabled v-if="!storingPlatform && error">Ungültige Daten
                        </button>
                        <div class="spinner-border spinner-border-sm text-primary" role="status"
                             v-if="storingPlatform && !error" style="height:29px; width: 29px;">
                            <span class="sr-only">...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="platformStored">
            <div class="col">
                <span class="d-block">
                    <span class="material-icons text-success mi-2x">done</span>
                    <span>Wir haben Ihnen eine <b>E-Mail mit den Login-Informationen</b> gesendet.</span>
                </span>
                <span class="d-block mt-2">
                    <span class="material-icons text-success mi-2x">done</span>
                    <span>Die Allisa Plattform wurde bei <b>Ihrem Profil registriert</b>.</span>
                </span>
                <hr>
                <span class="d-block mt-2">
                    <a :href="systemsRoute">Zu den Allisa Plattformen</a>
                </span>
                <span class="d-block mt-2">
                    <a :href="profileRoute">Zum Profil</a>
                </span>

            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        identifierProp: String,
        searchEndpoint: String,
        storeEndpoint: String,
        systemsRoute: String,
        profileRoute: String,
        allisaPrivacy: String,
        allisaTerms: String
    },
    data() {
        return {
            error: null,
            errors: {},
            errorMessage: '',
            checkingExistance: false,
            storingPlatform: false,
            invalid: false,
            exists: false,
            identifier: this.identifierProp,
            first_name: '',
            last_name: '',
            platformStored: false,
            debounce: null,
            terms: false
        };
    },
    methods: {
        submit() {
            let that = this;

            this.clearError();
            this.storingPlatform = true;

            let data = {
                identifier: this.identifier,
                terms: this.terms,
                first_name: this.first_name,
                last_name: this.last_name
            };

            axios.post(this.storeEndpoint, data).then(function () {
                that.storingPlatform = false;
                that.platformStored = true;
            }).catch((error) => {
                that.error = true;
                that.storingPlatform = false;
                that.errorMessage = error.response.data.message;
                that.errors = error.response.data.errors || [];
            });
        },
        onInputIdentifier(e) {
            clearTimeout(this.debounce);

            this.identifier = e.target.value.trim();
            this.invalid = false;

            this.clearError();

            // Falls der Identifier kürzer oder gleich 2 Zeichen ist.
            if (this.identifier === '' || this.identifier.length <= 2) {
                this.invalid = true;
                return;
            }

            this.debounce = setTimeout(() => {
                this.checkingExistance = true;

                this.fetchPlatformExists();
            }, 300);
        },
        fetchPlatformExists() {
            axios.get(this.searchEndpoint + '/' + this.identifier).then(response => {
                this.checkingExistance = false;
                this.exists = response.data.exists;

            }).catch(() => {
                this.invalid = true;
                this.checkingExistance = false;
            });
        },
        clearError() {
            this.error = false;
            this.errorMessage = '';
            this.errors = {};
        }
    },
    mounted() {
        this.fetchPlatformExists();
    },
    watch: {
        terms() {
            this.clearError();
        },
        first_name() {
            this.clearError();
        },
        last_name() {
            this.clearError();
        }
    }
};
</script>
