/**
 * Vuex-Store Modul für die Komponenten des Aktionstyp.
 */
const state = {
    ui: {
        // Id des Graphen der aktuell geladen ist
        processVersionId: null, // Id der Prozess-Identität des Demo-Benutzers
        allisaDemoIdentityId: null, // Flagge ob aktuell ein Ajax-Request ausgeführt wird.
        loading: false, // Flagge ob das Control-Panel bei "Regelwerk" offen ist.
        openControlPanel: true, // Flagge ob die Prozess-Version bearbeitet werden kann. Publizierte Versionen können
                                // nicht mehr bearbeitet werden.
        editable: true, // Flagge ob der "undo"-Button aktiviert ist.
        enabledUndo: true, // Flagge ob der "redo"-Button aktiviert ist.
        enabledRedo: true,
        navigation: {
            // Name des aktiven Tabs in der Konfiguration
            nav: '', // Name des Sub-Tabs in der Konfiguration, z.B. "Prozessoren" unter "Aktionen".
            sub: '', // Id oder Name der Detailanzeige, z.B. bestimmte Aktion unter "Aktionen".
            detail: null, // Detail-Anzeige im Regelwerk.
            details: {
                type: null,
                model: null
            }, // Die letzte Detail-Anzeige von einem bestimmten Model speichern, sodass beim Tab-Wechsel von einer
               // Listen
            // Detail-Ansicht zu einer Aktions-Detailansicht die zuletzt angezeigte Aktion angezeigt werden kann.
            // z.B. {listConfig: '13b5d99b-0d56-4161-943a-143bd2fcfc1a', actionType:
            // 'c8e575da-d0e7-4406-90c1-40b2feadb013'}
            lastViewedModelOfType: {}
        }, // Fehler Code eines Ajax-Requests.
        lastCommand: null, // Letzter Command-Name
        errorCode: null, // Fehler Message eines Ajax-Requests.
        errorMessage: null, // Validierungsfehler von einem Ajax-Request.
        validationErrors: {}, // Dialoganzeige
        modal: {
            componentName: null,
            onSave: null,
            open: false,
            data: {},
            clientHeight: 0
        }, // Urls zu anderen Bereichen des Graphen
        urls: {},
        flashMessages: {},
        userSettings: {},
        plugins: {},
    }
};

const getters = {
    ui: (state) => state.ui,
    flash_messages: (state) => state.ui.flashMessages
};

const actions = {
    setUiState({commit}, changes) {
        commit('SET_STATE', {
            ...state.ui,
            ...changes
        });
    },
    setModalClientHeight({commit}, clientHeight) {
        commit('SET_MODAL_CLIENT_HEIGHT', {
            ...state.ui,
            modal: {
                ...state.ui.modal,
                // 100 = 50 margin top and bottom, see app.scss ".modal-100vh"
                // 100 = Height of ModalHeader + height of ModalFooter, see Modal.vue -> concrete Modals, e.g. TemplateModal.vue
                // 2 = 1px header border, 1px footer border
                bodyHeight: clientHeight - 204
            }
        });
    },
    updateNavigation({commit}, navigation) {
        let lastViewedOfType = {
            ...state.ui.navigation.lastViewedModelOfType, ...navigation.lastViewedModelOfType || {}
        };

        commit('UPDATE_NAVIGATION', {
            ...state.ui,
            openControlPanel: true,
            navigation: {
                ...state.ui.navigation, ...navigation,
                lastViewedModelOfType: {...lastViewedOfType}
            }
        });
    },
    showElementDetails({commit}, elementDetails) {
        commit('SET_ELEMENT_DETAILS', {
            ...state.ui,
            openControlPanel: true,
            navigation: {
                ...state.ui.navigation,
                details: elementDetails
            }
        });
    },
    clearElementDetails({commit}) {
        commit('CLEAR_ELEMENT_DETAILS', {
            ...state.ui,
            navigation: {
                ...state.ui.navigation,
                details: {
                    type: null,
                    model: null
                }
            }
        });
    },
    startLoading({commit}) {
        commit('START_LOADING');
    },
    stopLoading({commit}) {
        commit('STOP_LOADING');
    },
    setEnabledUndo({commit}, enabled) {
        commit('SET_ENABLED_UNDO', enabled);
    },
    setEnabledRedo({commit}, enabled) {
        commit('SET_ENABLED_REDO', enabled);
    },
    setError({commit}, error) {
        // Via Response setzten
        if (error.hasOwnProperty('response')) {
            commit('SET_ERROR', {
                ...state.ui,
                loading: false,
                lastCommand: error.lastCommand || null,
                errorCode: error.response.status,
                errorMessage: error.response.data.message,
                validationErrors: error.response.data.errors || []
            });
        }

        // Manuel setzten
        if (!error.hasOwnProperty('response') && error.hasOwnProperty('code') && error.hasOwnProperty('message')) {
            commit('SET_ERROR', {
                ...state.ui,
                loading: false,
                errorCode: error.code,
                errorMessage: error.message,
                validationErrors: error.validationErrors || []
            });
        }
    },
    clearError({commit}) {
        commit('CLEAR_ERROR');
        commit('CLEAR_VALIDATION_ERRORS');
    },
    clearValidationErrors({commit}) {
        commit('CLEAR_VALIDATION_ERRORS');
    },
    openModal({commit}, payload) {
        commit('OPEN_MODAL', {
            ...state.ui,
            modal: {
                open: true,
                componentName: payload.componentName,
                onSave: payload.onSave || null,
                data: payload.data || null
            },
        });
    },
    setFlashMessage({commit}, payload) {
        commit('CLEAR_FLASH_MESSAGES');
        commit('SET_FLASH_MESSAGE', {
            ...state.ui,
            flashMessages: {
                ...state.ui.flashMessages,
                [payload.type]: payload.message
            },
        });
    },
    temporaryFlashMessage({commit}, payload) {
        if (Object.keys(state.ui.flashMessages).length) {
            return;
        }

        commit('SET_FLASH_MESSAGE', {
            ...state.ui,
            flashMessages: {
                ...state.ui.flashMessages,
                [payload.type]: payload.message
            },
        });

        setTimeout(() => commit('CLEAR_FLASH_MESSAGES'), 2000);
    },
    clearFlashMessages({commit}) {
        commit('CLEAR_FLASH_MESSAGES');
    },
    closeModal({commit}) {
        return new Promise(resolve => {
            let $modal = $('#genericModal');
            if ($modal) {
                $modal.modal('hide');
            }
            commit('CLOSE_MODAL');

            let $tooltips = $('[data-toggle="tooltip"]');

            $tooltips.tooltip('dispose');
            $tooltips.tooltip();

            resolve();
        });
    },
    setUserSettings({commit}, settings) {
        commit('SET_USER_SETTINGS', settings);
    },
};

const mutations = {
    SET_STATE: (state, ui) => state.ui = ui,
    SET_ELEMENT_DETAILS: (state, ui) => state.ui = ui,
    UPDATE_NAVIGATION: (state, ui) => state.ui = ui,
    SET_MODAL_CLIENT_HEIGHT: (state, ui) => state.ui = ui,
    CLEAR_ELEMENT_DETAILS: (state, ui) => state.ui = ui,
    SET_ERROR: (state, ui) => state.ui = ui,
    SET_ENABLED_UNDO: (state, enabled) => state.ui.enabledUndo = enabled,
    SET_ENABLED_REDO: (state, enabled) => state.ui.enabledRedo = enabled,
    CLEAR_ERROR: (state) => state.ui = {
        ...state.ui,
        errorCode: null,
        errorMessage: null,
    },
    CLEAR_VALIDATION_ERRORS: (state) => state.ui = {
        ...state.ui,
        validationErrors: {},
    },
    OPEN_MODAL: (state, ui) => state.ui = ui,
    SET_FLASH_MESSAGE: (state, ui) => state.ui = ui,
    CLEAR_FLASH_MESSAGES: (state) => state.ui = {
        ...state.ui,
        flashMessages: {},
    },
    CLOSE_MODAL: (state) => state.ui = {
        ...state.ui,
        modal: {
            ...state.ui.modal,
            componentName: null,
            open: false,
            onSave: null,
            data: null
        }
    },
    START_LOADING: (state) => state.ui.loading = true,
    STOP_LOADING: (state) => state.ui.loading = false,
    SET_USER_SETTINGS: (state, settings) => state.ui = {
        ...state.ui,
        userSettings: {
            ...state.ui.userSettings, ...settings
        }
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
