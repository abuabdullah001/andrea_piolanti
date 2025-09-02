@extends('backend.app')

@section('title', 'Banner List')

@section('content')
    <div class="app-content content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm rounded-2">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Banner List</h4>
                            <a href="{{ route('admin.banner.create') }}" class="btn btn-primary">Create Banner</a>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="data-table" class="table table-striped table-hover text-dark"
                                    style="width: 100%">
                                    <thead class="table-dark">
                                        <tr class="text-center align-middle text-dark">
                                            <th>SL</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ asset('backend/assets/datatable/js/datatables.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.banner.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                responsive: true
            });
        });

        // Delete Banner
        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');
            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: '/admin/banner/delete/' + id, // must match your route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        $('#data-table').DataTable().ajax.reload(null,
                        false); // refresh without resetting page
                        alert('Banner deleted successfully.');
                    },
                    error: function(xhr) {
                        alert('Something went wrong!');
                    }
                });
            }
        });
    </script>



<script>
function toggleStatus(el) {
    let id = $(el).data('id');
    let status = el.checked ? 1 : 0;

    let msg = status ? 'Are you sure you want to activate this banner?' : 'Are you sure you want to deactivate this banner?';

    if (!confirm(msg)) {
        el.checked = !el.checked; // revert checkbox
        return;
    }

    $.ajax({
        url: '/admin/banner/status/' + id, // matches route
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: status
        },
        success: function(res) {
            if(res.success) {
                // optional: toast
                console.log('Status updated to', res.status);
                // refresh DataTable row
                $('#data-table').DataTable().ajax.reload(null, false);
            } else {
                alert('Failed to update status!');
                el.checked = !el.checked;
            }
        },
        error: function(xhr) {
            alert('Something went wrong!');
            console.log(xhr.responseText);
            el.checked = !el.checked; // revert checkbox
        }
    });
}
</script>


@endpush
