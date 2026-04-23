/**
 * Vuex-Store Modul für die Definition des Prozesstyps.
 */

const state = {
    definition: {}
};

const getters = {
    definition: (state) => state.definition,
    action_types: (state) => state.definition.action_types,
    action_rules: (state) => state.definition.action_types.reduce((carry, actionType) => [...carry, ...actionType.action_rules || []], []),
    status_rules: (state) => state.definition.action_types.reduce((carry, actionType) => [...carry, ...actionType.status_rules || []], []),
    status_types: (state) => state.definition.status_types,
    states: (state) => state.definition.status_types.reduce((carry, statusType) => [...carry, ...statusType.states || []], []),
    list_configs: (state) => state.definition.list_configs,
    menu_items: (state) => state.definition.menu_items,
    categories: (state) => state.definition.categories,
    events: (state) => state.definition.events,
    listeners: (state) => state.definition.listeners,
    javascript: (state) => state.definition.javascript,
    outputs: (state) => state.definition.outputs,
    roles: (state) => state.definition.roles,
    relation_types: (state) => state.definition.relation_types,
    relation_types_with_single_process: (state) => state.definition.relation_types.filter(ele => ele.reference && (ele.connection_type === '1-1' || ele.connection_type === 'n-1')),
    templates: (state) => state.definition.templates,
    processors: function (state) {
        let processors = [];

        for (let i = 0; i < state.definition.action_types.length; i++) {
            for (let k = 0; k < state.definition.action_types[i].processors.length; k++) {
                processors.push(state.definition.action_types[i].processors[k]);
            }
        }

        return processors;
    },
};

const actions = {
    setDefinition({commit}, definition) {
        commit('SET_DEFINITION', definition);
    },
    deleteRole({commit}, roleId) {
        let roles = state.definition.roles.filter(ele => ele.id !== roleId);
        let defaultRole = state.definition.default_role_id === roleId ? null : state.definition.default_role_id;

        commit('SET_DEFINITION', {
            ...state.definition,
            roles: roles,
            default_role_id: defaultRole
        });
    },
    changeDefinition({commit}, payload) {
        commit('SET_DEFINITION', {
            ...state.definition,
            [payload.key]: payload.value
        });
    },
    changeDefaultRoleId({commit}, roleId) {
        commit('SET_DEFINITION', {
            ...state.definition,
            default_role_id: roleId
        });
    },
    changePublicRoleId({commit}, roleId) {
        commit('SET_DEFINITION', {
            ...state.definition,
            public_role_id: roleId
        });
    },
    changeReferencePattern({commit}, referencePattern) {
        commit('SET_DEFINITION', {
            ...state.definition,
            reference_pattern: referencePattern
        });
    }
};

const mutations = {
    SET_DEFINITION: (state, definition) => state.definition = {
        ...state.definition,
        ...definition
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
