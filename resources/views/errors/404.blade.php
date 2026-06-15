@extends('errors.layout')

@section('code', '404')
@section('title', 'Page non trouvée')
@section('message', 'Oups ! La page que vous cherchez n\'existe pas.')

@section('icon')
    🔍
@endsection

@section('description')
    <p>La page que vous avez demandée est introuvable ou a été déplacée.</p>
    <p style="margin-top: 10px; font-size: 14px;">Vérifiez l'URL ou retournez à l'accueil.</p>
@endsection