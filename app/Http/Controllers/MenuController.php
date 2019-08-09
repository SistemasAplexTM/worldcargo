<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->assignPermissionsJavascript('menus');
        return view('templates/menus');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data             = (new Menu)->fill($request->all());
            $data->created_at = date('Y-m-d H:i:s');
            if ($data->save()) {
                $this->AddToLog('Menu creado (id :' . $data->id . ')');
                $answer = array(
                    "datos"  => $data,
                    "code"   => 200,
                    "status" => 200,
                );
            } else {
                $answer = array(
                    "error"  => 'Error al intentar Eliminar el registro.',
                    "code"   => 600,
                    "status" => 500,
                );
            }
            return $answer;
        } catch (\Exception $e) {
            $answer = array(
                "error" => $e,
                "code"  => 600,
            );
            return $answer;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Menu  $Menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = Menu::findOrFail($id);
            $data->update($request->all());
            $this->AddToLog('Menu editado (id :' . $data->id . ')');
            $answer = array(
                "datos" => $request->all(),
                "code"  => 200,
            );
            return $answer;

        } catch (\Exception $e) {
            $answer = array(
                "error" => $e,
                "code"  => 600,
            );
            return $answer;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $table = false)
    {
        try {
            if ($table == 'detail') {
                $data = MenuDetalle::findOrFail($id);
                if ($data->delete()) {
                    $this->AddToLog('Menu detalle eliminado (id :' . $data->id . ')');
                    $answer = array(
                        "code" => 200,
                    );
                }
            } else {
                $data = Menu::findOrFail($id);
                if ($data->delete()) {
                    $this->AddToLog('Menu eliminado (id :' . $data->id . ')');
                    $answer = array(
                        "code" => 200,
                    );
                }
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * Restaura registro eliminado
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restaurar($id, $table = false)
    {
        if ($table == 'detail') {
            $data             = MenuDetalle::withTrashed()->findOrFail($id);
            $data->deleted_at = null;
            $data->save();
            $answer = array(
                'code' => 200,
            );
        } else {
            $data             = Menu::withTrashed()->findOrFail($id);
            $data->deleted_at = null;
            $data->save();
            $answer = array(
                'code' => 200,
            );
        }
        return $answer;
    }

    /**
     * Obtener todos los registros de la tabla para el datatable
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll($type)
    {
        $data = Menu::join('clientes as b', 'menu.cliente_id', 'b.id')
            ->join('admin_table AS c', 'menu.tipo_us_id', 'c.id')
            ->select('menu.id', 'menu.name', 'menu.cliente_id', 'b.name AS cliente', 'menu.tipo_us_id', 'c.name AS tipo_uds')
            ->where('c.name', $type)
            ->get();
        return \DataTables::of($data)->make(true);
    }

    public function getDataSelect($tipo_us_id)
    {
        $data = DB::table('menu')
            ->select('id', 'name')
            ->where('tipo_us_id', $tipo_us_id)
            ->get();
        $answer = array(
            'data' => $data,
        );
        return $answer;
    }

    public function getAllDetalle($id_menu)
    {
        $data = MenuDetalle::join('products AS b', 'menu_detalle.product_id', 'b.id')
            ->join('admin_table AS c', 'b.unidad_medida_id', 'c.id')
            ->leftJoin(DB::raw("(SELECT
                                z.id,
                                z.menu_detalle_id,
                                z.grupo_edad_id,
                                z.cantidad
                            FROM
                                pivot_menu_detalle_cantidad AS z
                            WHERE
                                z.grupo_edad_id = 24
                        ) AS d"), 'menu_detalle.id', 'd.menu_detalle_id')
            ->leftJoin(DB::raw("(SELECT
                                z.id,
                                z.menu_detalle_id,
                                z.grupo_edad_id,
                                z.cantidad
                            FROM
                                pivot_menu_detalle_cantidad AS z
                            WHERE
                                z.grupo_edad_id = 25
                        ) AS e"), 'menu_detalle.id', 'e.menu_detalle_id')
            ->select(
                'menu_detalle.id',
                'b.id AS product_id',
                'b.name AS product',
                'c.name AS unidad_medida',
                'c.description AS unidad_medida_ab',
                'd.id AS cantidad_1_3_id',
                'd.cantidad AS cantidad_1_3',
                'e.id AS cantidad_4_5_id',
                'e.cantidad AS cantidad_4_5'
            )
            ->where('menu_detalle.menu_id', $id_menu)
            ->get();
        return \DataTables::of($data)->make(true);
    }

    public function addMenuDetail(Request $request)
    {
        echo '<pre>';
        print_r($request->all());
        echo '</pre>';
        exit();
        // try {
        $id = DB::table('menu_detalle')->insertGetId(
            [
                'menu_id'    => $request->menu_id,
                'product_id' => $request->product_id,
                'age_group_id' => $request->age_group_id,
                'created_at' => date('Y-m-d H:i:s'),
            ]
        );
        if ($id) {
            $this->AddToLog('Menu detalle creado (id :' . $id . ')');
            $answer = array(
                "datos"  => $id,
                "code"   => 200,
                "status" => 200,
            );
        } else {
            $answer = array(
                "error"  => 'Error al intentar Eliminar el registro.',
                "code"   => 600,
                "status" => 500,
            );
        }
        return $answer;
        // } catch (\Exception $e) {
        //     $answer = array(
        //         "error" => $e,
        //         "code"  => 600,
        //     );
        //     return $answer;
        // }
    }

    public function updateDetailMenu(Request $request)
    {
        try {
            DB::table('pivot_menu_detalle_cantidad')
            ->where('id', $request->pk)
            ->update(['cantidad' => $request->value]);
            $this->AddToLog('Menu detalle editado (tbl_detalle_cantidad id ' . $request->pk . ')');
            $answer = array(
                "datos"  => '',
                "code"   => 200,
                "status" => 200,
            );
            return $answer;
        } catch (\Exception $e) {
            $error = '';
            if (isset($e->errorInfo) and $e->errorInfo) {
                foreach ($e->errorInfo as $key => $value) {
                    $error .= $key . ' - ' . $value . ' <br> ';
                }
            } else { $error = $e;}
            $answer = array(
                "error"  => $error,
                "code"   => 600,
                "status" => 500,
            );
            return $answer;
        }
    }
}
