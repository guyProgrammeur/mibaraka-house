@extends('errors.layout')

@section('code', '403')
@section('title', 'Accès interdit')
@section('message', 'Vous n\'avez pas les autorisations nécessaires.')

@section('icon')
    🔒
@endsection

@section('description')
    <p>Vous n'êtes pas autorisé à accéder à cette page.</p>
    <p style="margin-top: 10px; font-size: 14px;">Si vous pensez qu'il s'agit d'une erreur, contactez votre administrateur.</p>
@endsection