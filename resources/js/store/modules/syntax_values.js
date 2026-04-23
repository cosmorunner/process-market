/**
 * Vuex-Store Modul für die Graphen auf die der Benutzer Zugriff hat.
 */
const state = {
    syntax_values: {
        action_type_id: null,
        syntax: {},
        pipe: {}
    }
};

const getters = {
    syntax_values_action_type_id: (state) => {
        return state.syntax_values.action_type_id;
    },
    loaded_syntax_keys: (state) => {
        return Object.keys(state.syntax_values.syntax);
    },
    loaded_pipe_keys: (state) => {
        return Object.keys(state.syntax_values.pipe);
    },
    syntax_values_loaded: (state, getters) => {
        return getters.loaded_syntax_keys.length || getters.loaded_pipe_keys.length;
    },
    syntax_values_syntax: (state) => {
        return state.syntax_values.syntax;
    },
    syntax_values_pipe: (state) => {
        return state.syntax_values.pipe;
    }
};

const actions = {
    setSyntaxValues({commit}, syntaxValues) {
        commit('SET_SYNTAX_VALUES', syntaxValues);
    },
    clearSyntaxValues({commit}) {
        commit('CLEAR_SYNTAX_VALUES');
    }
};

const mutations = {
    SET_SYNTAX_VALUES: (state, syntaxValues) => state.syntax_values = {
        syntax: {
            ...state.syntax_values.syntax,
            ...syntaxValues.syntax
        },
        pipe: {
            ...state.syntax_values.pipe,
            ...syntaxValues.pipe
        },
        action_type_id: syntaxValues.action_type_id
    },
    CLEAR_SYNTAX_VALUES: (state) => state.syntax_values = {
        ...state.syntax_values,
        action_type_id: null,
        syntax: {},
        pipe: {}
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
