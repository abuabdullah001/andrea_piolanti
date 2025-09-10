@extends('backend.app')

@section('content')
    <div class="app-content content">
        <div class=" mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Manage Contacts</h4>
            </div>

            {{-- Success message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="contactsTable" class="table table-striped table-bordered w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Country</th>
                                <th>City</th>
                                <th>Postal Code</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Announce</th>
                                <th>Message</th>
                                <th>Action</th>
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
            $('#contactsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.contact.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'country',
                        name: 'country'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'post',
                        name: 'post'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'announce',
                        name: 'announce'
                    },
                    {
                        data: 'message',
                        name: 'message'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>

    <script>
        $(document).on('click', '.delete', function() {
            let id = $(this).data('id');
            if (confirm("Are you sure you want to delete this contact?")) {
                $.ajax({
                    url: "/admin/contact/delete/" + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#contactsTable').DataTable().ajax.reload();
                        alert(response.success);
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.error || 'Something went wrong!');
                    }
                });
            }
        });
    </script>
@endpush
