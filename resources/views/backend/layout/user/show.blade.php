@extends('backend.app')
@section('title', 'General Setting')
@section('content')
    <div class="app-content content ">
        <div class="container mt-5">
            <!-- User Information Section -->
            <div class="row mb-2">
                <div class="col-md-5 m-auto text-center">
                    <div class="card card-body mb-2 row">
                        <div class="col-lg-10 m-auto">
                            <div class="avatar m-auto mb-3">
                                <img src="{{ asset($user->avatar) ?? '' }}" alt="User Avatar" class="img-fluid rounded-circle"
                                    width="100">
                            </div>
                            <!-- User Details -->
                            <h3>{{ $user->name ?? 'No Name' }}</h3>
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>@</td>
                                        <td class="text-start">Username</td>
                                        <td>:</td>
                                        <td>{{ $user->username ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>👤</td>
                                        <td class="text-start">Role</td>
                                        <td>:</td>
                                        <td class="text-capitalize">{{ $user->getRoleNames()->first() ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>📧</td>
                                        <td class="text-start">Email</td>
                                        <td>:</td>
                                        <td>{{ $user->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>📞</td>
                                        <td class="text-start">Phone</td>
                                        <td>:</td>
                                        <td>{{ $user->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @if ($user->status == 'active')
                                                <i class="fa-solid fa-circle-check text-success"></i>
                                            @else
                                                <i class="fa-solid fa-circle-xmark text-danger"></i>
                                            @endif
                                        </td>
                                        <td class="text-start">Status</td>
                                        <td>:</td>
                                        <td>
                                            @if ($user->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 m-auto text-center">
                    <div class="card card-body mb-2">
                        <h3>About Me</h3>
                        <p>{{ $user->about ?? 'No information available' }}</p>
                    </div>
                </div>
                <div class="col-md-5 m-auto text-center">
                    <div class="card card-body mb-2">
                        <h3>Description</h3>
                        <p>{{ $user->description ?? 'No information available' }}</p>
                    </div>
                </div>
                <div class="col-md-5 m-auto text-center">
                    <div class="card card-body mb-2">
                        <h3>Address</h3>
                        <p>{{ $user->address ?? 'No information available' }}</p>
                    </div>
                </div>
                <div class="col-md-5 m-auto text-center">
                    <div class="card card-body mb-2">
                        <h3>User Created At</h3>
                        <p>{{ $user->created_at->format('d M Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
