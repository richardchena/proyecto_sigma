<template>
    <v-app style="background: gray"> 
      <v-main>
         <v-snackbar
            v-model="mensaje_error1"
            :timeout="-1"
            color="amber darken-4 black--text"
         >
            Falló la autenticación

            <template v-slot:action="{ attrs }">
            <v-btn
               color="white"
               text
               v-bind="attrs"
               @click="mensaje_error1 = false"
            >
               Cerrar
            </v-btn>
            </template>
         </v-snackbar>

         <v-snackbar
            v-model="mensaje_error2"
            :timeout="-1"
            color="amber darken-4 white--text"
         >
            Favor solicitar acceso a <a style="color: yellow" href="mailto:richard.cabrera@itau.com.py">richard.cabrera@itau.com.py</a>

            <template v-slot:action="{ attrs }">
            <v-btn
               color="black"
               text
               v-bind="attrs"
               @click="mensaje_error2 = false"
            >
               Cerrar
            </v-btn>
            </template>
         </v-snackbar>

         <v-container fluid fill-height>
            <v-layout align-center justify-center>
               <v-flex xs12 sm8 md4>
                  <v-card>
                     <v-toolbar elevation="0" color="orange dargen-4 white--text">
                        <div class="div_2">
                            <b><h1>Σ</h1></b>
                        </div>
                        <v-spacer></v-spacer>
                        <v-toolbar-title>¡Bienvenido a SIGMA!</v-toolbar-title>
                     </v-toolbar>
                     <v-card-text>
                        <v-form>
                           <v-text-field
                              style="margin-top:10px"
                              outlined
                              single-line
                              prepend-inner-icon="mdi-account"
                              name="login"
                              label="Username"
                              hint="Windows username"
                              type="text"
                              clearable
                              @input="validar"
                              v-model="sigma_user"
                           ></v-text-field>
                           <v-text-field
                              clearable
                              style="margin-top:-5px"
                              outlined
                              single-line
                              prepend-inner-icon="mdi-lock"
                              hint="Windows password"
                              :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                              :type="show1 ? 'text' : 'password'"
                              name="password"
                              label="Password"
                              @click:append="show1 = !show1"
                              @input="validar"
                              v-model="sigma_pass"
                           ></v-text-field>
                           <v-checkbox
                              label="Si no quieres iniciar sesión la próxima vez, please check here"
                              color="orange darken-3"
                              value="check"
                              v-model="check"
                           ></v-checkbox>
                        </v-form>
                     </v-card-text>
                     <v-divider style="margin-top:-15px"></v-divider>
                     <v-card-actions>
                        <v-spacer></v-spacer>
                        <!--<v-btn color="primary" to="/">INICIAR SESIÓN</v-btn>-->
                        <v-btn color="primary" :disabled="iniciar" @click="metodo()">INICIAR SESIÓN</v-btn>
                     </v-card-actions>
                  </v-card>
                  <h2>Desarrollado por <a style="color: rgba(0, 0, 0, 0.452)" href="mailto:richard.cabrera@itau.com.py">Richard Cabrera</a> de IMA</h2>
                  <h2>© 2022 Sigma Itaú PY v1.0.0</h2>
               </v-flex>
            </v-layout>
         </v-container>
      </v-main>
    </v-app>
</template>

<script>
    import axios from "axios"
    export default {
        name: 'login',
        data: () => ({
            show1: false,
            show2: true,
            sigma_user: null,
            sigma_pass: null,
            iniciar: true,
            check: null,
            mensaje_error1: false,
            mensaje_error2: false
        }),

        methods:{
           validar(){
              if(!this.sigma_user || !this.sigma_pass) {
                  this.iniciar = true;
              }
              else {
                  this.iniciar = false;
              }
           },

           metodo(){
              //alert('jijijijij');
               let url = this.$my_host + '/sigma-back/public/index.php/api/login';
               axios.get(url, {
                  params: {
                     username: this.sigma_user,
                     password: this.sigma_pass
                  }
               })
               .then(response=> {
                  if(!response.data[0]){
                     this.mensaje_error2 = false;
                     this.mensaje_error1 = true;
                  }else{
                     if(!response.data[1]){
                        this.mensaje_error1 = false;
                        this.mensaje_error2 = true
                     }
                     else{
                        this.mensaje_error1 = false;
                        this.mensaje_error2 = false;

                        if(this.check){
                           //alert('SE PRECIONO CHECK');
                           //GUARDAR DATOS
                           localStorage.setItem('sigma_user', this.sigma_user);
                           localStorage.setItem('sigma_admin', response.data[2])
                        }

                        //alert('NO SE PRECIONO');
                        sessionStorage.setItem('sigma_user_temp', this.sigma_user);
                        if(response.data[2]){
                           sessionStorage.setItem('sigma_admin_temp', true)
                           this.$router.replace("/admin");

                        }else{
                           sessionStorage.setItem('sigma_admin_temp', false);
                           this.$router.replace("/inicio");
                        }
                     }
                  }
               })
           }
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

  h2{
     font-size:15px;
     margin-top: 10px;
     text-align: center;
     color: rgba(0, 0, 0, 0.452);
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