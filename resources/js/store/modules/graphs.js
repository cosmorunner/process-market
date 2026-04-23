/**
 * Vuex-Store Modul für die Graphen auf die der Benutzer Zugriff hat.
 */
const state = {
    graphs: [],
};

const getters = {
    graphs: (state) => state.graphs,
    // Alle Prozess-Output-Namen aller Graph-Versionen
    graphs_output_names: (state) => {
        let names = [];

        for (let i = 0; i < state.graphs.length; i++) {
            for (let k = 0; k < state.graphs[i].outputs.length; k++) {
                names.push(state.graphs[i].outputs[k].name);
            }
        }

        return [...new Set(names)];
    },
    graphs_action_types: (state) => {
        let names = [];

        for (let i = 0; i < state.graphs.length; i++) {
            for (let k = 0; k < state.graphs[i].action_types.length; k++) {
                names.push({
                    namespace: state.graphs[i].full_namespace,
                    id: state.graphs[i].action_types[k].id,
                    name: state.graphs[i].action_types[k].name,
                });
            }
        }

        return [...new Set(names)];
    },
    graphs_status_types: (state) => {
        let names = [];

        for (let i = 0; i < state.graphs.length; i++) {
            for (let k = 0; k < state.graphs[i].status_types.length; k++) {
                names.push({
                    namespace: state.graphs[i].full_namespace,
                    id: state.graphs[i].status_types[k].id,
                    name: state.graphs[i].status_types[k].name,
                    smart: state.graphs[i].status_types[k].smart,
                    states: state.graphs[i].status_types[k].states
                });
            }
        }

        return [...new Set(names)];
    },
    graphs_relation_types: (state) => {
        let names = [];

        for (let graph of state.graphs) {
            for (let relationType of graph.relation_types) {
                names.push({
                    namespace: graph.full_namespace_without_version,
                    id: relationType.id,
                    name: relationType.name,
                    reference: relationType.reference,
                    default: relationType.default
                });
            }
        }

        return [...new Set(names)];
    },
    graphs_events: (state) => {
        let obj = {};
        let events = [];
        let eventNames = []

        for (let graph of state.graphs) {
            let namespace = graph.namespace + '/' + graph.identifier;

            if (!obj.hasOwnProperty(namespace)) {
                obj[namespace] = [];
            }

            events = [];

            for (let event of graph.events) {
                events.push(event.name);
            }
            obj[namespace] = [...new Set([...obj[namespace], ...events])];
        }

        for (let namespace in obj) {
            eventNames = [...eventNames, ...obj[namespace].map(ele => namespace + '::' + ele)]
        }

        return eventNames;
    },
};

const actions = {
    setGraphs({commit}, graphs) {
        commit('SET_GRAPHS', [...graphs]);
    }
};

const mutations = {
    SET_GRAPHS: (state, graphs) => state.graphs = graphs,
};

export default {
    state,
    getters,
    actions,
    mutations
};
