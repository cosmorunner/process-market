/**
 * Vuex Store für die Graph-App
 */

import Vuex from 'vuex';
import Vue from 'vue';

import definition from "./modules/definition";
import environments from "./modules/environments";
import graphs from "./modules/graphs";
import process from "./modules/process";
import simulation from "./modules/simulation";
import ui from "./modules/ui";
import syntax_values from "./modules/syntax_values";

Vue.use(Vuex);

export default new Vuex.Store({
    strict: true,
    modules: {
        definition,
        environments,
        graphs,
        process,
        simulation,
        ui,
        syntax_values
    }
});

export const reduxActions = [
    ...Object.keys(definition.actions),
    ...Object.keys(environments.actions),
    ...Object.keys(graphs.actions),
    ...Object.keys(process.actions),
    ...Object.keys(simulation.actions),
    ...Object.keys(ui.actions),
    ...Object.keys(syntax_values.actions)
];
