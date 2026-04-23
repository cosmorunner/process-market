<template>
    <div>
        <div class="form-group">
            <label for="title">Titel</label>
            <input id="title" v-model="process.title" aria-describedby="titleHelp" class="form-control" name="title"
                   placeholder="" type="text" :readonly="!editable">
            <div v-for="error in (errors.title || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <div class="form-group">
            <label for="description">Beschreibung</label>
            <textarea id="description" v-model="process.description" class="form-control" name="description" rows="5"
                      :readonly="!editable"></textarea>
            <small class="form-text text-muted">
                Prozesse mit einer Beschreibung werden eher gefunden.
            </small>
            <div v-for="error in (errors.description || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <AutocompleteTags :value="process.tags.join(';')" @tags-changed="onTagsChanged" :readonly="!editable"/>
        <div class="form-group">
            <label for="description">Sichtbarkeit</label>
            <div class="form-check mb-2">
                <input id="visibilityPrivate" v-model="process.visibility"
                       :checked="process.visibility === null || process.visibility === 0" class="form-check-input"
                       name="visibility" type="radio" value="0" @change="onVisibilityChange" :disabled="!editable">
                <label class="form-check-label" for="visibilityPrivate">
                    Privat<span class="text-muted"> - Nur der Eigentümer (Benutzer oder Organisation) kann den Prozess sehen. Der Prozess wird nicht gelistet.</span>
                </label>
            </div>
            <template v-if="hasPublishedVersion">
                <div class="form-check mb-2">
                    <input id="visibilityHidden" v-model="process.visibility" :checked="process.visibility === 1"
                           class="form-check-input" name="visibility" type="radio" value="1"
                           @change="onVisibilityChange" :disabled="!editable">
                    <label class="form-check-label" for="visibilityHidden">
                        Versteckt<span class="text-muted"> - Der Prozess wird nicht gelistet, ist aber per URL öffentlich aufrufbar. Nur mit "Open-Source"-Lizenz oder "Auf Anfrage" Auswahl möglich.</span>
                    </label>
                </div>
                <div class="form-check disabled">
                    <input id="visibilityPublic" v-model="process.visibility" :checked="process.visibility === 2"
                           class="form-check-input" name="visibility" type="radio" value="2"
                           @change="onVisibilityChange" :disabled="!editable">
                    <label class="form-check-label" for="visibilityPublic">
                        Öffentlich<span class="text-muted"> - Der Prozess ist öffentlich sichtbar und wird gelistet. Nur mit "Open-Source"-Lizenz oder "Auf Anfrage" Auswahl möglich.</span>
                    </label>
                </div>
            </template>
            <template v-else>
                <div class="alert alert-light py-0" role="alert">
                    Der Prozess hat noch keine fertiggestellte Version und kann daher nicht öffentlich angezeigt werden.
                </div>
            </template>
            <div v-for="error in (errors.visibility || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <span class="d-block mb-3">Lizenz-Optionen</span>
        <div class="dropdown">
            <button id="dropdownMenuButton" aria-expanded="false" aria-haspopup="true"
                    class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown" type="button"
                    :disabled="!editable">
                {{ levelLabel }}
                <span v-if="(process.license_options[0] || {}).level === 'open-source'"
                      class="material-icons text-danger opacity-3" data-placement="top" data-toggle="tooltip"
                      title="High Five!">favorite</span>
            </button>
            <div aria-labelledby="dropdownMenuButton" class="dropdown-menu">
                <button class="dropdown-item" type="button" @click="onChangeLicenseLevel('private')">Privat</button>
                <button class="dropdown-item" type="button" @click="onChangeLicenseLevel('open-source')">Open-Source
                </button>
                <button class="dropdown-item" type="button" @click="onChangeLicenseLevel('no-license')">Auf Anfrage
                </button>
            </div>
        </div>
        <hr/>
        <div v-if="(process.license_options[0] || {}).level === 'private'">
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-1">Benutzer oder Organisationen können Lizenzen nicht eigenständig
                    erwerben.
                </li>
                <li class="list-group-item p-1">Nur in Kombination mit Prozess-Sichtbarkeit "Privat" möglich.</li>
            </ul>
        </div>
        <div v-if="(process.license_options[0] || {}).level === 'open-source'">
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-1">Benutzer können eine kostenlose MIT-Lizenz (Open-Source) erhalten.
                </li>
                <li class="list-group-item p-1">Prozess-Kopien können erstellt, bearbeitet und beliebig lizensiert
                    werden.
                </li>
                <li class="list-group-item p-1">Export zu beliebig vielen Allisa Plattformen.</li>
            </ul>
            <div class="form-group form-check mt-3">
                <input id="agbs" v-model="process.accept_license" class="form-check-input" type="checkbox"
                       :disabled="!editable">
                <label class="form-check-label" for="agbs">Ich stelle meinen Prozess unter die <a
                    :href="urls.license_open_source" target="_blank">MIT-Lizenz
                    (Open-Source)</a>. Der Prozess versteht sich als Software im Sinne der Lizenz.</label>
            </div>
            <div v-for="error in (errors.accept_license || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <div v-if="(process.license_options[0] || {}).level === 'no-license'">
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-1">Benutzer können Lizenzen auf Anfrage erhalten.
                </li>
                <li class="list-group-item p-1">Prozess ist öffentlich sichtbar.
                </li>
                <li class="list-group-item p-1">Es kann keine öffentliche Demo gestartet werden.</li>
            </ul>
        </div>
        <div class="row mt-3" v-if="editable">
            <div class="col">
                <div class="d-flex justify-content-between">
                    <span class="text-danger">{{ errorMessage }}</span>
                    <button :disabled="loading" class="btn btn-sm btn-success" @click="submit">Speichern
                    </button>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <FlashMessages :locals="locals"></FlashMessages>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/utils';
import AutocompleteTags from "./AutocompleteTags";
import FlashMessages from "./FlashMessages";

export default {
    components: {
        AutocompleteTags,
        FlashMessages
    },
    props: {
        hasPublishedVersion: Boolean,
        processProp: Object,
        editable: Boolean,
        locals: Object,
        urls: Object
    },
    data() {
        if (!this.processProp.license_options.length) {
            this.processProp.license_options = [
                {
                    level: 'private',
                    level_options: {}
                }
            ];
        }

        // Falls die Lizenz-Bedingungen bereits akzeptiert wurden.
        if (this.processProp.license_options[0].level === 'open-source') {
            this.processProp.accept_license = true;
        }

        return {
            error: null,
            errors: [],
            errorMessage: '',
            loading: false,
            process: this.processProp
        };
    },
    computed: {
        ...mapGetters([
            'flash_messages'
        ]),
        levelLabel() {
            switch (this.process.license_options[0].level || 'private') {
                case 'private':
                    return 'Privat';
                case 'open-source':
                    return 'Open-Source';
                case 'no-license':
                    return 'Auf Anfrage';
            }
        }
    },
    methods: {
        ...mapActions(reduxActions),
        submit() {
            let that = this;

            this.error = false;
            this.errorMessage = '';
            this.errors = [];
            this.loading = true;

            this.clearFlashMessages();

            axios.patch(this.urls.update_process, this.process)
                .then(() => {
                    this.loading = false;

                    this.setFlashMessage({
                        type: 'success',
                        message: 'Gespeichert!'
                    });
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
        onVisibilityChange(e) {
            let visibility = e.target.value;
            let level = this.process.license_options[0].level;

            if (visibility > 0 && level === 'private') {
                this.process.license_options[0].level = 'open-source';
            }
        },
        onChangeLicenseLevel(level) {
            this.process.license_options[0].level = level;

            if (level === 'private') {
                this.process.visibility = '0';
                this.process.accept_license = false;
            }
        }
    }
};
</script>
