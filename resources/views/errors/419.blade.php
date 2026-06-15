@extends('errors.layout')

@section('code', '419')
@section('title', 'Session expirée')
@section('message', 'Votre session a expiré.')

@section('icon')
    ⏰
@endsection

@section('description')
    <p>Votre session a expiré en raison d'une inactivité prolongée.</p>
    <p style="margin-top: 10px; font-size: 14px;">Veuillez rafraîchir la page et réessayer.</p>
@endsection

@section('actions')
    <a href="javascript:location.reload()" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Rafraîchir la page
    </a>
@endsection