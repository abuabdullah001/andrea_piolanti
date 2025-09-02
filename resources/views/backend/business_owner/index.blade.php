@extends('backend.app')

@section('title', 'Business Owner Page')

@push('style')
    <style>
        .dropify-wrapper {
            width: 160px;
        }

        #data-table th,
        #data-table td {
            text-align: center !important;
            vertical-align: middle !important;
        }
    </style>
@endpush
@section('content')
    <main class="app-content content">
        <div class="row">
            <div class="col-lg-12 mb-1">
                <div class="card">
                    <div class="card-header">
                        <h4 class="m-0">Business Owner List</h4>
                    </div>
                    <div class="card-body">
                        <table id="data-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Avatar</th>
                                    <th>Owner Name</th>
                                    <th>Owner Email</th>
                                    <th>Total Services</th>
                                    <th>Successful Bookings</th>
                                    <th>Total Earning</th>
                                    <th>Rating</th>
                                    <th>Since</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('script')
    <script src="{{ asset('backend/assets/datatable/js/datatables.min.js') }}"></script>

    {{-- Datatable --}}
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            $(document).ready(function() {
                if (!$.fn.DataTable.isDataTable('#data-table')) {
                    $('#data-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('businessowners.index') }}",
                        columns: [
                            {
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'avatar',
                                name: 'avatar',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: 'email',
                                name: 'email'
                            },
                            {
                                data: 'total_services',
                                name: 'total_services'
                            },
                            {
                                data: 'total_bookings',
                                name: 'total_bookings'
                            },
                            {
                                data: 'total_earning',
                                name: 'total_earning'
                            },
                            {
                                data: 'rating',
                                name: 'rating'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });
                }
            });

        });
    </script>
@endpush
