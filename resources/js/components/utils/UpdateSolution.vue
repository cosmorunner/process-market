<template>
    <div>
        <div class="form-group">
            <label for="title">Name</label>
            <input type="text" id="title" v-model="solution.name" class="form-control" name="title"
                   aria-describedby="titleHelp" placeholder="">
            <div v-for="error in (errors.name || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <div class="form-group">
            <label for="description">Beschreibung</label>
            <textarea class="form-control" name="description" id="description" v-model="solution.description"
                      rows="5"></textarea>
            <small class="form-text text-muted">
                Lösungen mit einer Beschreibung werden eher gefunden.
            </small>
            <div v-for="error in (errors.description || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <AutocompleteTags @tags-changed="onTagsChanged" :value="solution.tags.join(';')"/>
        <div class="form-group">
            <label for="description">Sichtbarkeit</label>
            <div class="alert alert-primary" role="alert">
                Bei versteckter oder öffentlicher Sichtbarkeit werden nur jene Lösungs-Versionen angezeigt, bei denen
                alle konfigurierten Prozesse auch mindestens versteckte oder öffentliche Sichtbarkeit haben.
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="visibility" id="visibilityPrivate" value="0"
                       :checked="solution.visibility === null || solution.visibility === 0"
                       v-model="solution.visibility">
                <label class="form-check-label" for="visibilityPrivate">
                    Privat<span class="text-muted"> - Nur der Eigentümer (Benutzer oder Organisation) kann die Lösung sehen. Die Lösung wird nicht gelistet.</span>
                </label>
            </div>
            <template v-if="hasPublishedVersion">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="visibility" id="visibilityHidden" value="1"
                           :checked="solution.visibility === 1" v-model="solution.visibility">
                    <label class="form-check-label" for="visibilityHidden">
                        Versteckt<span class="text-muted"> - Die Lösung wird nicht gelistet, ist aber per URL öffentlich aufrufbar. Aktuell nur mit "Open-Source"-Lizenz möglich.</span>
                    </label>
                </div>
                <div class="form-check disabled">
                    <input class="form-check-input" type="radio" name="visibility" id="visibilityPublic" value="2"
                           :checked="solution.visibility === 2" v-model="solution.visibility">
                    <label class="form-check-label" for="visibilityPublic">
                        Öffentlich<span
                        class="text-muted"> - Die Lösung ist öffentlich sichtbar und wird gelistet.</span>
                    </label>
                </div>
            </template>
            <template v-else>
                <div class="alert alert-light py-0" role="alert">
                    Die Lösung hat noch keine fertiggestellte Version und kann daher nicht öffentlich angezeigt werden.
                </div>
            </template>
            <div v-for="error in (errors.visibility || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <div class="d-flex justify-content-between">
                    <span class="text-danger">{{ errorMessage }}</span>
                    <button :disabled="loading" class="btn btn-sm btn-success" @click="submit">Speichern</button>
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
        solutionProp: Object,
        locals: Object,
        urls: Object
    },
    data() {
        return {
            error: null,
            errors: [],
            errorMessage: '',
            loading: false,
            solution: this.solutionProp
        };
    },
    computed: {
        ...mapGetters([
            'flash_messages'
        ]),
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

            axios.patch(this.urls.update_solution, this.solution)
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
            this.solution.tags = tags;
        }
    }
};
</script>
