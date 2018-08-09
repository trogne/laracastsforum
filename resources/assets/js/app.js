
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
//ICI au lieu de bootstrap.js :
window.events = new Vue(); //every Vue instance already is an event bus , vue.$on, vue.$emit //let's just piggyback off that and save this to an event object (global events bus)

window.flash = function (message, level = 'success') {
     window.events.$emit('flash', { message, level });
}; // flash('my new flash message')

//Vue.prototype.authorize = function (handler) {
//    //return handler(window.App.user);
//    //if (window.App.user && window.App.user.id == '55') {
//    //    return true;
//    //} else {
//    //    return window.App.user ? handler(window.App.user) : false;
//    //}
//    
//    let user = window.App.user;
//    //if (!user) return false;
//    //return handler(user);
//    return user ? handler(user) : false;
//}

////now allowing named authorizations :
let authorizations = require('./authorizations');

//Vue.prototype.authorize = function (fn, ...rest) {
Vue.prototype.authorize = function (...params) { //params: could be a callback, or a string
    if (!window.App.signedIn) return false;
    
    if (typeof params[0] === 'string') { //then using named authorizations
        return authorizations[params[0]](params[1]);
    }
    
    return params[0](window.App.user); //fn.apply(null, rest);
}

Vue.prototype.signedIn = window.App.signedIn;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//Vue.component('example', require('./components/Example.vue'));
Vue.component('flash', require('./components/Flash.vue'));
//Vue.component('reply', require('./components/Reply.vue'));
Vue.component('paginator', require('./components/Paginator.vue'));
Vue.component('user-notifications', require('./components/UserNotifications.vue'));
Vue.component('avatar-form', require('./components/AvatarForm.vue'));

Vue.component('thread-view', require('./pages/Thread.vue'));

const app = new Vue({
    el: '#app'
});
