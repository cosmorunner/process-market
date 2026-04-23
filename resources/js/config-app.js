/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue';

/**
 * Alle Plugin-ConfigurationKomponenten aus resources/plugins, z.B. Allisa/ActionTypeComponent/Form/v1_0_0/configuration/Template.vue
 */
const req = require.context('../plugins', true, /(.*configuration)\/Template\.(vue)$/i);

// Nur .vue-Komponenten der Plugins laden die zur Ausführung (execution) gehören.
req.keys().map(key => {

    /**
     * "configuration"-Plugin-Komponenten werden im kebab-case registriert.
     * Aus "./Allisa/ActionTypeComponent/Form/v1_0_0/configuration/Form.vue" wird
     * "allisa-actiontypecomponent-form-v1_0_0-configuration-form".
     */
    // remove './' and '.vue' from key: e.g. key = ./Allisa/ActionTypeComponent/Form/v1_0_0/execution/Template.vue
    key = key.slice(2, -4);
    const name = key.replace(/^\/|\/$/g, '').toLowerCase().replace(/\//g, '-');

    return Vue.component(name, () => import('../plugins/' + key + '.vue'));
});

/**
 * Register all components from storage/app/Plugins dynamically with Vue.
 * Register "Template.vue" and "TemplateOptions.vue".
 */
const externalTemplates = require.context('../../storage/app/plugins/', true, /(.*configuration)\/Template\.(vue)$/i);
const externalTemplateOptions = require.context('../../storage/app/plugins/', true, /(.*configuration)\/TemplateOptions\.(vue)$/i);

externalTemplates.keys().map(key => {
    // remove './' and '.vue' from key: e.g. key = ./Allisa/ActionTypeComponent/Form/v1_0_0/execution/Template.vue
    key = key.slice(2, -4);
    const name = key.replace(/^\/|\/$/g, '').toLowerCase().replace(/\//g, '-');

    return Vue.component(name, () => import('../../storage/app/plugins/' + key + '.vue'));
});

externalTemplateOptions.keys().map(key => {
    // remove './' and '.vue' from key: e.g. key = ./Allisa/ActionTypeComponent/Form/v1_0_0/execution/Template.vue
    key = key.slice(2, -4);
    const name = key.replace(/^\/|\/$/g, '').toLowerCase().replace(/\//g, '-');

    return Vue.component(name, () => import('../../storage/app/plugins/' + key + '.vue'));
});

Vue.component('config-app', require('./components/config/App.vue').default);
Vue.component('flash-messages', require('./components/utils/FlashMessages.vue').default);
Vue.component('flash-message', require('./components/utils/FlashMessage.vue').default);

import store from './store/develop-and-config';

new Vue({
    store,
    el: '#app',
});
