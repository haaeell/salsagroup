@extends('layouts.homepage')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-body px-5 py-4">
                        <h4 class="fw-bold text-center mb-4">Pendaftaran Akun Baru</h4>
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="row g-3">
                                @php
                                    $fields = [
                                        ['nama_depan', 'Nama Depan', 'person'],
                                        ['nama_belakang', 'Nama Belakang', 'person'],
                                        ['username', 'Username', 'person-circle'],
                                        ['email', 'Email', 'envelope'],
                                        ['no_telepon', 'No Telepon', 'telephone'],
                                        ['alamat', 'Alamat', 'geo-alt'],
                                        ['nik', 'NIK', 'card-list'],
                                    ];
                                @endphp

                                @foreach ($fields as [$name, $label, $icon])
                                    <div class="col-md-6">
                                        <label for="{{ $name }}" class="form-label">{{ $label }} <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-{{ $icon }}"></i></span>
                                            <input type="text" id="{{ $name }}" name="{{ $name }}"
                                                class="form-control @error($name) is-invalid @enderror"
                                                value="{{ old($name) }}" required>
                                            @error($name)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" id="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="password-confirm" class="form-label">Konfirmasi Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" id="password-confirm" name="password_confirmation"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-danger px-4 py-2 w-100">Daftar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
