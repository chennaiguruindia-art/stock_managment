@extends('layouts.app')

@section('page-title', 'Logout')
@section('page-subtitle', 'You have signed out successfully.')

@section('content')
    <div class="card-panel">
        <h2 class="section-title">Logout</h2>
        <p class="mb-3">This placeholder logout page confirms the user has signed out. In a full Laravel auth setup, this route would clear the session and redirect to the login screen.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary"><i class="bi bi-speedometer2 me-1"></i> Back to dashboard</a>
    </div>
@endsection
