@extends('layouts.dashboard')

@section('title', 'Setting Akun')

@section('content')
    <div class="card">
        <div class="card-header fw-bold">Pengaturan Akun</div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form action="{{ route('account.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nama Depan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="nama_depan" class="form-control"
                                value="{{ old('nama_depan', $user->nama_depan) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Nama Belakang</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="nama_belakang" class="form-control"
                                value="{{ old('nama_belakang', $user->nama_belakang) }}">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>No Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="no_telepon" class="form-control"
                                value="{{ old('no_telepon', $user->no_telepon) }}">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Alamat</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <textarea name="alamat" class="form-control">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Password Baru (opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
