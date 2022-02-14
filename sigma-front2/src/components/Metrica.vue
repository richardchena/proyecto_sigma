<template v-if="autenticado">
    <div>
      <vue-c3 :handler="handler"></vue-c3>
      <div style="margin-top: 30px; align-content: center; background: #EEEEEE; height: 90px">
        <h3 style="margin-bottom: 10px;  margin-left: 15px;">Opciones de vista:</h3>
        <v-btn
        class="bot"
        color="pink"
        @click="to_pie()"
        >
        Pie Chart
        </v-btn>
        <v-btn
        class="bot"
        color="indigo"
        @click="to_donut()"
        >
        Donut Chart
        </v-btn>
        <v-btn
        class="bot"
        color="purple"
        @click="to_area()"
        >
        Area Chart
        </v-btn>
        <v-btn
        class="bot"
        @click="to_normal()"
        color="teal"
        >
        Bar Chart
        </v-btn>
      </div>
    </div>
</template>

<script>
import axios from "axios"
import Vue from "vue";
import VueC3 from "vue-c3";
import "c3/c3.min.css";

export default {
  name: "metrica",
  components: {
    VueC3,
  },
  data: () => ({
    handler: new Vue(),
    autenticado: false,
    valores: null,
    meses_considerar: [],
  }),

computed: {
    options() {
      return {
        data: {
            columns: this.valores,
            type: 'bar',
            labels: true
        },
        bar: {
            width: {
                ratio: 0.7
            }
        },
        grid: {
            x: {
                show: true
            },
            y: {
                show: true
            }
        },
        axis: {
            x: {
                type: 'category',
                categories: this.meses_considerar
            }
        }
      };
    },
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

  mounted() {
    this.cargar_valores();
    //this.muestra();
  },

  methods: {
    cargar_valores(){
      var hoy = new Date();
      var valor = hoy.getMonth() + 1;
      var meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];

      for (var i = 0; i < valor; i++) {
        this.meses_considerar.push(meses[i])
      }

      this.get_ax();
    },

    get_ax(){
      let url = this.$my_host + '/sigma-back/public/index.php/api/metricas'
      axios.get(url)
      .then(response => {
        var elementos = [];
        var hoy = new Date();
        var a_considerar = hoy.getMonth() + 1

        for (var i = 0; i < response.data.length; i++) {
          var lista = [response.data[i].WORKLINE, 
                parseInt(response.data[i].ENERO) || 0,
                parseInt(response.data[i].FEBRERO) || 0,
                parseInt(response.data[i].MARZO) || 0,
                parseInt(response.data[i].ABRIL) || 0,
                parseInt(response.data[i].MAYO) || 0,
                parseInt(response.data[i].JUNIO) || 0,
                parseInt(response.data[i].JULIO) || 0,
                parseInt(response.data[i].AGOSTO) || 0,
                parseInt(response.data[i].SEPTIEMBRE) || 0,
                parseInt(response.data[i].OCTUBRE) || 0,
                parseInt(response.data[i].NOVIEMBRE) || 0,
                parseInt(response.data[i].DICIEMBRE) || 0]
          
          lista.splice(a_considerar + 1);
          elementos.push(lista);
        }
        this.valores = elementos;
        this.handler.$emit("init", this.options);
      })
    },

    muestra(){
      this.valores = [
          ['ANALISÍS', 2, 4, 7, 3, 6, 2, 3, 6, 2, 1, 3, 8],
          ['AUTOMATIZACIONES', 3, 6, 2, 1, 3, 8, 8, 9, 2, 4, 7, 3],
          ['CAPACITY', 5, 3, 1, 4, 3, 4, 1, 3, 8, 7, 8, 9],
          ['HUDDLE', 1, 3, 8, 7, 8, 9, 1, 3, 8, 7, 8, 9],
          ['MODELOS', 4, 3, 4, 8, 3, 4, 5, 3, 1, 4, 3, 4],
          ['REPORTES', 7, 8, 9, 2, 4, 7, 3, 6, 2, 1, 3, 8],
          ['RUDI', 8, 3, 4, 7, 1, 6, 2, 4, 7, 3, 6, 2]
        ]
      
      this.meses_considerar = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
      this.handler.$emit("init", this.options);
    },

    cambiar(){
      this.handler.$emit("dispatch", (chart) => {
        chart.groups([['REPORTES', 'RUDI']]);
      });
    },

    to_normal(){
      this.handler.$emit("init", this.options);
    },

    to_pie(){
      this.handler.$emit("dispatch", (chart) => {
        chart.transform('pie');
      });
    },

    to_donut(){
      this.handler.$emit("dispatch", (chart) => {
        chart.transform('donut');
      });
    },

    to_area(){
      this.handler.$emit("dispatch", (chart) => {
        chart.transform('area');
      });
    }
  }
}

</script>

<style scoped>
  .bot{
    margin-left: 15px;
    color: white;
  }
</style>