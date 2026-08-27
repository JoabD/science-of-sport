<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Science of Sport - Eventos')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/css/public.css', 'resources/js/public.js'])
</head>
@php
    // Which modal (if any) should pop back open after a redirect: failed
    // login/register, saved profile, event validation errors, etc.
    $openModal = old('form', session('open_modal'));
@endphp
{{-- public.js is an external file, it cant read blade vars directly, so
     whatever it needs to know about the current user gets passed down
     through these data attributes instead --}}
<body
    data-logged-in="{{ auth()->check() ? '1' : '0' }}"
    data-is-admin="{{ auth()->check() && auth()->user()->can('create', \App\Models\Post::class) ? '1' : '0' }}"
    data-open-modal="{{ $openModal }}"
>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand text-brand-blue fw-bold" href="{{ route('home') }}">
            Science of Sport
        </a>

        <div class="d-flex align-items-center">
            @guest
                <button type="button" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                    Log In
                </button>
            @else
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
{{--                        <li>--}}
{{--                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">--}}
{{--                                My Account--}}
{{--                            </button>--}}
{{--                        </li>--}}
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Log Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </div>
</nav>

<main>
    @yield('content')
</main>

<footer class="bg-dark text-white text-center py-4 mt-5">
    <div class="container">
        <p class="mb-0">Science of Sport is a 501(c)(3) organization.</p>
    </div>
</footer>

@guest
    @include('partials.auth-modals')
@else
    @include('partials.profile-modal', ['user' => auth()->user()])
@endguest

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
