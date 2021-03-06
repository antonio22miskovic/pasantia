<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index()
    {
      $user =  User::where('rol_id',2)->get();
        $user->toJson();
      return $user;
    }


    public function store(Request $request)
    {
        //obtenemos los datos del usuario logeado
        $user = Auth::user();

        // que pasa si es administrador
        //significa que solo pude registrar a los encargados
        if ($user->rol_id === 1) {

                $avatar = $request['avatar'];
                $usuario = $request['usuario'];
                $rol = 2;

        }else{

             //si no es el admin es un usuario encargado registrando a un usuario por una cita
            $avatar = null;
            $usuario = null;
            $rol = 3;

        }

     $usuario =  User::create([

        'name' => $request['name'] ,
        'apellido' => $request['apellido'] ,
        'usuario' => $usuario ,
        'ci' => $request['ci'] ,
        'avatar' => $avatar,
        'email' => $request['email'] ,
        'password' => bcrypt($request['password']),
        'rol_id' => $rol,

        ]);

     if ($user->rol_id === 1) {

         return response()->json(['mensaje' => 'se ah registrado con exito'],201);

     }

        return response()->json($usuario,201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (is_null($user)) {

          return  response()->json(['mensaje'=>'el usuario no se encuentra registrado'],404);

        }

        $user->rol;
        $user->solicitudes;

       return response()->json($user,200);
    }


    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (is_null($user)) {

          return  response()->json(['mensaje'=>'el usuario no se encuentra registrado'],404);

        }else{

             $validaruser = User::where( 'usuario' , $request['usuario'] )->first();

            if (is_null($validaruser)) {

            }else{

                 if ($validaruser->usuario === $user->usuario){


                 }else{

                    return response()->json(['mensaje' => 'usuario no disponible','e' => 2]);

                 }

              }


              $validarci = User::where( 'ci' , $request['ci'] )->first();

            if (is_null($validarci)) {

            }else{

                 if ($validarci->ci === $user->ci){


                 }else{

                    return response()->json(['mensaje' => 'cedula no disponible','e' => 2]);

                 }

              }


               $validaremail = User::where( 'email' , $request['email'] )->first();

            if (is_null($validaremail)) {

            }else{

                 if ($validaremail->email === $user->email){


                 }else{

                    return response()->json(['mensaje' => 'email no disponible','e' => 2]);

                 }

              }



            // se introcude el mismo avatar y no se cambia la contraseña
            if ($request['avatar'] === $user->avatar && $request['password'] === 0 ) {

                $user->update([

                    'name' => $request['name'],
                    'apellido' => $request['apellido'],
                    'usuario' => $request['usuario'],
                    'ci' => $request['ci'],
                    'avatar' => $request['avatar'],
                    'email' => $request['email'],
                    // 'password' => bcrypt($request['password']),
                    'rol_id' => 2,

                ]);

                    return response()->json(['mensaje'=>'se ah actualizado con exito', 'e' => 1]);

            }else{

                //ahora se cambio la contraseña pero el avatar es el mismo
                if ($request['avatar'] === $user->avatar) {

                    $user->update([

                        'name' => $request['name'],
                        'apellido' => $request['apellido'],
                        'usuario' => $request['usuario'],
                        'ci' => $request['ci'],
                        'avatar' => $request['avatar'],
                        'email' => $request['email'],
                        'password' => bcrypt($request['password']),
                        'rol_id' => 2,

                    ]);

                    return response()->json(['mensaje'=>'se ah actualizado con exito', 'e' => 1]);

                }else{

                    //
                    if ($request['avatar'] != $user->avatar && $request['password'] != 0 ) {

                     $exploded = explode(',', $request->avatar);
                     $decoded =base64_decode($exploded[1]);

                        if (str_contains($exploded[0], 'jpeg')) {

                                  $extension = 'jpg';

                        }else{

                                 $extension = 'png';

                        }

                        $filename = str_random().'.'.$extension;

                        $path = public_path().'/img/'.$filename;

                        file_put_contents($path, $decoded);

                        $user->update([

                            'name' => $request['name'],
                            'apellido' => $request['apellido'],
                            'usuario' => $request['usuario'],
                            'ci' => $request['ci'],
                            'avatar' => $filename,
                            'email' => $request['email'],
                            'password' => bcrypt($request['password']),
                            'rol_id' => 2,

                        ]);
                        return response()->json(['mensaje'=>'se ah actualizado con exito', 'e' => 1]);

            }else{

                     $exploded = explode(',', $request->avatar);
                     $decoded =base64_decode($exploded[1]);

                        if (str_contains($exploded[0], 'jpeg')) {

                                  $extension = 'jpg';

                        }else{

                                 $extension = 'png';

                        }

                        $filename = str_random().'.'.$extension;

                        $path = public_path().'/img/'.$filename;

                        file_put_contents($path, $decoded);

                        $user->update([

                            'name' => $request['name'],
                            'apellido' => $request['apellido'],
                            'usuario' => $request['usuario'],
                            'ci' => $request['ci'],
                            'avatar' => $filename,
                            'email' => $request['email'],
                            // 'password' => bcrypt($request['password']),
                            'rol_id' => 2,

                        ]);
                        return response()->json(['mensaje'=>'se ah actualizado con exito', 'e' => 1]);

                    }
                }
            }
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
         $user = User::find($id);

        if (is_null($user)) {

            return response()->json(['mensaje'=> 'usuario no existe'],404);
        }

        $user->delete();

        return response()->json(['mensaje'=>'usuario eliminado con exito'],200);
    }
}
