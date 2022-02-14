<template>
    <v-main>
      <router-view/>
    </v-main>
</template>

<script>
  import axios from "axios"
  export default {
    name: 'App',
    data: () => ({
      dato: null
    }),

    created () {
      if ((localStorage.getItem('sigma_user') != null && localStorage.getItem('sigma_admin') != null) || 
          (sessionStorage.getItem('sigma_user_temp') != null && sessionStorage.getItem('sigma_admin_temp') != null)) {
        //CHECKEAR QUE EL USUARIO SIGA TENIENDO PERMISOS DE ACCESO
        let url = this.$my_host + '/sigma-back/public/index.php/api/check_user';
        axios.get(url, {
            params: {
              username: localStorage.getItem('sigma_user') || sessionStorage.getItem('sigma_user_temp')
            }
        })
        .then(response=> {
            if(!response.data){
              this.$router.replace("/login").catch(()=>{});
              //borrar datos
            } else {
              //CHECHEAR QUE SEA ADMIN O NO
              let url = this.$my_host + '/sigma-back/public/index.php/api/check_admin';
              axios.get(url, {
                  params: {
                    username: localStorage.getItem('sigma_user') || sessionStorage.getItem('sigma_user_temp')
                  }
              })
              .then(response=> {
                  sessionStorage.setItem('sigma_user_temp', localStorage.getItem('sigma_user') || sessionStorage.getItem('sigma_user_temp'));
          
                  if(response.data){
                    sessionStorage.setItem('sigma_admin_temp', true)
                    //this.$router.replace("/admin").catch(()=>{});
                  }else{
                    sessionStorage.setItem('sigma_admin_temp', false)
                    //this.$router.replace("/inicio").catch(()=>{});
                  }

                  switch(this.$route.path){
                    case '/history':
                      this.historial();
                      break;
                    case '/admin':
                      this.admin();
                      break;
                    case '/history_admin':
                      this.historial_admin();
                      break;
                    case '/metrica':
                      this.metricas();
                      break;
                    default:
                      this.inicio();
                  }
              })
            }
        })

      }else{
        this.$router.replace("/login").catch(()=>{});
      }
    },

    methods: {
      metricas(){
        if(sessionStorage.getItem('sigma_admin_temp') == 'true'){
          this.$router.replace("/metrica").catch(()=>{});
        }else{
          this.$router.replace("/inicio").catch(()=>{});
        }
      },

      historial(){
        this.$router.replace("/history").catch(()=>{});
      },

      admin(){
        this.$router.replace("/admin").catch(()=>{});
      },

      historial_admin(){
        this.$router.replace("/history_admin").catch(()=>{});
      },

      inicio(){
        if(sessionStorage.getItem('sigma_admin_temp') == 'true'){
          this.$router.replace("/admin").catch(()=>{});
        }else{
          this.$router.replace("/inicio").catch(()=>{});
        }
      }
    }
  }
</script>