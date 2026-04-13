require('./bootstrap');

window.Vue = require('vue').default;

Vue.component('test-component', require('./components/TestComponent.vue').default);
Vue.component('registro-component', require('./components/RegistroComponent.vue').default);
Vue.component('registro-citas-component', require('./components/RegistroCitasComponent.vue').default);
Vue.component('agenda-citas-component', require('./components/AgendaCitasComponent.vue').default);
Vue.component('registro-usuario-component', require('./components/RegistroUsuarioComponent.vue').default);
Vue.component('buscador-component', require('./components/BuscadorComponent.vue').default);
Vue.component('registro-ingreso-component', require('./components/RegistroIngresoComponent.vue').default);
Vue.component('lista-ingresos-component', require('./components/ListaIngresosComponent.vue').default);
Vue.component('vista-usuarios-component', require('./components/VistaUsuarios.vue').default);


const app = new Vue({
    el: '#app',
});
