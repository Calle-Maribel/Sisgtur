<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//Agremamos
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    //agregando una funcion constructor
    function __construct()
    {
        $this->middleware('permission:ver-rol | crear-rol | editar-rol | borrar-rol',['only'=>['index']]);
        $this->middleware('permission:crear-rol' ,['only'=>['create','store']]);
        $this->middleware('permission:editar-rol' ,['only'=>['edit','update']]);
        $this->middleware('permission:crear-rol' ,['only'=>['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $roles = Role::paginate(5);
        return view('roles.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // haviemdo uso de la clase permission del paquete de spatie
        $permission = Permission::get();
        return view('roles.crear', compact('permission'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //guardar datos, datos a validar
        $this->validate($request, ['name' => 'required','permission' => 'required']);
        $role = Role::create(['name' => $request->input('name')]);
        //sincronizando
        $role->syncPermissions($request->input('permission'));
        
        return redirect()->route('roles.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermission = DB::table('role_has_permissions')->where('role_has_permissions.role_id',$id)
        //metodo pluck recupera todos los valores de una clave determinada 
        ->pluck('role_has_permissions.role_id','role_has_permissions.role_id')
        ->all();

        return view('roles.editar',compact('role','permission','rolePermission'));
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
          //guardar datos, datos a validar
        $this->validate($request, ['name' => 'required','permission' => 'required']);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));
        return redirect()->route('roles.index');
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
        DB::table('roles')->where('id',$id)->delete();
        return redirect()->route('roles.index');
    }
}
