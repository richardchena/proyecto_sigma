<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use \Datetime;

class Actividades extends Controller
{	
	public $permitidos;
	public $admin;

    public function __construct() {
		$this -> permitidos = ['RICHARD', 'CEL', 'PCHOME', 'IPHONE'];
		$this -> admin = ['RICHARDs', 'CEL'];
    }

    public function get_list_users(){
        return $this -> permitidos;
    }

    //Funcion que retorna la lista de todos los registros (Exclusivo para el jefe)
    //Pendientes
    public function listar_actual(){
        /*return $dato = DB::table('TABLA_REPORTES')
            ->where('ESTADO', '<>', 'FINALIZADO')
            ->Where('ESTADO', '<>', 'CANCELADO')
            ->orderBy('id')
            ->get();*/

        return DB::select("SELECT ID, FORMAT (FECHA, 'dd/MM/yyyy HH:mm') as FECHA, ASIGNADO, ASUNTO, ESTADO, WORKLINE, DIFICULTAD, DEADLINE, ENTREGA, USUARIO_CREACION, COMMENT
            FROM TABLA_REPORTES
            WHERE ESTADO NOT IN ('FINALIZADO', 'CANCELADO')");
    }

    //Finalizados
    public function listar_historia(){
        return DB::select("SELECT ID, FORMAT (FECHA, 'dd/MM/yyyy HH:mm') as FECHA, ASIGNADO, ASUNTO, ESTADO, WORKLINE, DIFICULTAD, FORMAT (DEADLINE, 'dd/MM/yyyy') as DEADLINE, 
            FORMAT (ENTREGA, 'dd/MM/yyyy HH:mm') as ENTREGA, USUARIO_CREACION, COMMENT
            FROM TABLA_REPORTES
            WHERE ESTADO IN ('FINALIZADO', 'CANCELADO')
            ORDER BY ID");
    }

    //Funcion que retorna la lista de todos los registros de un usuario determinado
    //Pendientes
    public function listar_actual_user(Request $request){
        $usuario = $request -> usuario;

        return DB::select("SELECT ID, FORMAT (FECHA, 'dd/MM/yyyy HH:mm') as FECHA, ASIGNADO, ASUNTO, ESTADO, WORKLINE, DIFICULTAD, DEADLINE, ENTREGA, USUARIO_CREACION, COMMENT
                           FROM TABLA_REPORTES
                           WHERE ASIGNADO = ? AND ESTADO NOT IN ('FINALIZADO', 'CANCELADO')
                           ORDER BY ID", [$usuario]);
    }

    //Finalizados
    public function listar_historia_user(Request $request){
        $usuario = $request -> usuario;

        /*return $dato = DB::table('TABLA_REPORTES')
            ->where('USUARIO_CREACION', '=', $usuario)
            ->where('ESTADO', '=', 'FINALIZADO')
            ->orWhere('ESTADO', '=', 'CANCELADO')
            ->orderBy('id')
            ->get();*/

        return DB::select("SELECT ID, FORMAT (FECHA, 'dd/MM/yyyy HH:mm') as FECHA, ASIGNADO, ASUNTO, ESTADO, WORKLINE, DIFICULTAD, FORMAT (DEADLINE, 'dd/MM/yyyy') as DEADLINE, 
									FORMAT (ENTREGA, 'dd/MM/yyyy HH:mm') as ENTREGA, USUARIO_CREACION, COMMENT
                           FROM TABLA_REPORTES
                           WHERE ASIGNADO = ? AND ESTADO IN ('FINALIZADO', 'CANCELADO')
                           ORDER BY ID", [$usuario]);
    }

    //Usuario Actual
    public function usuario_conectado(){
        $desktop = gethostbyaddr($_SERVER['REMOTE_ADDR']);

        foreach ($this -> usuarios_permitidos  as $username => $host){
            if ($host == $desktop){
                //USUARIOS ADMIN
                if($username == 'RICHARDs' || $username == 'CELU' || $username == 'PCHOMES')
                    return array(true, $username, $host, true);
                else
                    return array(true, $username, $host, false);
                break;
            }
        }

        return array(false, '', $desktop, false);
    }

    //Crear datos
    public function crear(Request $request){
        $ASUNTO = $request -> ASUNTO;
        $WORKLINE = $request -> WORKLINE;
        $DIFICULTAD = $request -> DIFICULTAD;
        $DEADLINE = $request -> DEADLINE;
        $USUARIO_CREACION = $request -> USUARIO_CREACION;
        $ASIGNADO = $request -> ASIGNADO;
        
        return DB::insert('INSERT INTO TABLA_REPORTES (ASIGNADO, ASUNTO, WORKLINE, DIFICULTAD, DEADLINE, USUARIO_CREACION) 
                    VALUES (?, ?, ?, ?, ?, ?)', [$ASIGNADO, $ASUNTO, $WORKLINE, $DIFICULTAD, $DEADLINE, $USUARIO_CREACION]);
        

        /*$var = new DateTime();
        $var2 = $var->format('Y-m-d h:i:s');
        $var3 = $var->format('d/m/Y h:i:s');

        $ASUNTO = $request -> ASUNTO;
        $WORKLINE = $request -> WORKLINE;
        $DIFICULTAD = $request -> DIFICULTAD;
        $DEADLINE = $request -> DEADLINE;
        $USUARIO_CREACION = $request -> USUARIO_CREACION;
        $ASIGNADO = $request -> ASIGNADO;
        
        DB::insert('INSERT INTO TABLA_REPORTES (ASIGNADO, ASUNTO, WORKLINE, DIFICULTAD, DEADLINE, USUARIO_CREACION, FECHA) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)', [$ASIGNADO, $ASUNTO, $WORKLINE, $DIFICULTAD, $DEADLINE, $USUARIO_CREACION, $var2]);
        
        return $var3;*/
    }

    //Actualizar datos
    public function actualizar(Request $request){
        $ID = $request -> ID;
        $ASUNTO = $request -> ASUNTO;
        $WORKLINE = $request -> WORKLINE;
        $ESTADO = $request -> ESTADO;
        $DIFICULTAD = $request -> DIFICULTAD;
        $DEADLINE = $request -> DEADLINE;
        $COMMENT = $request -> COMMENT;

        if($ESTADO == 'FINALIZADO'){
            return DB::update(
                'UPDATE TABLA_REPORTES SET ASUNTO = ?, WORKLINE = ?, ESTADO = ?, DIFICULTAD = ?, DEADLINE = ?, COMMENT = ?, ENTREGA = GETDATE() WHERE ID = ?',
                [$ASUNTO, $WORKLINE, $ESTADO, $DIFICULTAD, $DEADLINE, $COMMENT, $ID]);
        }else{
            return DB::update(
                'UPDATE TABLA_REPORTES SET ASUNTO = ?, WORKLINE = ?, ESTADO = ?, DIFICULTAD = ?, DEADLINE = ?, COMMENT = ? WHERE ID = ?',
                [$ASUNTO, $WORKLINE, $ESTADO, $DIFICULTAD, $DEADLINE, $COMMENT, $ID]);
        }
    }

    //Eliminar datos
    public function eliminar(Request $request){
        $ID = $request -> ID;
        return DB::delete('DELETE FROM TABLA_REPORTES WHERE ID = ?', [$ID]);
    }

    //METRICAS
    public function metricas(){
        return DB::select("
        WITH RESUMEN AS
            (SELECT WORKLINE, DATENAME(month,ENTREGA) AS MES, COUNT(WORKLINE) AS CANTIDAD
            FROM TABLA_REPORTES
            WHERE ESTADO = 'FINALIZADO'
            GROUP BY DATENAME(month,ENTREGA), WORKLINE)
        SELECT *
        FROM RESUMEN 
        PIVOT (SUM(CANTIDAD)
        FOR MES IN ([ENERO],[FEBRERO],[MARZO],[ABRIL],[MAYO],[JUNIO],[JULIO],[AGOSTO],[SEPTIEMBRE],[OCTUBRE],[NOVIEMBRE],[DICIEMBRE])) AS PIVOT_TABLE");
    }

	//EXTERNO
	public function get_permitidos_2(Request $request){
		for ($i = 0; $i < count($this -> permitidos); $i++) {
			if($this -> permitidos[$i] == strtoupper($request -> username)){
				return true;
			}
		}
		return false;
	}
	
	public function get_admin_2(Request $request){
		for ($i = 0; $i < count($this -> admin); $i++) {
			if($this -> admin[$i] == strtoupper($request -> username)){
				return true;
			}
		}
		return false;
	}
	
	

	//INTERNO
	public function get_permitidos($valor){
		for ($i = 0; $i < count($this -> permitidos); $i++) {
			if($this -> permitidos[$i] == $valor){
				return true;
			}
		}
		return false;
	}
	
	public function get_admin($valor){
		for ($i = 0; $i < count($this -> admin); $i++) {
			if($this -> admin[$i] == $valor){
				return true;
			}
		}
		return false;
	}
	
	
	//LOGIN
	public function login(Request $request){
		$username = $request -> username;
		$password = $request -> password;
		
		$valor = DB::select("SELECT USERNAME FROM USUARIOS WHERE USERNAME = ? AND PASS = ?", [$username, $password]);
				
		$check1 = false; //TRUE si esta correcta la autenticacion
		$check2 = false; //TRUE si tiene acceso
		$check3 = false; //TRUE si es ADMIN
		
		if($valor){
			$check1 = true;

			$check2 = $this -> get_permitidos($valor[0]->USERNAME);
			
			$check3 = $this -> get_admin($valor[0]->USERNAME);
			
			return [$check1, $check2, $check3];
		} 
		return [$check1, $check2, $check3];
	}
}
