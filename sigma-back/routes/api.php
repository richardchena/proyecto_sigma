<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('listar_actual', 'App\Http\Controllers\Actividades@listar_actual');
Route::get('listar_historia', 'App\Http\Controllers\Actividades@listar_historia');
Route::get('listar_actual_user', 'App\Http\Controllers\Actividades@listar_actual_user');
Route::get('listar_historia_user', 'App\Http\Controllers\Actividades@listar_historia_user');
Route::get('usuario_conectado', 'App\Http\Controllers\Actividades@usuario_conectado');
Route::get('actualizar', 'App\Http\Controllers\Actividades@actualizar');
Route::get('eliminar', 'App\Http\Controllers\Actividades@eliminar');
Route::get('crear', 'App\Http\Controllers\Actividades@crear');
Route::get('lista_usuarios', 'App\Http\Controllers\Actividades@get_list_users');
Route::get('metricas', 'App\Http\Controllers\Actividades@metricas');
Route::get('login', 'App\Http\Controllers\Actividades@login');
Route::get('check_user', 'App\Http\Controllers\Actividades@get_permitidos_2');
Route::get('check_admin', 'App\Http\Controllers\Actividades@get_admin_2');