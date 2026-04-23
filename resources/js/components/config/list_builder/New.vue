<template>
    <div class="row">
        <div v-if="template === 'group_members'" class="col-12">
            <div class="alert alert-warning">
                <span>
                    <b>Hinweis:</b> Benutzer benötigen das jeweilige Gruppen-Recht, die Mitglider einsehen zu dürfen.
                </span>
            </div>
        </div>
        <div v-if="template === 'users'" class="col-12">
            <div class="alert alert-warning">
                <span>
                    <b>Hinweis:</b> Benutzer benötigen das Systemrollen-Recht, Benutzer einsehen zu dürfen.
                </span>
            </div>
        </div>
        <div v-if="template === 'groups'" class="col-12">
            <div class="alert alert-warning">
                <span>
                    <b>Hinweis:</b> Benutzer benötigen das Systemrollen-Recht, Gruppen einsehen zu dürfen.
                </span>
            </div>
        </div>
        <div class="col-8">
            <div class="form-group mb-2">
                <label class="mb-0" for="name">Label</label>
                <input id="name" v-model="name" aria-describedby="name" class="form-control form-control-sm"
                       type="text"/>
                <div v-for="error in (ui.validationErrors.name || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group mb-2">
                <label class="mb-0" for="description">Beschreibung</label>
                <textarea id="description" v-model="description" class="form-control" rows="2"></textarea>
                <div v-for="error in (ui.validationErrors.description || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>

            <div class="form-group mb-2">
                <label class="mb-0" for="slug">Slug</label>
                <input id="slug" v-model="slug" aria-describedby="label" class="form-control form-control-sm"
                       type="text"/>
                <small class="text-muted">Listenkennung in der URL.</small>
                <div v-for="error in (ui.validationErrors.slug || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group mb-0">
                <label class="mb-0" for="slug">Vorlage</label>
                <select v-model="template" class="form-control form-control-sm">
                    <optgroup label="Allgemein">
                        <option value="processes">Prozesse</option>
                        <option value="users">Benutzer</option>
                        <option value="groups">Gruppen</option>
                        <option value="group_members">Gruppen-Mitglieder</option>
                    </optgroup>
                    <optgroup label="Prozess-Elemente">
                        <option value="process_relations">Verknüpfte Prozesse</option>
                        <option value="process_identity_relations">Verknüpfte Prozesse der Prozess-Identität</option>
                        <option value="process_actions">Ausgeführte Aktionen</option>
                        <option value="process_artifacts">Prozess-Dokumente</option>
                        <option value="relation">Rollen</option>
                    </optgroup>
                    <optgroup label="Sonstige">
                        <option value="connector_request">Konnektor-Anfrage</option>
                    </optgroup>
                </select>
                <div v-for="error in (ui.validationErrors.template || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col d-flex justify-content-end">
                    <button class="btn btn-sm btn-outline-danger mr-3" @click="$emit('update-sub-navigation', 'Query')">
                        Abbrechen
                    </button>
                    <button class="btn btn-sm btn-success" @click="onAdd">Hinzufügen</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../../config-utils';
import {reduxActions} from '../../../store/develop-and-config';

export default {
    data() {
        return {
            name: '',
            description: '',
            slug: '',
            template: 'processes',
        };
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onAdd() {
            this.patchDefinition('StoreListConfig', this.$data).then((response) => {
                let listConfigId = response.data.definition.list_configs.find(ele => ele.slug === this.slug).id;
                this.$emit('update-sub-navigation', 'Options', listConfigId);
            }).catch(() => {
            });
        }
    },
    computed: {
        ...mapGetters([
            'ui',
            'definition',
        ]),
    },
};
</script>
