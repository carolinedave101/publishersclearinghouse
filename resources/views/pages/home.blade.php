@extends('layouts.app')

@section('content')
    @include('components.nav')
    <main class="flex-1">
        @include('components.hero-section')
        @include('components.code-entry-form')
        @include('components.recent-winners')
        @include('components.stats-bar')
    </main>
    @include('components.footer')
@endsection
