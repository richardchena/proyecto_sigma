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
              @click="estadistica"
              color="yellow accent-2"
              text
            >
            ESTADÍSTICA
            </v-btn>

            <v-spacer></v-spacer>
            <h2 style="color: white">¡Bienvenid@ {{this.user}}!</h2> 
        </v-system-bar>

        <v-data-table
            :headers="headers"
            :items="desserts"
            class="elevation-1"
            :search="search"
            :loading="loader"
            loading-text="Porfavor espera miestras cargamos los registros para ti"
            :items-per-page="-1"
        >
            <template v-slot:top>
            <v-toolbar
                flat
            >
                <v-toolbar-title>Historial de todos los usuarios</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-text-field
                  v-model="search"
                  background-color="gray"
                  color="black"
                  class="text-input-blue"
                  clearable
                  flat
                  dense
                  outlined
                  hide-details
                  prepend-inner-icon="mdi-magnify"
                  label="Escriba aquí para realizar un filtro"
                  maxlength="70"
                ></v-text-field>
                <v-dialog
                v-model="dialog"
                max-width="500px"
                >
                </v-dialog>

                <v-dialog v-model="dialogComment" max-width="500px">
                <v-card>
                    <v-card-title>
                    <span class="text-h5">Comentario #{{editedItem.ID}}</span>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-container>
                    <v-textarea
                        v-model="editedItem.COMMENT"
                        color="teal"
                        counter
                        maxlength="1000"
                        outlined
                        disabled
                        hint="Los comentarios no se pueden editar"
                        label="No se han agregado comentarios a esta solicitud"
                        solo
                        :readonly="true"
                    >
                    </v-textarea>
                    <v-divider></v-divider>
                    <div style="margin-left: 80%; margin-top: 9px">
                        <v-btn color="info" text @click="closeComment">CERRAR</v-btn>
                    </div>
                    </v-container>
                </v-card>
                </v-dialog>
                
            </v-toolbar>
            </template>
            <template v-slot:[`item.ESTADO`]="{ item }">
              <v-chip
                :color="getColor(item.ESTADO)"
              >
                {{ item.ESTADO }}
              </v-chip>
            </template>
            <template v-slot:[`item.actions`]="{ item }" >
            <div class="text-center">
                <v-btn
                color="success"
                dark
                class="mb-2"
                @click="editComment(item)"
                >
                VER Comentario
                </v-btn>
            </div>
            </template>
            <template v-slot:no-data>
                <strong>No hay registros</strong>
            </template>
        </v-data-table>
    </v-app>
</template>

<script>
import axios from 'axios'

export default {
    name: 'history',
    data: () => ({
        variable: null,
        search: '',
        loader: true,
        autenticado: false,
        user: null,
        dialog: false,
        dialogComment: false,
        headers: [
            { text: 'ID', align: 'center', value: 'ID', class:'deep-purple lighten-1 white--text'},
            { text: 'FECHA',  value: 'FECHA', class:'deep-purple lighten-1 white--text'},
            { text: 'ASIGNADO', value: 'ASIGNADO', class:'deep-purple lighten-1 white--text'},
            { text: 'ASUNTO',  value: 'ASUNTO', class:'deep-purple lighten-1 white--text'},
            { text: 'WORKLINE', value: 'WORKLINE', class:'deep-purple lighten-1 white--text'},
            { text: 'ESTADO', value: 'ESTADO', class:'deep-purple lighten-1 white--text'},
            { text: 'PRIORIDAD', value: 'DIFICULTAD', class:'deep-purple lighten-1 white--text'},
            { text: 'DEADLINE', value: 'DEADLINE', class:'deep-purple lighten-1 white--text'},
            { text: 'FINALIZADO', align: 'center', value: 'ENTREGA', class:'deep-purple lighten-1 white--text'},
            { text: 'CONTROL', align: 'center', value: 'actions', sortable: false, class:'deep-purple lighten-1 white--text'},
        ],
        desserts: [],
        editedIndex: -1,
        editedItem: {
            ID: null,
            FECHA: '',
            ASUNTO: '',
            WORKLINE: '',
            ESTADO: '',
            DIFICULTAD: '',
            DEADLINE: '',
            ENTREGA: '',
            COMMENT: '',
            USUARIO_CREACION: ''
        },
        defaultItem: {
            ID: null,
            FECHA: '',
            ASUNTO: '',
            WORKLINE: '',
            ESTADO: '',
            DIFICULTAD: '',
            DEADLINE: '',
            ENTREGA: '',
            COMMENT: ''
        }
    }),

    watch: {
      dialogComment (val) {
        val || this.closeComment()
      },
    },

    created () {
      if(sessionStorage.getItem('sigma_user_temp').toUpperCase().trim().length > 0){
          if(sessionStorage.getItem('sigma_admin_temp') == 'true'){
            this.autenticado = true;
            this.user = sessionStorage.getItem('sigma_user_temp').toUpperCase();
            this.cargar();
          }else{
            this.autenticado = false;
            this.$router.replace("/history");
          }
      }else{
          this.autenticado = false;
          this.$router.replace("/login");
      }
    },

    methods: {
      getColor (valor) {
        if (valor == 'FINALIZADO') {
          return 'green white--text'

        }else{
          return 'red white--text'
        }
      },

      inicio(){
        //window.location.href = "/admin";
        this.$router.replace("/admin");
      },

      estadistica(){
        this.autenticado = false;
        this.$router.replace("/metrica").catch(()=>{});
      },

      cargar(){
        let url = this.$my_host + '/sigma-back/public/index.php/api/listar_historia'
        axios.get(url, { params: { usuario: this.user } })
        .then(response => {
          this.desserts = response.data;
          if(response.data){
            this.loader = false;
          }
        })
      },

      editComment(item){
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogComment = true;
      },

      closeComment(){
        this.dialogComment = false;
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
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