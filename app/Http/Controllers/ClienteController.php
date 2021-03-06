<?php

namespace App\Http\Controllers;

use App\Carrera;
use App\Chofer;
use App\Cliente;
use App\Gasto;
use App\Http\Requests\FormularioClienteRequest;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClienteCarrerasExport;


class ClienteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
            $clientes = Cliente::buscadorCliente($request->get('buscarTexto'))
                ->orderBy('id','desc')
                ->paginate(6);
            $chofer = Chofer::where('baja',0)->get();
            return view('cliente',[
                "clientes"=>$clientes,
                "choferes"=>$chofer
                ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('crearCliente');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FormularioClienteRequest $request)
    {
        $cliente = new Cliente();
        $cliente->nombre = $request->name;
        $cliente->apellido = $request->surname;
        $cliente->telefono = $request->number;
        $cliente->email = $request->email;
        $cliente->save();
        return redirect()->route('clientePrincipal');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        $carreras = Carrera::join('clientes', 'cliente_id', '=', 'clientes.id')
            ->join('chofers', 'chofer_id', '=', 'chofers.id')
            ->select('carreras.*','clientes.nombre as nombre_cliente', 'clientes.apellido as    apellido_cliente', 'chofers.nombre as nombre_chofer', 'chofers.apellido as apellido_chofer', 'chofers.baja as chofer_baja')
            ->buscadorCarreraCliente($request->get('buscarCarrera'))
            ->idCliente($cliente->id)
            ->orderBy('id','desc')
            ->paginate(4);
        //return $carreras;
        return view('mostrarCliente',[
            'carreras' => $carreras,
            'cliente' => $cliente
        ]);
    }

    public function generarPdf($id)
    {
        $cliente = Cliente::findOrFail($id);
        $carreras = Carrera::join('clientes', 'cliente_id', '=', 'clientes.id')
            ->join('chofers', 'chofer_id', '=', 'chofers.id')
            ->select('carreras.*','clientes.nombre as nombre_cliente', 'clientes.apellido as apellido_cliente', 'chofers.nombre as nombre_chofer', 'chofers.apellido as apellido_chofer', 'chofers.baja as chofer_baja')
            ->idCliente($id)
            ->orderBy('id','desc')
            ->get();
        $pdf = PDF::loadView('layouts.clienteCarrerasPdf', compact(['carreras', 'cliente']));
        return $pdf->stream();
    }

    public function export($id)
    {
        return Excel::download(new ClienteCarrerasExport($id), 'cliente-carreras.xlsx');
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
        $cliente = Cliente::findOrFail($id);
        $cliente->nombre = $request->nombre;
        $cliente->apellido = $request->apellido;
        $cliente->telefono = $request->number;
        $cliente->email = $request->email;
        $cliente->save();
        return redirect()->route('clientePrincipal');
        //return $request;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        Carrera::join('clientes', 'cliente_id', '=', 'clientes.id')
            ->join('chofers', 'chofer_id', '=', 'chofers.id')
            ->select('carreras.*')
            ->where('clientes.id',$cliente->id)
            ->delete();
        $cliente->delete();
        return redirect()->route('clientePrincipal');
    }
}
