@extends('layouts.layout')
@section('contenido')
<div class="container px-5 d-flex flex-column">
    <form class="form-inline my-4 d-none d-md-block">
        <input class="form-control mr-sm-2 col-10 col-md-4" name="buscarTexto" type="search" placeholder="Ingrese un nombre o apellido" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Buscar</button>
    </form>

    <form class="form-inline search d-block d-md-none d-flex my-4">
        <input class="form-control form-control-sm w-75 search-i" type="text" name="buscarCarrera" placeholder="Ingrese un nombre o apellido"
        aria-label="Search">
        <i class="fas fa-search ml-3" aria-hidden="true"></i>
    </form>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido</th>
                    <th scope="col">Telefono</th>
                    <th scope="col">Email</th>
                    <th scope="col" colspan="2">Funciones</th>
                </tr>
            </thead>
            <tbody class="tabCarrera">
                @forelse ($clientes as $clienteItem)
                <tr>
                    <td>{{ $clienteItem->nombre}}</td>
                    <td>{{ $clienteItem->apellido}}</td>
                    <td>{{ $clienteItem->telefono}}</td>
                    <td>{{ $clienteItem->email}}</td>
                    <td>
                        <a href="#NuevaCarrera" class="btnNuevaCarrera btn btn-primary" data-toggle="modal">Nueva Carrera</a>
                        @include('partials/crearCarrera')
                    </td>
                    <td>
                        <a href="{{route('mostrarCliente', $clienteItem)}}" class="btn btn-primary">Ver más</a>
                    </td>
                </tr>
                @empty
                <h4>No hay Registros que mostrar</h4>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="paginate d-flex justify-content-center"> {{$clientes->links()}}</div>
</div>
@endsection


