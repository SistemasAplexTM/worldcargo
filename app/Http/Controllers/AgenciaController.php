<?php

namespace App\Http\Controllers;

use App\Agencia;
use App\AgenciaDetalle;
use App\Http\Requests\AgenciaRequest;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Redirect;

class AgenciaController extends Controller
{
    public function __construct(){
        $this->middleware('permission:agencia.index')->only('index');
        $this->middleware('permission:agencia.create')->only('store', 'create');
        $this->middleware('permission:agencia.edit')->only('edit');
        $this->middleware('permission:agencia.destroy')->only('destroy');
        $this->middleware('permission:agencia.delete')->only('delete');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->assignPermissionsJavascript('agencia');
        return view('templates/agenciaIndex');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->assignPermissionsJavascript('agencia');
        return view('templates/agenciaForm');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AgenciaRequest $request)
    {

        $data = (new Agencia)->fill($request->all());
        if ($data->usar_zopim) {
            $data->usar_zopim = 1;
        } else { $data->usar_zopim = 0;}
        if ($data->usar_mail_chimp) {
            $data->usar_mail_chimp = 1;
        } else { $data->usar_mail_chimp = 0;}
        if ($data->usar_paypal) {
            $data->usar_paypal = 1;
        } else { $data->usar_paypal = 0;}

        //obtenemos el campo file definido en el formulario
        if ($request->file('logo')) {
            //obtenemos el nombre del archivo
            $data->logo = trim($request->file('logo')->getClientOriginalName());
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \Storage::disk('public')->put('storage/'.$data->logo, \File::get($request->file('logo'))); //se guardara en 'public/storage'
        }
        /* GUARDO LA CABECERA */
        $data->save();

        /* REGISTRAR EL DETALLE */
        $agencia_id = $data->id;

        for ($i = 0; $i < count($request->input('servicios_id')); $i++) {
            $detalle                   = new AgenciaDetalle;
            $detalle->agencia_id       = $agencia_id;
            $detalle->servicios_id     = $request->input('servicios_id')[$i];
            $detalle->tarifa_principal = $request->input('tarifa_principal')[$i];
            $detalle->tarifa_agencia   = $request->input('tarifa_agencia')[$i];
            $detalle->seguro           = $request->input('seguro')[$i];
            $detalle->save();
        }
        return redirect()->route('agencia.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $agencia = Agencia::join('localizacion', 'agencia.localizacion_id', '=', 'localizacion.id')
            ->select('agencia.*', 'localizacion.nombre as ciudad', 'localizacion.id as ciudad_id')
            ->where([['agencia.id', '=', $id], ['agencia.deleted_at', '=', null]])
            ->first();

        $detalle = AgenciaDetalle::join('servicios', 'agencia_detalle.servicios_id', 'servicios.id')
        ->join('maestra_multiple', 'maestra_multiple.id', 'servicios.tipo_embarque_id')
            ->select('agencia_detalle.*','maestra_multiple.nombre as tipo_embarque', 'servicios.nombre as servicio', 'servicios.id as servicio_id', 'servicios.peso_minimo as tarifa_minima')
            ->where([['agencia_detalle.agencia_id', '=', $id], ['agencia_detalle.deleted_at', '=', null]])
            ->get();

        return view('templates/agenciaForm', compact('agencia', 'detalle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AgenciaRequest $request, $id)
    {
        $data = Agencia::findOrFail($id);
        /* LE ASIGNO UNA VARIABLE AL REQUEST PARA PODER ACTUALIZAR LOS CAMPOS BOLEANOS*/
        $requestData = $request->all();

        if ($request->usar_zopim) {
            $requestData['usar_zopim'] = 1;
        } else { $requestData['usar_zopim'] = 0;}
        if ($request->usar_mail_chimp) {
            $requestData['usar_mail_chimp'] = 1;
        } else { $requestData['usar_mail_chimp'] = 0;}
        if ($request->usar_paypal) {
            $requestData['usar_paypal'] = 1;
        } else { $requestData['usar_paypal'] = 0;}

        //obtenemos el campo file definido en el formulario
        if ($request->file('logo')) {
            //obtenemos el nombre del archivo
            $requestData['logo'] = trim($request->file('logo')->getClientOriginalName());
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \Storage::disk('public')->put('storage/'.$requestData['logo'], \File::get($request->file('logo'))); //se guardara en 'public/storage'
        }

        $data->update($requestData);

        /* REGISTRAR EL DETALLE */
        $agencia_id = $data->id;
        // return $request->all();
        if($request->input('servicios_id')){
            for ($i = 0; $i < count($request->input('servicios_id')); $i++) {
                $detalle                   = new AgenciaDetalle;
                $detalle->agencia_id       = $agencia_id;
                $detalle->servicios_id     = $request->input('servicios_id')[$i];
                $detalle->tarifa_principal = $request->input('tarifa_principal')[$i];
                $detalle->tarifa_agencia   = $request->input('tarifa_agencia')[$i];
                $detalle->seguro           = $request->input('seguro')[$i];
                $detalle->save();
            }
        }

        return redirect()->route('agencia.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Agencia::findOrFail($id);
        $data->delete();
    }

    /**
     * Actualiza el campo deleted_at del registro seleccionado.
     *
     * @param  int  $id
     * @param  boolean  $deleteLogical
     * @return \Illuminate\Http\Response
     */
    public function delete($id, $logical, $table = null)
    {

        if (isset($logical) and $logical == 'true') {
            if ($table) {
                $data = AgenciaDetalle::findOrFail($id);
            } else {
                $data = Agencia::findOrFail($id);
            }
            $now              = new \DateTime();
            $data->deleted_at = $now->format('Y-m-d H:i:s');
            if ($data->save()) {
                $answer = array(
                    "datos" => 'Eliminación exitosa.',
                    "code"  => 200,
                );
            } else {
                $answer = array(
                    "error" => 'Error al intentar Eliminar el registro.',
                    "code"  => 600,
                );
            }

            return $answer;
        } else {
            $this->destroy($id);
        }
    }

    /**
     * Restaura registro eliminado
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restaurar($id)
    {
        $data             = Agencia::findOrFail($id);
        $data->deleted_at = null;
        $data->save();
    }

    /**
     * Obtener todos los registros de la tabla para el datatable
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $sql = DB::table('agencia')
            ->join('localizacion', 'agencia.localizacion_id', '=', 'localizacion.id')
            ->join('deptos', 'localizacion.deptos_id', '=', 'deptos.id')
            ->join('pais', 'deptos.pais_id', '=', 'pais.id')
            ->select('agencia.*', 'localizacion.nombre as ciudad', 'localizacion.id as ciudad_id', 'deptos.descripcion as estado', 'deptos.id as estado_id', 'pais.descripcion as pais', 'pais.id as pais_id')
            ->where([['agencia.deleted_at', '=', null]])
            ->orderBy('agencia.descripcion');
        return \DataTables::of($sql)->make(true);
    }

    public function selectInput(Request $request, $tableName)
    {
        $term = $request->term ?: '';

        if ($tableName === 'localizacion') {
            $tags = DB::table($tableName)
                ->join('deptos', 'localizacion.deptos_id', '=', 'deptos.id')
                ->join('pais', 'deptos.pais_id', '=', 'pais.id')
                ->select(['localizacion.id', 'localizacion.nombre as text', 'deptos.descripcion as deptos', 'deptos.id as deptos_id', 'pais.descripcion as pais', 'pais.id as pais_id'])
                ->where([
                    ['localizacion.nombre', 'like', $term . '%'],
                    ['localizacion.deleted_at', '=', null],
                ])->get();
        } else {
            if ($tableName === 'servicios') {
                $tags = DB::table('servicios as a')
                    ->join('maestra_multiple as b', 'a.tipo_embarque_id', 'b.id')
                    ->select('a.id', 'a.nombre as text', 'a.tarifa', 'a.seguro', 'a.peso_minimo', 'b.nombre as tipo_embarque')
                    ->where([
                        ['a.deleted_at', null],
                    ])
                    ->orderBy('b.nombre')
                    ->get();
            } else {
                $tags = DB::table($tableName)->select(['id', 'nombre as text', 'tarifa', 'seguro'])->where([
                    ['nombre', 'like', $term . '%'],
                    [$tableName . '.deleted_at', '=', null],
                ])->get();
            }

        }

        $answer = array(
            'code'  => 200,
            'items' => $tags,
        );
        return \Response::json($answer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDetail(Request $request, $id)
    {
        try {
            $data = AgenciaDetalle::findOrFail($id);
            $data->update($request->all());
            $answer = array(
                "datos"  => $request->all(),
                "code"   => 200,
                "status" => 500,
            );
            return $answer;

        } catch (\Exception $e) {
            $error = '';
            foreach ($e->errorInfo as $key => $value) {
                $error .= $key . ' - ' . $value . ' <br> ';
            }
            $answer = array(
                "error"  => $error,
                "code"   => 600,
                "status" => 500,
            );
            return $answer;
        }
    }

     public function getAllUrls($id_agencia)
    {
        return \DataTables::of(DB::table('agencia_urls_publicas')->where([['deleted_at', NULL], ['agencia_id', $id_agencia]])->get())->make(true);
    }
}
