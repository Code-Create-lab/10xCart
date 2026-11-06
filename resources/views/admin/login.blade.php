@extends('layouts.admin')

@section('title', 'Sign in')

@section('content')
<form class="card login" method="POST" action="{{ route('admin.login.store') }}">
    @csrf
    <h1>{{ config('site.name') }} admin</h1>

    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus/>
    @error('email')<div class="error" role="alert">{{ $message }}</div>@enderror

    <label for="password">Password</label>
    <input id="password" name="password" type="password" autocomplete="current-password" required/>

    <label class="remember"><input name="remember" type="checkbox" value="1"/> Keep me signed in</label>

    <button class="btn" type="submit">Sign in</button>
</form>
@endsection
