@extends('backend.app')

@section('content')
    <div class="app-content content">
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Missions</h2>
                <a href="{{ route('admin.mission.create') }}" class="btn btn-primary">+ Add Mission</a>
            </div>

            <table class="table table-striped table-bordered w-100" id="missions-table">
                <thead class="">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('backend/assets/datatable/js/datatables.min.js') }}"></script>
    <script>
        $(function() {
            let table = $('#missions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.mission.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
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
                ]
            });

            // Delete Mission
            $(document).on('click', '.delete', function() {
                let id = $(this).data('id');

                if (confirm('Are you sure you want to delete this mission?')) {
                    $.ajax({
                        url: "{{ route('admin.mission.delete', ':id') }}".replace(':id', id),
                        type: 'DELETE', // ✅ must be DELETE
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.success) {
                                alert(res.message);
                                table.ajax.reload(null, false); // reload table via AJAX
                            }
                        },
                        error: function(xhr) {
                            alert('Delete failed! Please check the console.');
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>

<script>
    // Toggle Mission status
    $(document).on('click', '.status-toggle', function() {
        let badge = $(this);
        let id = badge.data('id');

        // Confirm before toggling
        if (!confirm('Are you sure you want to ' + (badge.text() === 'Active' ? 'deactivate' : 'activate') + ' this mission?')) {
            return; // exit if user cancels
        }

        // AJAX request to toggle status
        $.ajax({
            url: "{{ route('admin.mission.toggleStatus', ':id') }}".replace(':id', id),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if (res.success) {
                    let newColor = res.status === 'active' ? 'success' : 'secondary';
                    badge.removeClass('bg-success bg-secondary').addClass('bg-' + newColor);
                    badge.text(res.status.charAt(0).toUpperCase() + res.status.slice(1));
                }
            },
            error: function(xhr) {
                alert('Failed to update status!');
                console.log(xhr.responseText);
            }
        });
    });
</script>

@endpush
