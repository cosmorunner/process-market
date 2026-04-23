/**
 * Vuex Store für die Graph-App
 */

import Vuex from 'vuex';
import Vue from 'vue';
import ui from "./modules/ui";
import simulation from "./modules/simulation";

Vue.use(Vuex);

export default new Vuex.Store({
    strict: true,
    modules: {
        ui,
        simulation,
    }
});

export const reduxActions = [
    ...Object.keys(ui.actions),
    ...Object.keys(simulation.actions),
];
