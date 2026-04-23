/**
 * Vuex-Store Modul die Environments eines Graphen.
 */
const state = {
    environments: [],
};

const getters = {
    environments: (state) => state.environments,
    environment_groups: function (state) {
        let items = [];
        let ids = [];

        for (let i = 0; i < state.environments.length; i++) {
            for (let m = 0; m < state.environments[i].blueprint.groups.length; m++) {
                let group = state.environments[i].blueprint.groups[m];

                if (!ids.includes(group.id)) {
                    items.push(group)
                    ids.push(group.id)
                }
            }
        }

        return items;
    },
    environment_users: function (state) {
        let items = [];
        let ids = [];

        for (let i = 0; i < state.environments.length; i++) {
            for (let m = 0; m < state.environments[i].blueprint.users.length; m++) {
                let item = state.environments[i].blueprint.users[m];

                if (!ids.includes(item.id)) {
                    items.push(item)
                    ids.push(item.id)
                }
            }
        }

        return items;
    },
    environment_bots: function (state) {
        let items = [];
        let ids = [];

        for (let i = 0; i < state.environments.length; i++) {
            for (let m = 0; m < state.environments[i].blueprint.bots.length; m++) {
                let item = state.environments[i].blueprint.bots[m];

                if (!ids.includes(item.id)) {
                    items.push(item)
                    ids.push(item.id)
                }
            }
        }

        return items;
    },
    environment_processes: function (state) {
        let items = [];
        let ids = [];

        for (let i = 0; i < state.environments.length; i++) {
            for (let m = 0; m < state.environments[i].blueprint.processes.length; m++) {
                let item = state.environments[i].blueprint.processes[m];

                if (!ids.includes(item.id)) {
                    items.push(item)
                    ids.push(item.id)
                }
            }
        }

        return items;
    },
    environment_connectors: function (state) {
        let items = [];
        let ids = [];

        for (let i = 0; i < state.environments.length; i++) {
            for (let m = 0; m < state.environments[i].blueprint.connectors.length; m++) {
                let item = state.environments[i].blueprint.connectors[m];

                if (!ids.includes(item.id)) {
                    items.push(item)
                    ids.push(item.id)
                }
            }
        }

        return items;
    },
    environment_public_apis: function (state) {
        let items = [];
        let ids = [];

        for (let i = 0; i < state.environments.length; i++) {
            for (let m = 0; m < state.environments[i].blueprint.public_apis.length; m++) {
                let item = state.environments[i].blueprint.public_apis[m];

                if (!ids.includes(item.id)) {
                    items.push(item)
                    ids.push(item.id)
                }
            }
        }

        return items;
    },
    environment_requests: function (state) {
        let items = [];
        let ids = [];

        for (let i = 0; i < state.environments.length; i++) {
            for (let m = 0; m < state.environments[i].blueprint.requests.length; m++) {
                let item = state.environments[i].blueprint.requests[m];

                if (!ids.includes(item.id)) {
                    items.push(item)
                    ids.push(item.id)
                }
            }
        }

        return items;
    },

    environment_tasks: function (state) {
        let items = [];
        let ids = [];

        for (let i = 0; i < state.environments.length; i++) {
            for (let m = 0; m < state.environments[i].blueprint.tasks.length; m++) {
                let item = state.environments[i].blueprint.tasks[m];

                if (!ids.includes(item.id)) {
                    items.push(item)
                    ids.push(item.id)
                }
            }
        }

        return items;
    },

    environment_variables: function (state) {
        let items = [];
        let ids = [];

        for (let i = 0; i < state.environments.length; i++) {
            for (let m = 0; m < state.environments[i].blueprint.variables.length; m++) {
                let item = state.environments[i].blueprint.variables[m];

                if (!ids.includes(item.id)) {
                    items.push(item)
                    ids.push(item.id)
                }
            }
        }

        return items;
    }
};

const actions = {
    setEnvironments({commit}, environments) {
        commit('SET_ENVIRONMENTS', [...environments]);
    },
    addEnvironment({commit}, payload) {
        commit('ADD_ENVIRONMENT', [
            ...state.environments,
            payload
        ]);
    },
    updateEnvironment({commit}, payload) {
        commit('UPDATE_ENVIRONMENT', [...state.environments.map(ele => payload.id === ele.id ? payload : ele)]);
    },
    updateEnvironments({commit}, payload) {
        commit('UPDATE_ENVIRONMENTS', [...payload]);
    },
    removeEnvironment({commit}, id) {
        commit('REMOVE_ENVIRONMENT', [...state.environments.filter(ele => ele.id !== id)]);
    },
};

const mutations = {
    ADD_ENVIRONMENT: (state, environments) => state.environments = environments,
    SET_ENVIRONMENTS: (state, environments) => state.environments = environments,
    REMOVE_ENVIRONMENT: (state, environments) => state.environments = environments,
    UPDATE_ENVIRONMENT: (state, environments) => state.environments = environments,
    UPDATE_ENVIRONMENTS: (state, environments) => state.environments = environments,
};

export default {
    state,
    getters,
    actions,
    mutations
};
