/**
 * Vuex-Store Modul für die Komponenten des Aktionstyp.
 */
const state = {
    simulation: {
        starting: false,
        stopping: false,
        running: false,
        executing: null,
        undoing: null,
        initial: null,
        id: null,
        situation: {},
        categories: [],
        accesses: [],
        meta_data: {},
        data: {},
        history: [],
        inputs: {},
        finished_at: null,
        allisa_user_id: null,
        user_id: null,
        environment_id: null,
        default_allisa_process_id: null,
        default_allisa_user_identity_id: null,
        connector_error: null,
        connector_error_message: null,
        demo_data: []
    }
};

const getters = {
    simulation: (state) => state.simulation,
    demo_data: (state) => state.simulation.demo_data,
    active_action_type_ids: (state) => {
        let active = [];

        for (let i = 0; i < state.simulation.categories.length; i++) {
            for (let k = 0; k < state.simulation.categories[i].action_types.length; k++) {
                if (state.simulation.categories[i].action_types[k].active) {
                    active = [
                        ...active,
                        state.simulation.categories[i].action_types[k].id
                    ];
                }
            }
        }

        return active;
    },
    inaccessible_action_type_ids: state => {
        let inaccessible = [];

        for (let i = 0; i < state.simulation.categories.length; i++) {
            for (let k = 0; k < state.simulation.categories[i].action_types.length; k++) {
                if (!state.simulation.categories[i].action_types[k].execute_access) {
                    inaccessible = [
                        ...inaccessible,
                        state.simulation.categories[i].action_types[k].id
                    ];
                }
            }
        }

        return inaccessible;
    },
    active_state_ids: state => {
        let situation = Object.keys(state.simulation.situation).length ? state.simulation.situation : [];

        return situation.map(ele => ele.state_id);
    }
};

const actions = {
    startingSimulation({commit}) {
        commit('STARTING');
    },
    clearStarting({commit}) {
        commit('CLEAR_STARTING');
    },
    initSimulation({commit}, simulation) {
        commit('SET', {
            ...state.simulation,
            starting: false,
            running: true,
            stopping: false,
            executing: null,
            id: simulation.id,
            situation: simulation.situation,
            categories: simulation.categories,
            accesses: simulation.accesses,
            meta_data: simulation.meta_data,
            data: simulation.data,
            history: simulation.history,
            finished_at: simulation.finished_at,
            allisa_user_id: simulation.allisa_user_id,
            environment_id: simulation.environment_id,
            allisa_id: simulation.allisa_id,
            user_id: simulation.user_id,
            connector_error: simulation.connector_error,
            connector_error_message: simulation.connector_error_message
        });
    },
    updateSimulation({commit}, simulation) {
        return new Promise((resolve) => {

            commit('UPDATE', {
                ...state.simulation,
                executing: null,
                undoing: null,
                categories: [...simulation.categories],
                situation: [...simulation.situation],
                meta_data: {...simulation.meta_data},
                data: [...simulation.data],
                // Hier wird auf last_action und last_action.id geprüft weil bei fehlender "Aktions-Historie einsehen"
                // Berechtigung die "id" nicht existiert.
                history: simulation.last_action && simulation.last_action.id ? [
                    ...state.simulation.history,
                    simulation.last_action
                ] : [...state.simulation.history],
                accesses: [...simulation.accesses],
                allisa_id: simulation.allisa_id,
                allisa_user_id: simulation.allisa_user_id,
                connector_error: simulation.connector_error,
                connector_error_message: simulation.connector_error_message
            });
            resolve();
        });
    },
    stopSimulation({commit}) {
        commit('STOP', {
            ...state.simulation,
            running: false,
            stopping: false,
            executing: null,
            id: null,
            situation: {},
            categories: [],
            accesses: [],
            meta_data: {},
            data: {},
            history: [],
            inputs: {},
            finished_at: null,
            allisa_user_id: null,
            environment_id: null,
            connector_error: null,
            connector_error_message: null
        });
    },
    stoppingSimulation({commit}) {
        commit('STOPPING');
    },
    setExecutingActionTypeId({commit}, actionTypeId) {
        commit('SET_EXECUTING_ACTION_TYPE_ID', {
            ...state.simulation,
            executing: actionTypeId
        });
    },
    setActionTypeInputs({commit}, inputs) {
        commit('SET_ACTIONTYPE_INPUTS', {
            ...state.simulation,
            inputs: inputs
        });
    },
    clearExecuting({commit}) {
        commit('CLEAR_EXECUTING');
    },
    setDefaultAllisaProcessId({commit}, id) {
        commit('SET_DEFAULT_ALLISA_PROCESS_ID', {
            ...state.simulation,
            default_allisa_process_id: id
        });
    },
    setDefaultAllisaUserIdentityId({commit}, id) {
        commit('SET_DEFAULT_ALLISA_USER_IDENTITY_ID', {
            ...state.simulation,
            default_allisa_user_identity_id: id
        });
    },
    setDemoData({commit}, demoData) {
        commit('SET_DEMO_DATA', {
            ...state.simulation,
            demo_data: demoData
        });
    },
    undoAction({commit}, actionId) {
        commit('UNDO_ACTION', {
            ...state.simulation,
            undoing: actionId
        });
    },
    clearUndo({commit}) {
        commit('CLEAR_UNDO');
    }
};

const mutations = {
    STARTING: (state) => state.simulation.starting = true,
    CLEAR_STARTING: (state) => state.simulation.starting = false,
    STOPPING: (state) => state.simulation.stopping = true,
    SET: (state, simulation) => state.simulation = simulation,
    UPDATE: (state, simulationWithLastAction) => state.simulation = simulationWithLastAction,
    STOP: (state, updates) => state.simulation = updates,
    SET_EXECUTING_ACTION_TYPE_ID: (state, simulation) => state.simulation = simulation,
    SET_DEFAULT_ALLISA_PROCESS_ID: (state, simulation) => state.simulation = simulation,
    SET_DEFAULT_ALLISA_USER_IDENTITY_ID: (state, simulation) => state.simulation = simulation,
    CLEAR_EXECUTING: (state) => state.simulation.executing = null,
    UNDO_ACTION: (state, simulation) => state.simulation = simulation,
    CLEAR_UNDO: (state) => state.simulation.undoing = null,
    SET_ACTIONTYPE_INPUTS: (state, simulation) => state.simulation = simulation,
    SET_DEMO_DATA: (state, simulation) => state.simulation = simulation,
};

export default {
    state,
    getters,
    actions,
    mutations
};
