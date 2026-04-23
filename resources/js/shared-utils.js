/**
 * Utility Funktionen die mehrere Apps (Config/Develop/Utils) nutzen.
 */
export default {

    /**
     * Setzt anhand von URL-Parametern die Navigation im Tab oder Panel.
     */
    initNavigation(app = 'config') {
        let urlParams = new URLSearchParams(window.location.search);

        let nav = urlParams.get('nav') || (app === 'develop' ? 'actionTypes' : 'ActionTypes');
        let sub = urlParams.get('sub') || (app === 'develop' ? null : 'Components');
        let model = urlParams.get('model') || null;
        let detail = urlParams.get('detail') || null;
        let type = urlParams.get('type') || null;

        // Wenn "type" und "model" gesetzt wird davon ausgegangen, dass man im Regelwerk eine Detailansicht
        // haben möchte.
        if (type && model) {
            this.showElementDetails({
                type: type,
                model: model
            });

            this.updateNavigation({
                nav: nav,
            });

            return;
        }

        this.updateNavigation({
            nav: nav,
            sub: sub,
            detail: detail
        });
    },

    /**
     *
     */
    updateUrl(navigation) {
        let all = {
            nav: navigation.nav,
            sub: navigation.sub,
            detail: navigation.detail,
            model: navigation.details.model,
            type: navigation.details.type,
        };

        let query = Object.keys(all).reduce(function (carry, key) {
            if (typeof all[key] === 'string' && all[key].length) {
                return {
                    ...carry,
                    [key]: all[key]
                };
            }

            return carry;
        }, {});


        let url = `${location.protocol}//${location.host}${location.pathname}`;

        window.history.replaceState({}, '', url + '?' + (new URLSearchParams(query)).toString());
    },

    highlightDetailAction(){
        if(this.ui.navigation.details.model) {
            let modelId = this.ui.navigation.details.model;
            let $elements = cy.nodes('[model_id="' + modelId + '"]');

            $elements.filter(ele => ele.id() !== modelId).addClass('highlighted');

        }
    },

    /**
     * SyntaxLoader Bereiche
     */
    syntaxLoaderLabels() {
        return {
            'action.outputs': 'Aktions-Daten',
            'action.processors': 'Aktions-Prozessor-Daten',
            'action.artifacts': 'Aktion-Artefakte',
            'process.meta': 'Prozess-Metadaten',
            'process.outputs': 'Prozess-Daten',
            'process.status': 'Prozess-Status',
            'process.artifacts': 'Prozess-Artefakte',
            'process.actions': 'Prozess-Aktionen',
            'process.urls': 'Prozess-URLs',
            'url': 'URL-Daten',
            'auth': 'Benutzer-Daten',
            'auth.identity.outputs': 'Prozess-Identität - Daten',
            'auth.identity.status': 'Prozess-Identität - Status',
            'public_apis': 'Öffentl. APIs URLs',
            'variables': 'Variablen-Werte',
            'system': 'System-Metadaten',
            'date': 'Datumsangaben',
            'faker': 'Zufällige Werte',
            'reference.metas': 'Referenz-Metadaten',
            'reference.relation_data': 'Referenz-Verknüpfungsdaten',
            'reference.outputs': 'Referenz-Prozess-Daten',
            'reference.status': 'Referenz-Status-Daten',
            'reference.urls': 'Referenz-URLs',
            'graphs.urls': 'Prozesstyp-URLs',
            'graphs.meta': 'Prozesstyp-Metadaten'
        };
    },

    /**
     * PipeLoader Bereiche
     */
    pipeLoaderLabels() {
        return {
            'environment_groups': 'Gruppen-Models',
            'environment_connectors': 'Konnektor-Models',
            'environment_requests': 'Request-Models',
            'environment_users': 'Benutzer-Models',
            'environment_processes': 'Prozess-Models',
            'environment_bots': 'Bot-Models',
            'environment_public_apis': 'Öffentl. API-Models',
            'environment_variables': 'Variablen-Models',
            'roles': 'Rollen-Models',
            'events': 'Event-Models',
            'list_configs': 'Listen-Konfiguration-Models',
            'relation_types': 'Verknüpfungstypen',
            'templates': 'Vorlagen-Models',
            'action_type_processors': 'Prozessor-Ergebnisse',
            'action_types': 'Aktionstypen',
            'graphs': 'Prozesstypen',
            'graphs_action_types': 'Externe Aktionstypen',
            'graphs_relation_types': 'Externe Verknüpfungstypen',
        };
    },

    /**
     * Manuelle Items
     */
    manualLabels() {
        return {
            'manual': 'Diverses'
        };
    },

    /**
     * Entfernt aus einem Syntax-String, z.B. "[[process.outputs.field_1((Prozess-Daten - field_1))]]"
     * oder "process|001900c6-443a-4fbe-a7d5-99b7697ae79f[Demo-Prozess]" das Label und gibt dafür ein Objekt mit
     * "label" und "value" zurück.
     * "value" ist dann die Syntax ohne das Label.
     * @param syntax
     * @returns {{label: null, value}}
     */
    getSyntaxParts(syntax) {
        if (typeof syntax !== 'string') {
            syntax = '';
        }

        let parts = {
            original: syntax,
            label: null,
            syntax: syntax,
            namespace: null, // Nur bei PipeSyntax
            key: null, // Nur bei PipeSyntax
            type: null
        };

        // Prüfen ob der Wert ein Label hat, z.b. bei [[process.outputs.field_1((Prozess-Daten - field_1))]]
        if (syntax.startsWith('[[') && syntax.endsWith(']]')) {
            parts.type = 'syntax';

            if (syntax.includes('((')) {
                let match = syntax.match("\\(\\((.*?)\\)\\)");

                if (Array.isArray(match) && match.length > 1) {
                    parts.label = match[1];
                    parts.syntax = syntax.replaceAll(match[0], '');
                }
            }
        }

            // Prüfen, ob der Wert ein label hat, z.B. bei "process|d84dad32-55ed-4abc-91b7-3c5d7817d0da[Prozess Test 1]"
        else if (syntax.includes('|')) {
            parts.type = 'pipe';
            let labelPattern = /(?<=\[).+?(?=])/g
            let match = syntax.match(labelPattern);

            // --> Label ermitteln: "Prozess Test 1"
            if (Array.isArray(match) && match.length === 1) {
                parts.label = match[0];
                parts.syntax = syntax.replaceAll('[' + parts.label + ']', '');
            }
            parts.key = parts.syntax.split('|')[1];
            if (syntax.includes('::')) {
                parts.namespace = syntax.split('::')[0];
            }
        }

        return parts;
    },
    setSyntaxLabel(syntax, label) {
        let parts = this.getSyntaxParts(syntax);

        // Syntax ohne Label
        let added = parts.original;

        if (!label) {
            return syntax;
        }

        // [[]]-Syntax
        if (parts.type === 'syntax') {
            return added.substring(0, added.length - 2) + '((' + label + '))]]';
        }
        if (parts.type === 'pipe') {
            return added + '[' + label + ']';
        }

        return syntax;
    },
    modelIcons(name) {
        return {
            group: 'group',
            connector: 'settings_ethernet',
            request: 'nat',
            publicApi: 'api',
            faker: 'shuffle',
            role: 'assignment_ind',
            user: 'person',
            bot: 'smart_toy',
            artifact: 'description',
            relationType: 'settings_ethernet',
            actionType: 'flash_on',
            template: 'wysiwyg',
            process: 'grain',
            listConfig: 'list',
            processor: 'settings',
            event: 'flag',
            variable: 'tag',
            task: 'double_arrow'
        }[name] || 'help';
    },
    processorNames(identifier) {
        return {
            execute_action: 'Aktion ausführen',
            tag_action: 'Aktion markieren',
            copy_artifact: 'Artefakt kopieren',
            trigger_task: 'Aufgabe ausführen',
            send_push_message: 'Benachrichtigung versenden',
            trigger_connector: 'Konnektor-Anfrage ausführen',
            create_document: 'Dokument erzeugen',
            execute_custom_logic: 'Eigene Logik ausführen',
            send_email: 'E-Mail versenden',
            create_e_invoice: 'E-Rechnung erstellen (Experimentell)',
            trigger_event: 'Event auslösen',
            display_flash_message: 'Flash-Nachricht anzeigen',
            create_process: 'Prozess-Instanz erstellen',
            delete_process: 'Prozess-Instanzen löschen',
            update_process_meta: 'Prozess-Metadaten ändern',
            create_relation: 'Verknüpfung erstellen / aktualisieren',
            delete_relation: 'Verknüpfung löschen',
            redirect: 'Weiterleitung',
            delete_access: 'Zugriff entziehen',
            create_access: 'Zugriff erteilen'
        }[identifier] || identifier;
    },
    processorIcons(identifier) {
        return {
            create_access: 'lock_open',
            create_process: 'grain',
            create_relation: 'compare_arrows',
            create_document: 'description',
            delete_process: 'delete_forever',
            copy_artifact: 'file_copy',
            delete_access: 'lock',
            delete_relation: 'swap_horiz',
            execute_action: 'flash_on',
            send_email: 'email',
            display_flash_message: 'feedback',
            send_push_message: 'notifications',
            update_process_meta: 'info',
            trigger_connector: 'settings_ethernet',
            redirect: 'link',
            execute_custom_logic: 'calculate',
            trigger_event: 'flag',
            trigger_task: 'double_arrow',
            tag_action: 'tag',
            create_e_invoice: 'description'
        }[identifier];
    },
    configUrl(nav, sub, detail) {
        let params = {
            nav: nav
        };
        if(sub) {
            params.sub = sub;
        }
        if(detail) {
            params.detail = detail;
        }

        return this.ui.urls.config + '?' + (new URLSearchParams(params)).toString();;
    },
    developUrl() {
        let url = this.ui.urls.develop;

        if(this.ui.navigation.detail && this.ui.navigation.nav === 'ActionTypes') {
            url = this.ui.urls.develop + '?' + (new URLSearchParams({
                nav: 'actionTypes',
                type: 'action',
                model: this.ui.navigation.detail
            })).toString();
        }

        return url;
    },
    /**
     * Definition
     */
    fetchDefinition() {
        this.clearError();
        this.startLoading();

        let that = this;

        return new Promise((resolve, reject) => {
            axios.get(this.ui.urls.definition).then(function (response) {
                that.stopLoading();
                that.setDefinition(response.data);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },
    /**
     * Externe Graphen laden
     */
    fetchUserGraphs() {
        let that = this;

        return new Promise((resolve, reject) => {
            axios.get(that.ui.urls.user_graphs).then(function (response) {
                that.stopLoading();
                that.setGraphs(response.data);
            }).catch(function (error) {
                that.clearError();
                that.setError(error);
                reject(error);
            });
        });
    },
};
