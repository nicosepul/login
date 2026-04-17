@extends('layouts.app')

@section('content')
<div class="container">
    <perfil-mascota-component mascota-id="{{ $mascotaId }}"></perfil-mascota-component>
</div>
@endsection
