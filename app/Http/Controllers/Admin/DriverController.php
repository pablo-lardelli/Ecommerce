<?php

namespace App\Http\Controllers\Admin;

use App\Models\Driver;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin.drivers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('admin.drivers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:1,2',
            'plate_number' => 'required|string',
        ]);

        Driver::create($request->all());
        
        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Conductor registrado!',
            'text' => 'El conductor ha sido regisrado exitosamente',
        ]);
        
        return redirect()->route('admin.drivers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver)
    {
        return view('admin.drivers.show', compact('driver'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Driver $driver)
    {
        $users = User::all();
        return view('admin.drivers.edit', compact('driver', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:1,2',
            'plate_number' => 'required|string',
        ]);

        $driver->update($request->all());
        
        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Conductor actualizado!',
            'text' => 'El conductor ha sido actualizado exitosamente',
        ]);
        
        return redirect()->route('admin.drivers.edit', $driver);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver)
    {
        $driver->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Conductor eliminado!',
            'text' => 'El conductor ha sido eliminado exitosamente',
        ]);
        
        return redirect()->route('admin.drivers.index');
    }
}
