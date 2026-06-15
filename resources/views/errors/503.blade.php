@extends('errors.layout')

@section('code', '503')
@section('title', 'Site en maintenance')
@section('message', 'Notre site est temporairement indisponible.')

@section('icon')
    🔧
@endsection

@section('description')
    <p>Nous effectuons actuellement des améliorations sur notre site.</p>
    <p style="margin-top: 10px; font-size: 14px;">Nous serons de retour dans quelques instants.</p>
@endsection