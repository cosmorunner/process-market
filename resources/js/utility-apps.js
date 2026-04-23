/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue';

Vue.component('stop-simulation', require('./components/config/StopSimulationButton.vue').default);
Vue.component('switch-simulation-user', require('./components/utils/SwitchSimulationUser.vue').default);
Vue.component('switch-demo-user', require('./components/utils/SwitchDemoUser.vue').default);
Vue.component('create-allisa-demo', require('./components/utils/CreateAllisaDemo.vue').default);
Vue.component('create-process', require('./components/utils/CreateProcess.vue').default);
Vue.component('create-solution', require('./components/utils/CreateSolution.vue').default);
Vue.component('start-process-demo', require('./components/utils/StartProcessDemo.vue').default);
Vue.component('start-solution-demo', require('./components/utils/StartSolutionDemo.vue').default);
Vue.component('update-process', require('./components/utils/UpdateProcess.vue').default);
Vue.component('update-solution', require('./components/utils/UpdateSolution.vue').default);
Vue.component('update-solution-config', require('./components/utils/UpdateSolutionConfig.vue').default);
Vue.component('license-settings', require('./components/utils/LicenseSettings.vue').default);
Vue.component('flash-messages', require('./components/utils/FlashMessages.vue').default);
Vue.component('flash-message', require('./components/utils/FlashMessage.vue').default);
Vue.component('purchase-process-license', require('./components/utils/PurchaseProcessLicense.vue').default);
Vue.component('purchase-solution-license', require('./components/utils/PurchaseSolutionLicense.vue').default);
Vue.component('profile-picture', require('./components/utils/ProfilePicture.vue').default);
Vue.component('explore-processes', require('./components/utils/ExploreProcesses.vue').default);

import store from './store/utils';

new Vue({
    store,
    el: '#app',
});
