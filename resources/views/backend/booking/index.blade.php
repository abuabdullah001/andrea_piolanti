@extends('backend.app')

@section('title', 'All Bookings')

@push('style')
    <link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet" />
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
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Bookings</h4>
                    </div>
                    <div class="card-body">
                        <table id="data-table" class="table table-striped" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Booking Type</th>
                                    <th>Date</th>
                                    <th>Service Info</th>
                                    <th>Service Price</th>
                                    <th>Owner Info</th>
                                    <th>Items</th>
                                    <th>Subtotal</th>
                                    <th>Tax</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTable will be loaded here --}}
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
                        ajax: "{{ route('booking.index') }}",
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'booking_type',
                                name: 'booking_type',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'date',
                                name: 'date'
                            },
                            {
                                data: 'service_info',
                                name: 'service_info',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'service_price',
                                name: 'service_price'
                            },
                            {
                                data: 'owner_info',
                                name: 'owner_info',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'items',
                                name: 'items',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'subtotal',
                                name: 'subtotal'
                            },
                            {
                                data: 'tax',
                                name: 'tax'
                            },
                            {
                                data: 'total',
                                name: 'total'
                            },
                            {
                                data: 'status',
                                name: 'status',
                                orderable: false
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
