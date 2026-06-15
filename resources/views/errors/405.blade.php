@extends('errors.layout')

@section('code', '405')
@section('title', 'Méthode non autorisée')
@section('message', 'La méthode utilisée n\'est pas autorisée.')

@section('icon')
    🚫
@endsection

@section('description')
    <p>La méthode de requête utilisée n'est pas autorisée pour cette page.</p>
    <p style="margin-top: 10px; font-size: 14px;">Veuillez vérifier votre action et réessayer.</p>
@endsection