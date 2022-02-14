<template>
    <v-app style="background: gray"> 
        <div class="contenedor">
            <div class="hijo" style="text-align: center;">
                <p>Error: Acceso Denegado</p>
                Contactar con <a style="color: orange" href="mailto:richard.cabrera@itau.com.py">richard.cabrera@itau.com.py</a> para acceder
                <p></p>
                ID Equipo: {{this.$equipo}}
                <p></p>
            </div>
        </div>
    </v-app>
</template>

<script>
export default {
    name: 'error',
    methods:{
        login_get(){
            if(this.$usuario_log.trim().length > 0){
                if(this.$resp_admin){
                    this.$router.replace("/admin");
                }else{
                    this.$router.replace("/inicio");
                }
            }
        }
    },
    created(){
        this.login_get()
    }
}
</script>

<style>
    .super_div{
        background-color: gray;
    }

    .contenedor{
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 15%;
    }
    .hijo {
        background: #525252;
        width: 600px;
        border-style: solid;
        font-family: Arial, Helvetica, sans-serif;
        border-color: black;
        color: white;
        font-size: 20px;
    }

    p {
        font-family: Arial, Helvetica, sans-serif;
        text-align: center;
        font-size: 30px;
        color: white;
    }
</style>