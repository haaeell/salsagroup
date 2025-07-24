@extends('layouts.homepage')

@section('content')
    <div class="container vh-100 ">
        <div class="row justify-content-center align-items-center" style="height: 80vh">
            <div class="col-md-6">
                <div class="card shadow border-0 rounded-4 p-3">
                    <h4 class="fw-semibold text-center mb-4">Masuk ke Akun Anda</h4>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email" required
                                        autofocus>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </button>
                                <a href="{{ route('register') }}" class="btn btn-outline-danger mt-2">
                                    <i class="bi bi-person-plus"></i> Daftar Akun Baru
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
