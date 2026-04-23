/**
 * Utility Methoden für die Konfiguration
 */
import sharedUtils from './shared-utils';

export default {

    ...sharedUtils,

    /**
     * Prozesstyp-Definition ändern
     */
    patchDefinition(command, payload = {}) {
        this.clearError();
        this.startLoading();

        let that = this;
        let position = payload.position;

        delete payload.position;

        return new Promise((resolve, reject) => {
            axios.patch('/api/process-versions/' + this.ui.processVersionId + '/definition?only-definition=1', {
                command: command,
                payload: payload,
                position: position
            }).then(function (response) {
                that.stopLoading();
                that.setDefinition(response.data.definition);
                that.setEnabledUndo(true);
                that.setEnabledRedo(false);

                // Clear copy element.
                if (command === 'PasteElement') {
                    that.setUserSettings({
                        copy_element: {}
                    });
                }

                // Wenn die Definition aktualisiert wurde, müssen die Syntax-Werte entfernt werden,
                // weil sich vielleicht die Werte verändert haben.
                that.clearSyntaxValues();

                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },
    /**
     * Undo a command.
     */
    undo() {
        this.clearError();
        this.startLoading();

        let that = this;
        let url = this.ui.urls.undo;

        return new Promise((resolve, reject) => {
            axios.patch(url).then(function (response) {
                that.stopLoading();
                that.setDefinition(response.data.definition);
                that.setEnabledUndo(response.data.undo);
                that.setEnabledRedo(response.data.redo);

                // Wenn die Definition aktualisiert wurde, müssen die Syntax-Werte entfernt werden,
                // weil sich vielleicht die Werte verändert haben.
                that.clearSyntaxValues();
                that.temporaryFlashMessage({
                    type: 'info',
                    message: 'Rückgängig gemacht'
                });

                jQuery('[data-toggle="tooltip"]').tooltip();

                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },

    /**
     * Redo a command.
     */
    redo() {
        this.clearError();
        this.startLoading();

        let that = this;
        let url = this.ui.urls.redo;

        return new Promise((resolve, reject) => {
            axios.patch(url).then(function (response) {
                that.stopLoading();
                that.setDefinition(response.data.definition);
                that.setEnabledUndo(response.data.undo);
                that.setEnabledRedo(response.data.redo);

                // Wenn die Definition aktualisiert wurde, müssen die Syntax-Werte entfernt werden,
                // weil sich vielleicht die Werte verändert haben.
                that.clearSyntaxValues();
                that.temporaryFlashMessage({
                    type: 'info',
                    message: 'Wiederhergestellt'
                });

                jQuery('[data-toggle="tooltip"]').tooltip();

                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },
    fetchListSupportData(listConfigId) {
        this.clearError();
        this.startLoading();

        let that = this;

        return new Promise((resolve, reject) => {
            axios.get('/api/process-versions/' + this.ui.processVersionId + '/listconfigs/' + listConfigId + '/list-support', {}).then(function (response) {
                that.stopLoading();
                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },
    updateFullWidth(value) {
        let that = this;

        this.startLoading();

        return new Promise((resolve, reject) => {
            axios.patch('/api/user/settings', {config_app_full_width: value}).then(function (response) {
                that.setUserSettings({'config_app_full_width': value});
                that.stopLoading();
                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                that.setUserSettings({'config_app_full_width': !value});
                reject(error);
            });
        });
    },
    staticColumnNames(alias) {
        return {
            artifacts_id: 'Artefakt-Id',
            actions_id: 'Aktions-Id',
            documents_title: 'Dokument-Titel',
            documents_mime_type: 'Dokument-Mime-Type',
            documents_file_name: 'Dokument-Dateiname',
            documents_created_at: 'Dokument-Erstelldatum',
            groups_id: 'Gruppen-Id',
            groups_name: 'Gruppen-Name',
            process_type_metas_name: 'Prozesstyp-Name',
            process_type_metas_description: 'Prozesstyp-Beschreibung',
            process_type_metas_image: 'Prozesstyp-Icon',
            process_type_metas_namespace: 'Prozesstyp-Namespace',
            process_type_metas_identifier: 'Prozesstyp-Identifier',
            process_type_metas_full_namespace: 'Prozesstyp-Namespace/Identifier',
            process_type_metas_last_version: 'Prozesstyp-Aktuellste Version',
            process_types_id: 'Prozesstyp-Id',
            process_types_version: 'Prozesstyp-Version',
            processes_id: 'Prozess-Id',
            processes_pipe_notation: 'Prozess-Pipe-Notation',
            processes_name: 'Prozess-Name',
            processes_description: 'Prozess-Beschreibung',
            processes_image: 'Prozess-Icon',
            processes_tags: 'Prozess-Tags',
            processes_created_at: 'Prozess-Erstelldatum',
            processes_updated_at: 'Prozess-Änderungsdatum',
            processes_reference: 'Prozess-Referenz',
            relations_id: 'Verknüpfung-Id',
            relations_created_at: 'Verknüpfung-Erstelldatum',
            relations_relation_type_name: 'Verknüpfungstyp-Name',
            relations_relation_type_id: 'Verknüpfungstyp-Id',
            relation_types_name: 'Verknüpfungstyp-Name',
        }[alias] || alias;
    },
    defaultProcessorOptions(identifier) {
        return {
            update_process_meta: {
                name: '',
                description: '',
                image: null,
                reference: '',
                tags: null,
            },
            create_access: {
                recipients: [],
                role: null,
                is_public_role: false
            },
            create_process: {
                process_type: null,
                name: '',
                description: '',
                image: null,
                reference: '',
                tags: null,
                role: null,
                mapping: []
            },
            create_relation: {
                to: [],
                relation_type: null,
                relation_type_name: null,
                data: {},
            },
            create_document: {
                type: 'pdf',
                name_format: null,
                template: null,
                template_name: null,
            },
            create_e_invoice: {
                profile: null,
                profile_parameters: {}
            },
            delete_access: {
                recipients: [],
                role: null,
                is_public_role: false
            },
            delete_process: {
                processes: [],
                related: []
            },
            delete_relation: {
                processes: [],
                relation_type: null,
                relation_type_name: null
            },
            execute_action: {
                process: null,
                action_type: null,
                action_type_name: null,
                mapping: {},
                multiple: {}
            },
            send_email: {
                to: [],
                cc: [],
                bcc: [],
                subject: null,
                template: null,
                template_name: null,
                attachments: [],
                mapping: {},
                multiple: {},
                priority: '0'
            },
            send_push_message: {
                channels: ['database'],
                recipients: [],
                message: null,
                button_label: null,
                button_url: null,
                type: 'info'
            },
            trigger_connector: {
                connector: null,
                request: null,
                helper_text: null,
                request_mapping: {},
                response_mapping: {},
                allowed_to_fail: false
            },
            redirect: {
                url: null
            },
            execute_custom_logic: {
                template: null,
                template_name: null,
            },
            trigger_event: {
                event: null,
                data: {}
            },
            trigger_task: {
                task: null,
                parameters: {}
            },
            display_flash_message: {
                message: null,
                type: 'info',
                button_url: null,
                button_label: null
            }
        }[identifier] || {};
    },
    toAlias: function (string) {
        const map = {
            '\u00dc': 'UE',
            '\u00c4': 'AE',
            '\u00d6': 'OE',
            '\u00fc': 'ue',
            '\u00e4': 'ae',
            '\u00f6': 'oe',
            '\u00df': 'ss',
        };

        let converted = string.replace(/[\u00dc\u00c4\u00d6][a-z]/g, (a) => {
            const big = map[a.slice(0, 1)];
            return big.charAt(0) + big.charAt(1).toLowerCase() + a.slice(1);
        }).replace(new RegExp('[' + Object.keys(map).join('|') + ']', "g"), (a) => map[a]);

        converted = converted && converted.trim().match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
            .map(x => x.toLowerCase()).join('_');

        return converted;
    },

    storeEnvironment() {
        let that = this;

        return new Promise((resolve, reject) => {
            axios.post('/api/process-versions/' + this.ui.processVersionId + '/environments').then(function (response) {
                that.addEnvironment(response.data);
                that.stopLoading();
                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },

    /**
     * Prozesstyp-Definition ändern
     */
    patchEnvironment(environment, silent = false) {
        let that = this;

        this.clearError();

        if (!silent) {
            this.startLoading();
        }

        return new Promise((resolve, reject) => {
            axios.patch('/api/process-versions/' + environment.process_version_id + '/environments/' + environment.id, environment).then((response) => {
                that.updateEnvironments(response.data);

                if (!silent) {
                    that.stopLoading();
                }

                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },

    deleteEnvironment(id) {
        let that = this;

        return new Promise(((resolve, reject) => {
            axios.delete('/api/process-versions/' + this.ui.processVersionId + '/environments/' + id, this.environment).then((response) => {
                that.removeEnvironment(id);
                that.stopLoading();
                that.environment = null;
                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        }));
    },

    copyEnvironment(id) {
        let that = this;

        return new Promise(((resolve, reject) => {
            axios.post('/api/process-versions/' + this.ui.processVersionId + '/environments/' + id + '/copy', this.environment).then((response) => {
                that.addEnvironment(response.data);
                that.stopLoading();
                that.environment = null;
                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        }));
    },

    /**
     * Prozesstyp-Definition ändern
     */
    patchBlueprint(command, payload = {}) {
        this.clearError();
        this.startLoading();

        let that = this;

        return new Promise((resolve, reject) => {
            axios.patch('/api/process-versions/' + this.ui.processVersionId + '/environments/' + this.environment.id + '/blueprint', {
                command: command,
                payload: payload,
            }).then((response) => {
                that.updateEnvironment(response.data);
                that.stopLoading();
                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },

    /**
     * Prozesstyp-Definition ändern
     */
    saveCopyElement(name, object) {
        this.clearError();
        this.startLoading();

        let that = this;
        let url = this.ui.urls.copy_element;

        return new Promise((resolve, reject) => {
            axios.patch(url, {
                name: name,
                object: object
            }).then(function (response) {
                that.stopLoading();

                that.setUserSettings({
                    copy_element: {
                        name: name,
                        object: object,
                        namespace: response.data.namespace
                    }
                });
                that.temporaryFlashMessage({
                    type: 'info',
                    message: 'Kopiert'
                });
                jQuery('[data-toggle="tooltip"]').tooltip();

                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },

    getCopyElement() {
        let component = this.ui.userSettings['copy_element'] || {};
        return JSON.parse(JSON.stringify(component));
    },

    clearCopyElement() {
        let that = this;
        let url = this.ui.urls.delete_copy_element;

        return new Promise((resolve, reject) => {
            axios.delete(url).then(function (response) {
                that.setUserSettings({
                    copy_element: {}
                });
                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    }

};
