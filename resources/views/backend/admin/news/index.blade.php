@extends('backend.app')

@section('content')
    <div class="app-content content">
        <div class=" mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Manage News</h4>
                <a href="{{ route('admin.news.create') }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> Create News
                </a>
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
                    <table id="newsTable" class="table table-striped" style="width: 100%!important">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User Name</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th width="120">Action</th>
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
            let table = $('#newsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.news.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_id',
                        name: 'user_id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function(data) {
                            return data.length > 50 ? data.substr(0, 50) + '...' : data;
                        }
                    },
                    {
                        data: 'image',
                        name: 'image',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let color = data === 'active' ? 'success' : 'secondary';
                            return `<span class="badge bg-${color} status-toggle" data-id="${row.id}" style="cursor:pointer;">
                            ${data.charAt(0).toUpperCase() + data.slice(1)}
                        </span>`;
                        }
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Delete button click event
            $(document).on('click', '.delete', function() {
                let id = $(this).data('id');
                if (confirm("Are you sure you want to delete this news?")) {
                    $.ajax({
                        url: "{{ route('admin.news.delete', '') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            table.ajax.reload();
                            alert('News deleted successfully');
                        },
                        error: function() {
                            alert('Something went wrong!');
                        }
                    });
                }
            });
        });
    </script>

    <script>
        // Toggle News Status
        $(document).on('click', '.status-toggle', function() {
            let badge = $(this);
            let id = badge.data('id');

            if (!confirm('Are you sure you want to toggle the status of this news?')) return;

            $.ajax({
                url: "{{ route('admin.news.toggleStatus', ':id') }}".replace(':id', id),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.success) {
                        let newColor = res.status === 'active' ? 'success' : 'secondary';
                        badge.removeClass('bg-success bg-secondary').addClass('bg-' + newColor);
                        badge.text(res.status.charAt(0).toUpperCase() + res.status.slice(1));
                    }
                },
                error: function() {
                    alert('Failed to update status!');
                }
            });
        });
    </script>
@endpush
