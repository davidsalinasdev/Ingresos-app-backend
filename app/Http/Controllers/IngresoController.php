<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class IngresoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $ingresos = Ingreso::orderBy('id', 'DESC')->paginate(10); // Todos los servidores publicos

        $today = Carbon::now()->toDateString();

        $count = Ingreso::whereDate('created_at', '=', $today)->count();

        $countCurso = DB::table('ingresos')
            ->whereDate('created_at', '=', now()) // Coincidir con la fecha actual
            ->whereNull('fecha_salida') // Campo fecha_salida sea nulo
            ->count();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'visitas' => $ingresos,
            'cantidad' => $count,
            'cantCurso' => $countCurso
        );
        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Recoger los usuarios por post
        $params = (object) $request->all(); // Devulve un obejto
        $paramsArray = $request->all(); // Devulve un Array

        // 2.-Validar datos
        $validate = Validator::make($request->all(), [
            'carnet' => 'required',
            'nombres' => 'required',
            'lugar' => 'required',
            'users_id' => 'required',
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos',
                'visitas' => $request->all(),
                'errors' => $validate->errors()
            );
        } else {
            // Crear el objeto usuario para guardar en la base de datos
            $ingresos = new Ingreso();
            $ingresos->carnet = $params->carnet;
            $ingresos->nombres = $params->nombres;
            $ingresos->lugar = $params->lugar;
            $ingresos->users_id = $params->users_id;

            try {
                // Guardar en la base de datos
                $ingresos->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La visita se registro correctamente',
                    'visitas' => $ingresos
                );
            } catch (\Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'No se pudo registrar, intente nuevamente',
                    'error' => $e->getMessage()
                );
            }
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ingreso = Ingreso::find($id);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($ingreso)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'visitas' => $ingreso
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La visita no existe'
            );
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // Validar que la visita exista
        $ingreso = Ingreso::find($id);

        if (!empty($ingreso)) {

            // 1.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($request->all(), [
                'carnet' => 'required',
                'nombres' => 'required',
                'lugar' => 'required',
                'users_id' => 'required'
            ]);

            // 2.-Recoger los usuarios por post
            $params = (object) $request->all(); // Devuelve un obejto
            $paramsArray = $request->all(); // Es un array

            // // Comprobar si los datos son validos
            if ($validate->fails()) { // en caso si los datos fallan la validacion
                // La validacion ha fallado
                $data = array(
                    'status' => 'Error',
                    'code' => 400,
                    'message' => 'Datos incorrectos no se puede actualizar',
                    'errors' => $validate->errors()
                );
            } else {

                // 4.- Quitar los campos que no quiero actualizar de la peticion.
                unset($paramsArray['created_at']);

                try {

                    Ingreso::where('id', $id)->update($paramsArray);

                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Los datos se modificaron correctamente',
                        'visitas' => $ingreso,
                        'changes' => $paramsArray
                    );
                } catch (\Exception $e) {
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'No se hizo la modificación',
                        'error' => $e->getMessage()
                    );
                }
            }
            return response()->json($data, $data['code']);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este visitante no existe',
                // 'error' => $e
            );
            return response()->json($data, $data['code']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // 5.- Actualizar los datos en la base de datos.
            Ingreso::where('id', $id)->delete();
            // 6.- Devolver el array con el resultado.
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'El visitante se elimino correctamente'
            );
        } catch (\Exception $e) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'No se pudo eliminar intente nuevamente',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }

    // Buscar Visitas
    public function buscarVisitas(Request $request)
    {
        $params = (object) $request->all(); // Devuelve un objeto
        $texto = trim($params->visitante);

        try {
            $ingreso = Ingreso::where(function ($query) use ($texto) {
                $query->where('carnet', 'iLIKE', "%{$texto}%")
                    ->orWhere('nombres', 'iLIKE', "%{$texto}%")
                    ->orWhere('lugar', 'iLIKE', "%{$texto}%");
                // ->orWhere('estado', 'ilike', "%{$texto}%");
            })
                ->orderBy('id', 'DESC')
                ->paginate(10);

            $data = [
                'status' => 'success',
                'code' => 200,
                'visitas' => $ingreso,
                'texto' => $texto
            ];
        } catch (Exception $e) {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'No se pudo buscar',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function fechaSalida(Request $request, $id)
    {

        // Validar que la visita exista
        $ingreso = Ingreso::find($id);

        if (!empty($ingreso)) {

            // 2.-Recoger los usuarios por post
            $params = (object) $request->all(); // Devuelve un obejto
            $paramsArray = $request->all(); // Es un array

            $today = Carbon::now()->toDateTimeLocalString();
            $paramsArray['fecha_salida'] = $today;

            try {

                Ingreso::where('id', $id)->update($paramsArray);

                // 6.- Devolver el array con el resultado.
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Se registro la salida correctamente',
                    'visitas' => $ingreso,
                    'changes' => $paramsArray
                );
            } catch (\Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'No se hizo la modificación',
                    'error' => $e->getMessage()
                );
            }

            return response()->json($data, $data['code']);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este visitante no existe',
                // 'error' => $e
            );
            return response()->json($data, $data['code']);
        }
    }

    public function historial($id)
    {
        $ingreso = Ingreso::find($id);
        $historial = Ingreso::where('carnet', $ingreso->carnet)
            ->with('user')
            ->orderBy('id', 'DESC')
            ->get();
        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($ingreso)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'historial' => $historial
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El historial no existe'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function historialReal($carnet)
    {


        $historialreal = Ingreso::where('carnet', $carnet)
            ->with('user')
            ->orderBy('id', 'DESC')
            ->take(3)
            ->get();
        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($historialreal)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'historialreal' => $historialreal
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'No tiene visitas registrada'
            );
        }
        return response()->json($data, $data['code']);
    }
}
