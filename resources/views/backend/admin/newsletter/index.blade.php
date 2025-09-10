@extends('backend.app')

@section('content')
    <div class="app-content content">
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Manage Newsletters</h4>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="newsletterTable" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('backend/assets/datatable/js/datatables.min.js') }}"></script>

    <script>
        $(function() {
            let table = $('#newsletterTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.newsletter.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Delete Newsletter
            $(document).on('click', '.delete', function() {
                let id = $(this).data('id');
                if (confirm("Are you sure you want to delete this newsletter?")) {
                    $.ajax({
                        url: "{{ route('admin.newsletter.delete', ':id') }}".replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            table.ajax.reload();
                            alert(response.success);
                        },
                        error: function(xhr) {
                            alert(xhr.responseJSON?.error || 'Something went wrong!');
                        }
                    });
                }
            });
        });
    </script>
@endpush
