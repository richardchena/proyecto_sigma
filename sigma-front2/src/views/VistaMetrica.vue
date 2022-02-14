<template>
    <v-app>
        <v-system-bar class="deep-purple darken-4" height="60">
            <div class="div_2">
                <b><h1>Σ</h1></b>
            </div>

            <h2 style="color: white; margin-left: 2%">SIGMA</h2>

            <v-btn
              style="margin-left: 3%; margin-top: -3px"
              @click="inicio"
              color="yellow accent-2"
              text
            >
            INICIO
            </v-btn>

            <v-btn
              style="margin-left: 0%; margin-top: -3px"
              @click="historial"
              color="yellow accent-2"
              text
            >
            vER HISTORIAL
            </v-btn>

            <v-spacer></v-spacer>
        </v-system-bar>
        <div style="background: #EEEEEE">
            <h2 style="margin-bottom: 10px; margin-top: 10px; margin-left: 10px;">Métrica de Métricas 📊📈 (Desde Enero 2022)</h2>
        </div>
        <metrica></metrica>
    </v-app>
</template>

<script>
import metrica from '@/components/Metrica.vue'
export default {
  components:{
    metrica
  },

  created () {
    if(sessionStorage.getItem('sigma_user_temp').toUpperCase().trim().length > 0){
        if(sessionStorage.getItem('sigma_admin_temp') == 'true'){
          this.autenticado = true;
          this.user = sessionStorage.getItem('sigma_user_temp').toUpperCase();          
        }else{
          this.autenticado = false;
          this.$router.replace("/inicio");
        }
    }else{
        this.autenticado = false;
        this.$router.replace("/login");
    }
  },
 
  methods:{
    inicio(){
      //window.location.href = "/admin";
      this.$router.replace("/admin");
    },


    historial(){
      //this.autenticado = false;
      this.$router.replace("/history_admin").catch(()=>{});
    },
  }
}
</script>

<style scoped>
  h1{
    font-size:35px; 
    margin-top: -27%; 
    color: yellow; 
    font-family: Times New Roman, Times, serif;
  }

  .div_2{
      border: 3.5px;
      border-radius: 8px;
      border-style: solid;
      border-color: black;
      width: 40px;
      height: 40px;
      text-align: center;
      background-color: blue;
      margin-left: 1%;
  }
</style>