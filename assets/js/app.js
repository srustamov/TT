window._ = require('lodash');



try {
    //window.Popper = require('popper.js').default;
    //import jquery
    //window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

window.Vue = require('vue');



//Vue.config.devtools = false;

Vue.component('example-card', require('./components/Card.vue').default);

const app = new Vue({
    el: '#app',
});
