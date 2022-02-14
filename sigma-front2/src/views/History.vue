<template>
    <v-app v-if="autenticado">
        <v-system-bar color="#ff8000" height="60">
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

            <v-spacer></v-spacer>

            <h2 style="color: white">USUARIO: {{this.user}}</h2> 
        </v-system-bar>
        <v-data-table
            :headers="headers"
            :items="desserts"
            class="elevation-1"
            :loading="loader"
            loading-text="Porfavor espera miestras cargamos los registros para ti"
            :items-per-page="-1"
        >
            <template v-slot:top>
            <v-toolbar
                flat
            >
                <v-toolbar-title>Historial del usuario</v-toolbar-title>
                <v-spacer></v-spacer>
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
            <template v-slot:[`item.actions`]="{ item }" >
            <div class="text-center">
                <v-btn
                color="success"
                dark
                class="mb-2"
                @click="editComment(item)"
                >
                Comentario
                </v-btn>
            </div>
            </template>
            <template v-slot:no-data>
                <strong>No hay registros del usuario conectado</strong>
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
        autenticado: false,
        user: null,
        loader: true,
        dialog: false,
        dialogComment: false,
        headers: [
            { text: 'ID', align: 'center', value: 'ID', class:'orange dargen-4 black--text'},
            { text: 'FECHA', align: 'center', value: 'FECHA', class:'orange dargen-4 black--text'},
            { text: 'ASUNTO', align: 'center', value: 'ASUNTO', class:'orange dargen-7 black--text'},
            { text: 'WORKLINE', align: 'center', value: 'WORKLINE', class:'orange dargen-7 black--text'},
            { text: 'ESTADO', align: 'center', value: 'ESTADO', class:'orange dargen-7 black--text'},
            { text: 'PRIORIDAD', align: 'center', value: 'DIFICULTAD', class:'orange dargen-7 black--text'},
            { text: 'DEADLINE', align: 'center', value: 'DEADLINE', class:'orange dargen-7 black--text'},
            { text: 'FINALIZADO', align: 'center', value: 'ENTREGA', class:'orange dargen-7 black--text'},
            { text: 'CONTROL', align: 'center', value: 'actions', sortable: false, class:'orange dargen-7 black--text'},
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
            this.autenticado = false;
            this.$router.replace("/history_admin");
          }
          else{
            this.autenticado = true;
            this.user = sessionStorage.getItem('sigma_user_temp').toUpperCase();
            this.cargar();
          }
        }else{
            this.autenticado = false;
            this.$router.replace("/login");
        }
    },

    methods: {
      inicio(){
        this.$router.replace("/inicio");
      },

      cargar(){
        let url = this.$my_host + '/sigma-back/public/index.php/api/listar_historia_user'
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