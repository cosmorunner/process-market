/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue';

Vue.component('flash-messages', require('./components/utils/FlashMessages.vue').default);
Vue.component('flash-message', require('./components/utils/FlashMessage.vue').default);
Vue.component('explore-processes', require('./components/utils/ExploreProcesses.vue').default);

import store from './store/utils';

new Vue({
    store,
    el: '#app',
});
