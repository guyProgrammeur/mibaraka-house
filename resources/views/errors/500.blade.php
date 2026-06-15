@extends('errors.layout')

@section('code', '500')
@section('title', 'Erreur serveur')
@section('message', 'Une erreur inattendue s\'est produite.')

@section('icon')
    ⚠️
@endsection

@section('description')
    <p>Nous rencontrons actuellement des problèmes techniques.</p>
    <p style="margin-top: 10px; font-size: 14px;">Nos équipes ont été notifiées et travaillent à résoudre le problème.</p>
@endsection