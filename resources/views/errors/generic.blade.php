@extends('errors.layout')

@section('code', 'Erreur')
@section('title', 'Une erreur est survenue')
@section('message', $message ?? 'Une erreur inattendue s\'est produite.')

@section('icon')
    ❌
@endsection

@section('description')
    <p>{{ $description ?? 'Veuillez réessayer plus tard ou contacter le support si le problème persiste.' }}</p>
    @if(config('app.debug') && isset($errorDetails))
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: left; font-size: 12px; overflow-x: auto;">
            <strong style="color: #dc2626;">Détails techniques (mode debug) :</strong>
            <pre style="margin-top: 10px; color: #6b7280;">{{ $errorDetails }}</pre>
        </div>
    @endif
@endsection