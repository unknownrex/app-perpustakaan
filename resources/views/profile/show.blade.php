{{-- File: resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Saya')
@section('content')
        <h1>Profil Saya</h1>

        <table>
            <tbody>
                <tr>
                    <th>Nama</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>{{ ucfirst($user->role) }}</td>
                </tr>
            </tbody>
        </table>

        <h2>Ganti Password</h2>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <label for="current_password">Password Lama</label>
            <input type="password" name="current_password" id="current_password">
            @error('current_password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">Password Baru</label>
            <input type="password" name="password" id="password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation">

            <button type="submit" class="btn">Simpan Password Baru</button>
        </form>
@endsection
