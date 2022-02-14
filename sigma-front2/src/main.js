import Vue from 'vue'
import App from './App.vue'
import router from './router'
import vuetify from './plugins/vuetify'
import "@mdi/font/css/materialdesignicons.css";
/*ESTA DIRECCION DEBE MODIFICARSE AL CAMBIAR DE SERVIDOR*/
var host = 'http://192.168.0.10:80'

Vue.config.productionTip = false
Vue.prototype.$my_host = host


//const axios = require('axios');

/*
let url = host + '/sigma-back/public/index.php/api/usuario_conectado';

async function getUser() {
  try {
    const response = await axios.get(url);
    //alert('aquiii')
    return response;

  } catch (error) {
    console.error(error);
  }
}

getUser().then(v => {
  Vue.prototype.$usuario_log = v.data[1];
  Vue.prototype.$resp_admin = v.data[3];
  Vue.prototype.$equipo = v.data[2];

  new Vue({
    router,
    vuetify,
    render: h => h(App)
  }).$mount('#app')
  
})

*/

//VALORES PREDETERMINADOS
Vue.prototype.$usuario_log = ''; //PUEDE SER NULL ???
Vue.prototype.$resp_admin = false;


if (localStorage.getItem('sigma_usuario') != null && localStorage.getItem('sigma_admin') != null) {
    Vue.prototype.$usuario_log = localStorage.getItem('sigma_usuario');
    Vue.prototype.$resp_admin = localStorage.getItem('sigma_admin');
}

new Vue({
  router,
  vuetify,
  render: h => h(App)
}).$mount('#app')