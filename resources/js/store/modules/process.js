/**
 * Vuex-Store Modul für den Prozess.
 */
const state = {
    process: {}
};

const getters = {
    process: state => state.process,
    namespace_with_version: state => state.process.full_namespace + '@' + state.process.latest_version
};

const actions = {
    setProcess({commit}, process) {
        commit('SET_PROCESS', {...process});
    }
};

const mutations = {
    SET_PROCESS: (state, process) => state.process = process,
};

export default {
    state,
    getters,
    actions,
    mutations
};
