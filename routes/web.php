<?php

Route::get('/', function () {
    return view('auth/login');
});
/* RUTA PARA CAMBIAR EL LENGUAJE */
Route::get('lang/{lang}', function($lang) {
  \Session::put('lang', $lang);
  return \Redirect::back();

})->middleware('web')->name('change_lang');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('master/buscar/{dato}/{type?}', 'MasterController@getSoC');
Route::group(['middleware' => 'auth'], function () {
    Route::get('commandBackup', function () {
        \Artisan::call('backup:run', ['--only-db' => true]);
        return "successfully!";
    });

    /* REGISTRO DE LOG DE ACTIVIDADES DE USUARIOS */
    Route::get('logActivity', 'LogActivityController@index')->name('logActivity.index');
    Route::get('logActivity/all', 'LogActivityController@getAll')->name('logActivity.getAll');

    /* RUTA PARA ACCEDER A LOS ARCHIVOS GUARDADOS EN EL SISTEMA */
    Route::get('storage/{archivo}', function ($archivo) {
        $public_path = public_path();
        $url         = $public_path . '/storage/' . $archivo;
        //verificamos si el archivo existe y lo retornamos
        if (Storage::exists($archivo)) {
            return response()->download($url);
        }
        //si no se encuentra lanzamos un error 404.
        abort(404);

    });
    /* CONSULTAR SHIPPERS O CONSIGNEE */
    Route::get('consulta', 'ConsultaController@index')->name('consulta.index');
    Route::get('consulta/all', 'ConsultaController@getAll')->name('consulta.getAll');
    Route::get('consulta/pdf', 'ConsultaController@pdf')->name('consulta.pdf');

    /* VISTA RESULTADOS DE LA BUSQUEDA DEL SELECT EN EL NAVBAR */
    Route::get('resultSearch/{id}', 'ResultSearchController@index')->name('resultSearch.index');

    /* VISTA MANTENIMIENTO */
    Route::get('mantenimiento', 'MantenimientoController@index')->name('mantenimiento.index');

    /*--- MODULO USER ---*/
    Route::resource('user', 'UserController', ['except' => ['show', 'create', 'edit']]);
    Route::get('user/delete/{id}/{logical?}', 'UserController@delete')->name('user.delete')->middleware('permission:user.delete');
    Route::get('user/restaurar/{id}', 'UserController@restaurar');
    Route::get('user/all', 'UserController@getAll')->name('datatable/all');
    Route::get('user/getDataSelect/{table}', 'UserController@getDataSelect');
    Route::post('user/validarUsername', 'UserController@validarUsername');
    Route::get('user/getNameAgenciaUser', 'UserController@getNameAgenciaUser');

    /*--- MODULO ROLES ---*/
    Route::resource('rol', 'RolController', ['except' => ['show', 'create', 'edit']]);
    Route::get('rol/all', 'RolController@getAll')->name('datatable/all');
    Route::get('rol/allPermissions', 'RolController@getAllPermissions')->name('datatable.allPermissions');
    Route::get('rol/delete/{id}/{logical?}', 'RolController@delete')->name('rol.delete');
    Route::get('rol/restaurar/{id}', 'RolController@restaurar');
    Route::get('rol/getPermissions/{id}', 'RolController@getPermissions');

    /*--- MODULO ACCESS CONTROL ---*/
    Route::resource('accessControl', 'AccessControlController', ['except' => ['show', 'create', 'edit']]);
    Route::get('accessControl/all', 'AccessControlController@getAll')->name('datatable/all');
    Route::get('accessControl/allPermissions', 'AccessControlController@getAllPermissions')->name('datatable.allPermissions');
    Route::get('accessControl/delete/{id}/{logical?}', 'AccessControlController@delete')->name('accessControl.delete');
    Route::get('accessControl/getPermisionsRole/{role_id}', 'AccessControlController@getPermisionsRole');
    Route::get('accessControl/getSpecialPermisions/{module}/{role_id}', 'AccessControlController@getSpecialPermisions');
    Route::post('accessControl/saveSpecialPermissions', 'AccessControlController@saveSpecialPermissions');

    /***********************************************************************************************************************/

    /*--- MASTER ---*/
    Route::resource('master', 'MasterController');
    // Reg al final, por que genera conficto el hecho de que el show se llama con "nombre/variable"
    Route::get('master/create/{master}', 'MasterController@create');
    Route::get('master/all/reg', 'MasterController@getAll');
    Route::get('master/delete/{id}/{logical?}', 'MasterController@delete')->name('modulo.delete');
    Route::get('master/restaurar/{id}', 'MasterController@restaurar');
    Route::get('master/imprimir/{id_master}/{simple?}', 'MasterController@imprimir');
    Route::get('master/getOtherCharges/{id}', 'MasterController@getOtherCharges');
    Route::get('master/imprimirLabel/{id_master}', 'MasterController@imprimirLabel');
    Route::get('master/imprimirGuias/{consolidado_id}/{option?}', 'MasterController@imprimirGuias');

    /*--- MODULO TRACKINGS ---*/
    Route::resource('tracking', 'TrackingController', ['except' => ['show', 'create', 'edit', 'update']]);
    Route::post('tracking/addOrDeleteDocument', 'TrackingController@addOrDeleteDocument');
    Route::get('tracking/all/{grid?}/{add?}/{id?}/{req_consignee?}', 'TrackingController@getAll')->name('datatable/all');
    Route::get('tracking/delete/{id}/{logical?}', 'TrackingController@delete')->name('tracking.delete');
    Route::get('tracking/getAllShipperConsignee/{table}', 'TrackingController@getAllShipperConsignee');
    Route::get('tracking/searchTracking/{tracking}', 'TrackingController@searchTracking');
    Route::post('tracking/validar_tracking', 'TrackingController@validar_tracking');

    /*--- MODULO MODULOS ---*/
    Route::resource('modulo', 'ModuloController', ['except' => ['show', 'create', 'edit']]);
    Route::get('modulo/all', 'ModuloController@getAll')->name('datatable/all');
    Route::get('modulo/delete/{id}/{logical?}', 'ModuloController@delete')->name('modulo.delete');
    Route::get('modulo/restaurar/{id}', 'ModuloController@restaurar');

    /*---- Rutas para la tabla MaestraMultiple ----*/
    Route::get('administracion/{type}/all', 'MaestraMultipleController@getAll');
    Route::get('administracion/{type}', 'MaestraMultipleController@index')->name('administracion.index');
    Route::get('administracion/{type}/restaurar/{id}', 'MaestraMultipleController@restaurar');
    Route::get('administracion/{type}/delete/{id}/{logical?}', 'MaestraMultipleController@delete')->name('administracion.delete');
    Route::post('administracion/{type}', 'MaestraMultipleController@store')->name('administracion.store');
    Route::put('administracion/{type}/{id}', 'MaestraMultipleController@update')->name('administracion.update');
    Route::delete('administracion/{type}/{id}', 'MaestraMultipleController@destroy')->name('administracion.destroy');
    Route::get('administracion/{type}/selectInput/{tableName}', 'MaestraMultipleController@selectInput');

    /*--- MODULO PAIS ---*/
    Route::resource('pais', 'PaisController', ['except' => ['show', 'create', 'edit']]);
    Route::get('pais/all', 'PaisController@getAll')->name('datatable/all');
    Route::get('pais/delete/{id}/{logical?}', 'PaisController@delete')->name('pais.delete');
    Route::get('pais/restaurar/{id}', 'PaisController@restaurar');

    /*--- MODULO DEPTO - ESTADO ---*/
    Route::resource('departamento', 'DepartamentoController', ['except' => ['show', 'create', 'edit']]);
    Route::get('departamento/all', 'DepartamentoController@getAll')->name('datatable/all');
    Route::get('departamento/delete/{id}/{logical?}', 'DepartamentoController@delete')->name('departamento.delete');
    Route::get('departamento/restaurar/{id}', 'DepartamentoController@restaurar');
    Route::get('departamento/selectInput/{tableName}', 'DepartamentoController@selectInput');

    /*--- MODULO CIUDAD ---*/
    Route::resource('ciudad', 'CiudadController', ['except' => ['show', 'create', 'edit']]);
    Route::get('ciudad/all', 'CiudadController@getAll')->name('datatable/all');
    Route::get('ciudad/delete/{id}/{logical?}', 'CiudadController@delete')->name('ciudad.delete');
    Route::get('ciudad/restaurar/{id}', 'CiudadController@restaurar');
    Route::get('ciudad/selectInput/{tableName}/{idCondition?}', 'CiudadController@selectInput');

    /*--- MODULO AEROLINES - AEROPUERTOS ---*/
/*    Route::resource('transport/{type}', 'AerolineasAeropuertosController', ['except' => ['show', 'create', 'edit', 'update']]);*/
    Route::delete('transport/{id}', 'AerolineasAeropuertosController@destroy')->name('transport.destroy');
    Route::post('transport/{type}', 'AerolineasAeropuertosController@store')->name('transport.store');
    Route::put('transport/{type}/{id}', 'AerolineasAeropuertosController@update');
    Route::get('transport/{type}', 'AerolineasAeropuertosController@index')->name('transport.index');
    Route::put('transport/{type}/{id}', 'AerolineasAeropuertosController@update')->name('transport.update');
    Route::get('transport/{type}/all', 'AerolineasAeropuertosController@getAll')->name('datatable/all');
    Route::get('transport/delete/{id}/{logical?}', 'AerolineasAeropuertosController@delete')->name('transport.delete');
    Route::get('transport/{type}/restaurar/{id}', 'AerolineasAeropuertosController@restaurar');
    Route::get('transport/selectInput/{tableName}', 'AerolineasAeropuertosController@selectInput');

    /*--- MODULO SERVICIOS ---*/
    Route::resource('servicios', 'ServiciosController', ['except' => ['show', 'create', 'edit']]);
    Route::get('servicios/all/{id_embarque?}', 'ServiciosController@getAll')->name('datatable/all');
    Route::get('servicios/getAllServiciosAgencia/{id_embarque?}', 'ServiciosController@getAllServiciosAgencia');
    Route::get('servicios/delete/{id}/{logical?}', 'ServiciosController@delete')->name('servicios.delete');
    Route::get('servicios/restaurar/{id}', 'ServiciosController@restaurar');

    /*--- MODULO TRANSPORTADOR ---*/
    Route::resource('transportador', 'TransportadorController', ['except' => ['show', 'create', 'edit']]);
    Route::get('transportador/all', 'TransportadorController@getAll')->name('datatable/all');
    Route::get('transportador/delete/{id}/{logical?}', 'TransportadorController@delete')->name('transportador.delete');
    Route::get('transportador/restaurar/{id}', 'TransportadorController@restaurar');

    /*--- MODULO STATUS ---*/
    Route::resource('status', 'StatusController', ['except' => ['show', 'create', 'edit']]);
    Route::get('status/all', 'StatusController@getAll')->name('datatable/all');
    Route::get('status/delete/{id}/{logical?}', 'StatusController@delete')->name('status.delete');
    Route::get('status/restaurar/{id}', 'StatusController@restaurar');
    Route::get('status/getDataSelect', 'StatusController@getDataSelect');
    Route::get('status/getDataSelectModalTagGuia', 'StatusController@getDataSelectModalTagGuia');

    /*--- MODULO STATUS-REPORT ---*/
    Route::resource('statusReport', 'StatusReportController', ['except' => ['show', 'create', 'edit']]);
    Route::get('statusReport/all', 'StatusReportController@getAll')->name('datatable/all');
    Route::get('statusReport/getAllGrid/{id_documento}', 'StatusReportController@getAllGrid');
    Route::get('statusReport/delete/{id}/{logical?}', 'StatusReportController@delete')->name('statusReport.delete');
    Route::get('statusReport/restaurar/{id}', 'StatusReportController@restaurar');
    Route::get('statusReport/getStatusByIdDetalle/{id}', 'StatusReportController@getStatusByIdDetalle');

    /*--- MODULO ARANCEL ---*/
    Route::resource('arancel', 'ArancelController', ['except' => ['show', 'create', 'edit']]);
    Route::get('arancel/all', 'ArancelController@getAll')->name('datatable/all');
    Route::get('arancel/delete/{id}/{logical?}', 'ArancelController@delete')->name('arancel.delete');
    Route::get('arancel/restaurar/{id}', 'ArancelController@restaurar');
    Route::get('arancel/getPositionById/{id}', 'ArancelController@getPositionById');

    /*--- MODULO AGENCIA ---*/
    Route::resource('agencia', 'AgenciaController', ['except' => ['show', 'update']]);
    Route::put('agencia/{id}', 'AgenciaController@update')->name('agencia.update')->middleware('permission:agencia.update');
    Route::put('agencia/updateDetail/{id_detail}', 'AgenciaController@updateDetail')->name('agencia.update_detail');
    Route::get('agencia/all', 'AgenciaController@getAll')->name('datatable/all');
    Route::get('agencia/{id_agencia}/allUrls', 'AgenciaController@getAllUrls');
    Route::get('agencia/delete/{id}/{logical?}/{table?}', 'AgenciaController@delete')->name('agencia.delete')->middleware('permission:agencia.delete');
    Route::get('agencia/restaurar/{id}', 'AgenciaController@restaurar');
    Route::get('agencia/selectInput/{tableName}', 'AgenciaController@selectInput');

    /*--- MODULO REMITENTES ---*/
    Route::resource('shipper', 'ShipperController', ['except' => ['show', 'create', 'edit']]);
    Route::get('shipper/all/{data?}/{id_consignee?}/{id_agencia?}', 'ShipperController@getAll')->name('datatable/all');
    Route::get('shipper/delete/{id}/{logical?}', 'ShipperController@delete')->name('shipper.delete');
    Route::get('shipper/restaurar/{id}', 'ShipperController@restaurar');
    Route::get('shipper/selectInput/{tableName}', 'ShipperController@selectInput');
    Route::get('shipper/getDataById/{id}', 'ShipperController@getDataById');

    /*--- MODULO DESTINATARIOS ---*/
    Route::resource('consignee', 'ConsigneeController', ['except' => ['show', 'create', 'edit']]);
    Route::get('consignee/all/{data?}/{id_shipper?}/{id_agencia?}', 'ConsigneeController@getAll')->name('datatable/all');
    Route::get('consignee/delete/{id}/{logical?}', 'ConsigneeController@delete')->name('arancel.delete');
    Route::get('consignee/restaurar/{id}', 'ConsigneeController@restaurar');
    Route::get('consignee/selectInput/{tableName}', 'ConsigneeController@selectInput');
    Route::get('consignee/getDataById/{id}', 'ConsigneeController@getDataById');
    Route::get('consignee/generarCasillero/{id}', 'ConsigneeController@generarCasillero');
    Route::get('consignee/getConsigneesMonth', 'ConsigneeController@getConsigneesMonth');

    /*--- MODULO EMAIL TEMPLATES ---*/
    Route::resource('emailTemplate', 'EmailTemplateController', ['except' => ['show', 'create', 'edit']]);
    Route::get('emailTemplate/all', 'EmailTemplateController@getAll')->name('datatable/all');
    Route::get('emailTemplate/delete/{id}/{logical?}', 'EmailTemplateController@delete')->name('emailTemplate.delete');
    Route::get('emailTemplate/restaurar/{id}', 'EmailTemplateController@restaurar');

    /*--- MODULO TIPO DOCUMENTOS ---*/
    Route::resource('tipoDocumento', 'TipoDocumentoController', ['except' => ['show', 'create', 'edit']]);
    Route::get('tipoDocumento/all', 'TipoDocumentoController@getAll')->name('datatable/all');
    Route::get('tipoDocumento/delete/{id}/{logical?}', 'TipoDocumentoController@delete')->name('tipoDocumento.delete');
    Route::get('tipoDocumento/restaurar/{id}', 'TipoDocumentoController@restaurar');
    Route::get('tipoDocumento/selectInput/{tableName}', 'TipoDocumentoController@selectInput');
    Route::get('tipoDocumento/getPlantillasEmail', 'TipoDocumentoController@getPlantillasEmail');

    /*--- MODULO DOCUMENTO ---*/
    Route::resource('documento', 'DocumentoController', ['except' => ['create']]);

    Route::post('documento/insertDetail', 'DocumentoController@insertDetail')->name('documento.insertDetail');
    Route::post('documento/editDetail', 'DocumentoController@editDetail')->name('documento.editDetail');
    Route::post('documento/updatedDocument/{id}', 'DocumentoController@update')->name('documento.update');
    Route::post('documento/ajaxCreate/{document}', 'DocumentoController@ajaxCreate')->name('documento.ajaxCreate');
    Route::post('documento/{id}/additionalChargues', 'DocumentoController@additionalChargues')->name('documento.additionalChargues');
    Route::post('documento/ajaxCreateNota/{id}', 'DocumentoController@ajaxCreateNota')->name('documento.ajaxCreateNota');
    Route::post('documento/{id}/createContactsConsolidadoDetalle', 'DocumentoController@createContactsConsolidadoDetalle');
    Route::post('documento/{id}/addStatusToGuias', 'DocumentoController@addStatusToGuias')->name('documento.addStatusToGuias');
    Route::post('documento/{id}/agruparGuiasConsolidadoCreate', 'DocumentoController@agruparGuiasConsolidadoCreate');
    Route::get('documento/{id}/removerGuiaAgrupada/{id_detalle}/{id_guia_detalle}', 'DocumentoController@removerGuiaAgrupada')->name('documento.removerGuiaAgrupada');
    Route::get('documento/sendEmailDocument/{id}', 'DocumentoController@sendEmailDocument');
    Route::get('documento/{id}/deleteDetailConsolidado/{id_detail}/{logical}', 'DocumentoController@deleteDetailConsolidado')->name('documento.deleteDetailConsolidado');
    Route::get('documento/{id}/liquidar', 'DocumentoController@liquidar');
    Route::get('documento/{id}/additionalChargues/getAll/{documento_id}', 'DocumentoController@additionalCharguesGetAll')->name('documento.charguesGetAll');
    Route::get('documento/{id}/additionalChargues/delete/{chargue_id}', 'DocumentoController@additionalCharguesDelete')->name('documento.additionalCharguesDelete');

    Route::put('documento/updateDetail/{id_detail}', 'DocumentoController@updateDetail')->name('documento.updateDetail');
    Route::post('documento/{id}/updateDetailConsolidado/', 'DocumentoController@updateDetailConsolidado')->name('datatable.updateDetailConsolidado');
    Route::post('documento/{id}/updateDetailDocument/', 'DocumentoController@updateDetailDocument')->name('datatable.updateDetailDocument');
    Route::get('documento/all/{tableName}', 'DocumentoController@getAll')->name('datatable.all');
    Route::get('documento/delete/{id}/{logical?}/{table?}', 'DocumentoController@delete')->name('documento.delete');
    Route::get('documento/restaurar/{id}/{table?}', 'DocumentoController@restaurar');
    Route::get('documento/selectInput/{tableName}', 'DocumentoController@selectInput');
    Route::get('documento/create/{document}', 'DocumentoController@create')->name('documento.create');
    Route::get('documento/{id}/buscarGuias/{num_guia}/{num_bolsa}/{pais_id}', 'DocumentoController@buscarGuias');
    Route::get('documento/{id}/getAllGuiasDisponibles/{pais_id?}/{transporte_id?}', 'DocumentoController@getAllGuiasDisponibles');
    Route::get('documento/{id}/getAllConsolidadoDetalle/{num_bolsa?}', 'DocumentoController@getAllConsolidadoDetalle');
    Route::get('documento/{id}/restoreShipperConsignee/{id_detalle}/{table}', 'DocumentoController@restoreShipperConsignee');
    Route::get('documento/getDataSelectWarehousesModalTagGuia/{id}', 'DocumentoController@getDataSelectWarehousesModalTagGuia');
    Route::get('documento/getAllGridNotas/{id_documento}', 'DocumentoController@getAllGridNotas');
    Route::get('notas/delete/{id}/{logical?}', 'DocumentoController@deleteNota')->name('documento.deleteNota');
    Route::get('documento/getHistoryConsignee/{id}', 'DocumentoController@getHistoryConsignee');
    Route::get('documento/getHistoryDocument/{document}', 'DocumentoController@getHistoryDocument');
    Route::get('documento/{id}/getGuiasAgrupar/{id_detalle}', 'DocumentoController@getGuiasAgrupar');
    Route::put('documento/{id}/updatePositionArancel', 'DocumentoController@updatePositionArancel');
    Route::get('documento/{id}/getDataDetailDocument', 'DocumentoController@getDataDetailDocument');
    Route::get('documento/{id}/getBoxesConsolidado', 'DocumentoController@getBoxesConsolidado');
    Route::get('documento/{id}/removeBoxConsolidado/{num_bolsa}', 'DocumentoController@removeBoxConsolidado');
    Route::get('documento/{id}/changeBoxConsolidado/{num_bolsa}/{consol_id}', 'DocumentoController@changeBoxConsolidado');
    Route::get('documento/getDataByDocument/{id}', 'DocumentoController@getDataByDocument');

    /*  REPORTES - IMPRESIONES EN PDF */
    Route::get('impresion-documento/{id}/{document}/{id_detalle?}', 'DocumentoController@pdf')->name('documento.pdf');
    Route::get('impresion-documento-label/{id}/{document}/{id_detalle?}/{consolidado?}', 'DocumentoController@pdfLabel')->name('documento.pdfLabel');
    Route::get('impresion-documento/pdfContrato', 'DocumentoController@pdfContrato')->name('documento.pdfContrato');
    Route::get('impresion-documento/pdfTsa', 'DocumentoController@pdfTsa')->name('documento.pdfTsa');

    /* PREALERTA */
    Route::get('prealerta', 'PrealertaController@prealertaList')->name('prealerta.list')->middleware('permission:prealerta.list');

    /*--- AEROLINEA INVENTARIO ---*/
    Route::get('aerolinea_inventario/delete/{id}', 'AerolineasInventarioController@delete')->name('aerolinea_inventario.delete');
    Route::put('aerolinea_inventario/{id}', 'AerolineasInventarioController@update')->name('aerolinea_inventario.update');
    Route::post('aerolinea_inventario', 'AerolineasInventarioController@store')->name('aerolinea_inventario.store');
    Route::get('aerolinea_inventario/get/{aerolinea}', 'AerolineasInventarioController@getByAerolinea');
    Route::get('aerolinea_inventario/all', 'AerolineasInventarioController@getAll');
    Route::get('aerolinea_inventario', 'AerolineasInventarioController@index')->name('aerolinea_inventario.index');

    /*--- MODULO CLIENTES ---*/
    Route::resource('clientes', 'ClienteController', ['except' => ['show', 'create', 'edit']]);
    Route::get('clientes/all', 'ClienteController@getAll')->name('datatable/all');
    Route::get('clientes/delete/{id}/{logical?}', 'ClienteController@delete')->name('arancel.delete');
    Route::get('clientes/restaurar/{id}', 'ClienteController@restaurar');
    Route::get('clientes/selectInput/{tableName}', 'ClienteController@selectInput');

    /*--- MODULO BL ---*/
    Route::resource('bill', 'BillLadingController', ['except' => ['show', 'create']]);
    Route::get('bill/create/{bill?}', 'BillLadingController@create');
    Route::get('bill/all', 'BillLadingController@getAll')->name('datatable/all');
    Route::get('bill/delete/{id}/{logical?}', 'BillLadingController@delete')->name('BillLading.delete');
    Route::get('bill/imprimir/{id_bill}/{simple?}', 'BillLadingController@imprimir');
    Route::get('bill/restaurar/{id}', 'BillLadingController@restaurar');
    Route::get('bill/getParties', 'BillLadingController@getParties');
    Route::post('bill/createPartie', 'BillLadingController@createPartie');
    Route::put('bill/editPartie/{id}', 'BillLadingController@editPartie');
    Route::delete('bill/destroyPartie/{id}', 'BillLadingController@destroyPartie');

    /* MODULO APLEXCONFIG */
    Route::get('aplexConfig', 'AplexConfigController@index')->name('config.index');
});
Route::get('aplexConfig/config/{key}', 'AplexConfigController@get')->name('config.config');

Route::get('consignee/vueSelect/{term}', 'ConsigneeController@vueSelect');
Route::get('shipper/vueSelect/{term}', 'ShipperController@vueSelect');

Route::get('documento/vueSelectGeneral/{table}/{term}', 'DocumentoController@vueSelectGeneral');
Route::get('documento/vueSelect/{term}', 'DocumentoController@vueSelect');
Route::get('documento/vueSelectSucursales/{term}', 'DocumentoController@vueSelectSucursales');
Route::get('documento/vueSelectTransportadorMaster/{term}', 'DocumentoController@vueSelectTransportadorMaster');
Route::get('documento/vueSelectServicios/{term}', 'DocumentoController@vueSelectServicios');
Route::get('documento/searchDataByNavbar/{data}/{element}', 'DocumentoController@searchDataByNavbar');
Route::get('master/vueSelectConsolidados/{term}', 'MasterController@vueSelectConsolidados');
Route::get('consignee/vueSelectClientes/{term}', 'ConsigneeController@vueSelectClientes');

/* VALIDAR EMAIL DE CLIENTE */
Route::post('clientes/existEmail', 'ClienteController@existEmail');

/* VALIDAR EMAIL DE CONSIGNEE */
Route::post('consignee/existEmail', 'ConsigneeController@existEmail');

/* VALIDAR EMAIL DE SHIPPER */
Route::post('shipper/existEmail', 'ShipperController@existEmail');

/* VALIDAR USERNAME */
Route::get('validarUsername/{element}', 'UserController@validarUsername');

/*--- CASILLERO ---*/
Route::post('casillero/validar/validar_email', 'CasilleroController@validar_email');
Route::get('casillero/vueSelectCiudad/{term}', 'CasilleroController@buscar_ciudad');
Route::post('casillero', 'CasilleroController@store');
Route::get('casillero/{id}', 'CasilleroController@index');

Route::get('obtener_contactos/{id}/{table}', 'ShipperController@getContactos');
Route::post('agregar_contactos/{id}/{table}', 'ShipperController@storeContacto');

/* PREALERTA */
Route::get('prealerta/{id_agencia}', 'PrealertaController@index')->name('prealerta.index');
Route::get('prealerta/{id_agencia}/all', 'PrealertaController@getAll')->name('prealerta.all');
Route::post('prealerta/{id_agencia}', 'PrealertaController@store');
Route::post('prealerta/{id_agencia}/existEmailPost', 'PrealertaController@existEmailPost');
Route::post('prealerta/{id_agencia}/validar_tracking', 'PrealertaController@validar_tracking');

/* RASTREO */
Route::get('rastreo', 'RastreoController@index');
Route::get('rastreo/getStatusReport/{data}', 'RastreoController@getStatusReport');
