@extends('errors.layout')

@section('code', '429')
@section('title', 'Trop de requêtes')
@section('message', 'Vous avez effectué trop de requêtes.')

@section('icon')
    🚦
@endsection

@section('description')
    <p>Veuillez patienter quelques instants avant de réessayer.</p>
    <p style="margin-top: 10px; font-size: 14px;">Cette limite permet de protéger le système contre les abus.</p>
@endsection