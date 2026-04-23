/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue'

Vue.component('develop-app', require('./components/develop/App.vue').default);
Vue.component('flash-messages', require('./components/utils/FlashMessages.vue').default);
Vue.component('flash-message', require('./components/utils/FlashMessage.vue').default);

import store from './store/develop-and-config';

new Vue({
    store,
    el: '#app',
});
