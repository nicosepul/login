require('./bootstrap');

window.Vue = require('vue').default;

Vue.component('test-component', require('./components/TestComponent.vue').default);
Vue.component('registro-component', require('./components/RegistroComponent.vue').default);
Vue.component('register-component', require('./components/RegisterComponent.vue').default);
Vue.component('buscador-component', require('./components/BuscadorComponent.vue').default);
Vue.component('registro-ingreso-component', require('./components/RegistroIngresoComponent.vue').default);
Vue.component('lista-ingresos-component', require('./components/ListaIngresosComponent.vue').default);


const app = new Vue({
    el: '#app',
});
