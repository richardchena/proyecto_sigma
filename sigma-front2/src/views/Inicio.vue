<template>
  <v-app style="background: white">
    <v-system-bar v-if="autenticado" color="#ff8000" height="60">
      <div class="div_2">
          <b><h1>Σ</h1></b>
      </div>
      <h2 style="color: white; margin-left: 2%">SIGMA</h2>
      <v-btn
        style="margin-left: 3%; margin-top: -3px"
        @click="historial"
        color="yellow accent-2"
        text
      >
      VER HISTORIAL
      </v-btn>
      <v-spacer></v-spacer>
      <h2 style="color: white">¡Bienvenid@ {{this.user}}!</h2>
            <v-btn
              class="ma-2"
              color="yellow"
              x-small
              title="Cerrar sesión"
              @click="dialogLogout=true"
            >
              <v-icon color="black">
                mdi-logout-variant 
              </v-icon>
            </v-btn>
    </v-system-bar>

    <v-snackbar
      :timeout="-1"
      v-model="mensaje_error"
      color="deep-orange darken-4"
      height="70"
    >
      <strong>Favor completar todos los campos requeridos *</strong>
    </v-snackbar>

  <v-data-table
    :headers="headers"
    :items="desserts"
    class="elevation-1"
    v-if="autenticado"
    :loading="loader"
    loading-text="Porfavor espera miestras cargamos los registros para ti"
    :items-per-page="-1"
  >
    <template v-slot:top>
      <v-toolbar
        flat
      >
        <v-toolbar-title>Ingeniería de Metricas y Analytics</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-dialog
          v-model="dialog"
          max-width="500px"
        >
          <template v-slot:activator="{ on }">
            <v-btn
              color="primary"
              dark
              class="mb-2"
              
              v-on="on"
            >
              CREAR NUEVO
            </v-btn>
          </template>
          <v-card max-width="580px">
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>
            <v-divider></v-divider>

            <v-card-text>
              <v-container>
                <v-text-field 
                  v-model="editedItem.ASUNTO" 
                  label="ASUNTO *" 
                  outlined
                  counter
                  maxlength="200"
                >
                </v-text-field>

                <v-select
                    v-model="editedItem.WORKLINE"
                    :items="worklines()"
                    label="WORKLINE *"
                    outlined
                ></v-select>

                <v-select
                    v-model="editedItem.DIFICULTAD"
                    :items="difi()"
                    label="PRIORIDAD *"
                    outlined
                ></v-select>

                <v-menu
                    v-model="menu2"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                >
                    <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="editedItem.DEADLINE"
                        label="DEADLINE"
                        prepend-icon="mdi-calendar"
                        :readonly="true"
                        v-bind="attrs"
                        v-on="on"
                        outlined
                    ></v-text-field>
                    </template>
                    <v-date-picker
                    locale="es-AR"
                    no-title
                    v-model="editedItem.DEADLINE"
                    @input="menu2 = false"
                    :min="today"
                    >
                    </v-date-picker>
                </v-menu>
                
                <!--<div class="input-group-prepend" style="width: 30%; margin-top: 7%">
                    <label class="input-group-text" for="inputGroupSelect01">DEADLINE</label>
                </div>
                <input class="entrada" id="deadline" name="estimado" type="date" :min="today" v-model="editedItem.DEADLINE">
                -->
              </v-container>
            </v-card-text>

            <v-divider></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="red darken-1"
                text
                @click="close"
              >
                Cancelar
              </v-btn>
              <v-btn
                color="green darken-1"
                text
                @click="save"
              >
                Guardar
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="750px">
          <v-card class="blue-grey darken-3">
            <v-card-title class="justify-center" style="color: white;">¿Desea realmente eliminar "{{editedItem.ASUNTO}}"?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="info" text @click="closeDelete">Cancelar</v-btn>
              <v-btn color="error" text @click="deleteItemConfirm">OK</v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogLogout" max-width="350px">
          <v-card class="blue-grey darken-3">
              <v-card-title class="justify-center" style="color: white;">¿Cerrar sesión?</v-card-title>
              <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="info" text @click="dialogLogout=false">CANCELAR</v-btn>
              <v-btn color="error" text @click="logout()">OK</v-btn>
              <v-spacer></v-spacer>
              </v-card-actions>
          </v-card>
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
                filled
                hint="¡Por favor brinde solo detalles relevantes!"
                label="Detalles de la solicitud"
                solo
              >
              </v-textarea>
              <v-divider></v-divider>
              <div style="margin-left: 60%; margin-top: 9px">
                <v-btn color="info" text @click="closeComment">Cancelar</v-btn>
                <v-btn color="error" text @click="save">OK</v-btn>
              </div>
            </v-container>
          </v-card>
        </v-dialog>
        
        <v-dialog v-model="dialogStatus" max-width="500px">
          <v-card>
            <v-card-title>
              <span class="text-h5">Modificar Estado #{{editedItem.ID}}</span>
            </v-card-title>
            <v-divider></v-divider>

            <v-card-text>
              <v-row style="margin-top: 5%; margin-left: 2%">
                <div class="lab">
                  <label class="label_yo">ELEGIR NUEVO ESTADO</label>
                </div>

                <select v-model="editedItem.ESTADO" id="dffv" class="sel"> 
                  <option disabled="disabled">Elige una opción</option>
                  <option select value="FINALIZADO">FINALIZADO</option>
                  <option value="PENDIENTE">PENDIENTE</option>
                  <option value="CANCELADO">CANCELADO</option>
                  <option value="EN CURSO">EN CURSO</option>
                </select>
              </v-row>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="info" text @click="closeStatus">Cancelar</v-btn>
              <v-btn color="error" text @click="save">OK</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:[`item.DIFICULTAD`]="{ item }">
      <v-chip
        :color="getColor(item.DIFICULTAD)"
      >
        {{ item.DIFICULTAD }}
      </v-chip>
    </template>
    <template v-slot:[`item.actions`]="{ item }" >
      <div class="text-center">
        <v-btn
          color="warning"
          dark
          class="mb-2"
          
          
          @click="editItem(item)"
        >
          Editar
        </v-btn>
        <v-btn
          color="info"
          dark
          class="mb-2"
          
         
          @click="editStatus(item)"
        >
          Estado
        </v-btn>
        <v-btn
          color="success"
          dark
          class="mb-2"
          
         
          @click="editComment(item)"
        >
          Comentario
        </v-btn>
        <v-btn
          color="error"
          dark
          class="mb-2"
          
       
          @click="deleteItem(item)"
        >
          Eliminar
        </v-btn>
      </div>
    </template>
    <template v-slot:no-data>
        <strong>No tienes nada pendiente, disfruta del día 😍😁🐓</strong>
    </template>
  </v-data-table>

    <v-main>
      <router-view/>
    </v-main>
  </v-app>
</template>

<script>
    import axios from 'axios';
    export default {
      name: 'inicio',
    data: () => ({
      today: new Date().toISOString().slice(0, 10),
      autenticado: false,
      dialogLogout: false,
      user: null,
      loader: true,
      menu2: false,
      mensaje_error: false,
      dialog: false,
      dialogDelete: false,
      dialogStatus: false,
      dialogComment: false,
      resp_upd: null,
      valor: null,
      headers: [
        { text: 'ID', align: 'center', value: 'ID', class:'orange dargen-4 black--text'},
        { text: 'FECHA', align: 'center', value: 'FECHA', class:'orange dargen-4 black--text'},
        { text: 'ASUNTO', align: 'center', value: 'ASUNTO', class:'orange dargen-7 black--text'},
        { text: 'WORKLINE', align: 'center', value: 'WORKLINE', class:'orange dargen-7 black--text'},
        { text: 'ESTADO', align: 'center', value: 'ESTADO', class:'orange dargen-7 black--text'},
        { text: 'PRIORIDAD', align: 'center', value: 'DIFICULTAD', class:'orange dargen-7 black--text'},
        { text: 'DEADLINE', align: 'center', value: 'DEADLINE', class:'orange dargen-7 black--text'},
        { text: 'CONTROLES', width: '471px', align: 'center', value: 'actions', sortable: false, class:'orange dargen-7 black--text'},
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
        COMMENT: ''
      },
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Registrar Solicitud' : 'Modificar Solicitud #' + this.editedItem.ID
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },

      dialogLogout (val) {
        val || this.closeLogout()
      },

      dialogStatus (val) {
        val || this.closeStatus()
      },

      dialogComment (val) {
        val || this.closeComment()
      },
    },

    created () {
      this.axios_funcion();
    },

    methods: {
      logout(){
        this.dialogLogout=false;
        localStorage.removeItem('sigma_user');
        localStorage.removeItem('sigma_admin');
        sessionStorage.removeItem('sigma_user_temp');
        sessionStorage.removeItem('sigma_admin_temp');
        this.$router.replace("/login");
      },

      difi(){
          return ["BAJA", "MEDIA", "ALTA"];
      },

      worklines(){
          return ["ANALISIS", "AUTOMATIZACION", "CAPACITY", "HUDDLE", "MODELO", "REPORTE", "RUDI"];
      },

      get_sms(valor){
        if (valor == 'ALTA'){
          alert('FAVOR ATENDER ESTE PEDIDO EN LA BREVEDAD POSIBLE!!!')
        }
      },

      getColor (valor) {
        if (valor == 'ALTA') {
          return 'red white--text'

        }else if (valor == 'MEDIA'){
          return 'yellow'

        }else{
          return 'green white--text'
        }
      },

      closeLogout () {
        this.dialogLogout = false
      },

      metricas(){
        this.autenticado = false;
        this.$router.replace("/metrica").catch(()=>{});
      },

      historial(){
        this.autenticado = false;
        this.$router.replace("/history").catch(()=>{});
      },

      admin(){
        this.autenticado = false;
        this.$router.replace("/admin").catch(()=>{});
      },

      historial_admin(){
        this.autenticado = false;
        this.$router.replace("/history_admin").catch(()=>{});
      },

      axios_funcion(){
        //this.user = this.$usuario_log;
        this.user = sessionStorage.getItem('sigma_user_temp').toUpperCase();
        if(this.user.trim().length > 0){
          if(sessionStorage.getItem('sigma_admin_temp') == 'true'){
            this.$router.replace("/admin");
          }else{
            let url2 = this.$my_host + '/sigma-back/public/index.php/api/listar_actual_user';
            this.autenticado = true;
            axios.get(url2, {
                params: {
                  usuario: this.user
                }})
            .then(response=> {
              if(response.data){
                this.loader = false;
                this.desserts = response.data;
              }
            })
          }
        }else{
          this.$router.replace("/login");
        }        
      },

      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogDelete = true
      },

      editStatus(item){
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogStatus = true;
      },

      editComment(item){
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogComment = true;
      },

      deleteItemConfirm () {
        let url = this.$my_host + '/sigma-back/public/index.php/api/eliminar';
        axios.get(url, {
          params: {
            ID: this.editedItem.ID
          }
        })
        .then(response=> {
          if(response.data == 1){
            this.desserts.splice(this.editedIndex, 1)
          }
        })
        this.closeDelete()
      },

      close () {
        this.dialog = false;
        this.mensaje_error = false;
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      closeStatus(){
        this.dialogStatus = false;
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      closeComment(){
        this.dialogComment = false;
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })

      },

      save () {
        if(this.editedItem.ASUNTO.trim() == '' || this.editedItem.WORKLINE.trim() == '' || this.editedItem.DIFICULTAD.trim() == ''){
          this.mensaje_error = true;

        }else{
          if (this.editedIndex > -1) {
            let url = this.$my_host + '/sigma-back/public/index.php/api/actualizar';
            axios.get(url, {
              params: {
                ID: this.editedItem.ID,
                ASUNTO: this.editedItem.ASUNTO,
                WORKLINE: this.editedItem.WORKLINE,
                ESTADO: this.editedItem.ESTADO,
                DIFICULTAD: this.editedItem.DIFICULTAD,
                DEADLINE: this.editedItem.DEADLINE,
                COMMENT: this.editedItem.COMMENT
              }
            })
            //.then(response=> {
              //if(response.data == 1){
                Object.assign(this.desserts[this.editedIndex], this.editedItem);
                //alert(response.data);
            //  })
            if(this.editedItem.ESTADO == 'FINALIZADO' || this.editedItem.ESTADO == 'CANCELADO'){
              this.desserts.splice(this.editedIndex, 1)

            }else{
              Object.assign(this.desserts[this.editedIndex], this.editedItem);
            }
          } else {
              let url = this.$my_host + '/sigma-back/public/index.php/api/crear';
              axios.get(url, {
                params: {
                  ASUNTO: this.editedItem.ASUNTO,
                  WORKLINE: this.editedItem.WORKLINE,
                  DIFICULTAD: this.editedItem.DIFICULTAD,
                  DEADLINE: this.editedItem.DEADLINE,
                  USUARIO_CREACION: this.user,
                  ASIGNADO: this.user
                }
              });
              //this.desserts.push(this.editedItem)
              this.$router.go()
              //this.axios_funcion();
          }

          this.close();
          this.closeStatus();
          this.closeComment();
          this.mensaje_error = false;
        }

        //alert(this.editedItem.ID);
        //aqui guardar en la base de datos con el ID en cuestion
        //Desabilitar boton si no esta escrito
      },
    },
  }
</script>


<style scoped>
  .label_yo{
    margin-left: 5%;
    margin-top: 7px;
    position: absolute;
  }

  .lab{
    background: #E6ECEF;
    border-radius: 5px 0px 0px 5px;
    height: 40px;
    width: 45%;
    border: 2px solid rgb(175, 175, 175);
  }

  .sel{
    width: 50%;
    height: 40px;
    border: 2px solid rgba(0, 0, 0, 0.616);
    text-align: center;
  }

  .sele{
  border: 0;
  outline: 0;
  background-color: transparent;
  }

  .v-btn {
    margin-right: 1%;
    margin-top: 1%;
  }

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