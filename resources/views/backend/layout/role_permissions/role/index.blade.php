@extends('backend.app')

@section('title', 'Role')

@section('content')
    <main class="app-content content">
        <div class="row">
            <div class="col-lg-12 mb-1">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title mb-0">All Roles</h2>
                    <a href="{{ route('admin.role.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i> Add Role
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-1">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic_tables" class="table table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>Name</th>
                                        <th style="width: 150px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($roles as $index => $role)
                                        <tr id="role-{{ $role->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>
                                                <a href="{{ route('admin.role.edit', $role->id) }}"
                                                    class="btn btn-sm btn-warning me-1" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                {{-- <button onclick="deleteRole({{ $role->id }})"
                                                    class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fa fa-times"></i>
                                                </button> --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No roles found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('script')
    <script>
        function deleteRole(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: '/role/destroy/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#role-' + id).remove();
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'Something went wrong. Please try again later.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endpush
