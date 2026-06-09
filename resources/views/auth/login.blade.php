@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
    <!-- Logo / Brand -->
    <div class="text-center mb-8">
        <div class="inline-block p-4 bg-white rounded-full shadow-md mb-4">
            <div class="w-12 h-12 bg-neutral-900 rounded-full flex items-center justify-center">
                <span class="text-gold font-serif text-xl font-bold">M</span>
            </div>
        </div>
        <h1 class="text-2xl font-serif font-bold text-neutral-900">Mibaraka House</h1>
        <p class="text-sm text-neutral-500 mt-1">Espace d'administration</p>
    </div>
    
    <!-- Session Status -->
    @if(session('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
            {{ session('status') }}
        </div>
    @endif
    
    <!-- Formulaire de connexion -->
    <div class="bg-white rounded-xl shadow-lg border border-neutral-100 p-6 md:p-8">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <!-- Email Address -->
            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-neutral-700 mb-1">
                    Adresse email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-colors @error('email') border-red-500 @enderror"
                       placeholder="admin@mibaraka.com">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Password -->
            <div class="mb-5">
                <label for="password" class="block text-sm font-medium text-neutral-700 mb-1">
                    Mot de passe
                </label>
                <input type="password" id="password" name="password" required
                       class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-colors @error('password') border-red-500 @enderror"
                       placeholder="••••••••">
                @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Remember Me -->
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" 
                           class="w-4 h-4 rounded border-neutral-300 text-gold focus:ring-gold focus:ring-offset-0">
                    <span class="ml-2 text-sm text-neutral-600">Se souvenir de moi</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" 
                       class="text-sm text-neutral-500 hover:text-gold transition-colors">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>
            
            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-neutral-900 text-white py-2.5 rounded-lg font-semibold hover:bg-gold hover:text-black transition-colors duration-300">
                Se connecter
            </button>
        </form>
    </div>
    
    <!-- Footer -->
    <div class="text-center mt-6">
        <p class="text-xs text-neutral-400">
            &copy; {{ date('Y') }} Mibaraka House. Tous droits réservés.
        </p>
    </div>
@endsection