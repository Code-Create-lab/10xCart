<!DOCTYPE html>
<html lang="en-IN">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta name="robots" content="noindex, nofollow"/>
<title>@yield('title') · {{ config('site.name') }} admin</title>
<link href="{{ asset('favicon.ico') }}" rel="icon" sizes="48x48"/>
<link href="{{ \App\Support\Assets::versioned('assets/css/admin.css') }}" rel="stylesheet"/>
</head>
<body>
@auth
<header class="topbar">
    <a class="brand" href="{{ route('admin.dashboard') }}">{{ config('site.name') }} admin</a>
    <nav aria-label="Admin">
        <a href="{{ route('admin.dashboard') }}" @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>Dashboard</a>
        <a href="{{ route('admin.visitors.index') }}" @if (request()->routeIs('admin.visitors.*')) aria-current="page" @endif>Visitors</a>
        <a href="{{ route('admin.leads.index') }}" @if (request()->routeIs('admin.leads.*')) aria-current="page" @endif>Leads</a>
        <a href="{{ route('home') }}" target="_blank" rel="noopener">View site ↗</a>
    </nav>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Sign out</button>
    </form>
</header>
@endauth

<main>
@yield('content')
</main>
</body>
</html>
