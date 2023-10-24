<?php

namespace App\Http\Controllers;

use App\Models\Fotografia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FotografiaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($carnet)
    {
        $fotografia = Fotografia::find($carnet);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($fotografia)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'fotografia' => $fotografia
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'Fotografia no existe'
            );
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // echo 'Hola Mundo';
        // die();
        // 1.-Recoger los usuarios por post
        $params = (object) $request->all(); // Devulve un obejto
        $paramsArray = $request->all(); // Devulve un Array

        $archivo = $request->file('imagen'); //Segun angular

        // 2.-Validar datos
        $validate = Validator::make($paramsArray, [
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación de la imagen
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos.',
                'socio' => $request->all(),
                'errors' => $validate->errors()
            );
        } else {

            try {
                // Para crear nombre de archivos
                if ($archivo != null) {

                    $fileName = time() . $archivo->getClientOriginalName(); // Obtiene el nombre original del archivo
                    Storage::disk('public')->put($fileName, File::get($archivo)); // Guarda la archivo en el disco laravel

                    $photo = new Fotografia;
                    $photo->carnet = $params->carnet;
                    $photo->imagen = $fileName; //Direccion
                    $photo->save();
                } else {
                    echo 'No Guardo!';
                }
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La imagen se guardo correctamente.',

                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'No se pudo crear la imagem.',
                    'error' => $e
                );
            }
        }
        return response()->json($data, $data['code']);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
