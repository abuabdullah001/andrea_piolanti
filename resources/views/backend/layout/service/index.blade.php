@extends('backend.app')

@section('title', 'All Services')

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
                        <h4>All Services</h4>
                    </div>
                    <div class="card-body">
                        <table id="data-table" class="table table-striped" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Service Image</th>
                                    <th>Service Title</th>
                                    <th>Service By</th>
                                    <th>Price</th>
                                    <th>Total Booked</th>
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

    <!-- Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">Service Details</h5>
                    <button type="button" class="btn btn-sm btn-danger" onclick="$('#serviceModal').modal('hide')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <img src="{{ asset('default.jpg') }}" alt="Service Image" class="img-fluid">
                            <p style="font-size: 12px; color: #888; margin-top: 5px; margin-bottom: 0;">Service Icon</p>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-stripe">
                                <tr>
                                    <th>Service Title</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Service By</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Price</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Available <br> Time Slots</th>
                                    <td class="text-start">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
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
                        ajax: "{{ route('allservice.index') }}",
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'image',
                                name: 'image',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'title',
                                name: 'title'
                            },
                            {
                                data: 'service_by',
                                name: 'service_by'
                            },

                            {
                                data: 'price',
                                name: 'price'
                            },
                            {
                                data: 'totalBookings',
                                name: 'totalBookings'
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

    <script>
        function formatTime(timeStr) {
            const [hour, minute] = timeStr.split(':');
            let h = parseInt(hour);
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12; // Convert 0 to 12 for midnight
            return `${String(h).padStart(2, '0')}:${minute} ${ampm}`;
        }

        function viewService(id) {
            $.ajax({
                url: "{{ route('get.serviceinfo', ':id') }}".replace(':id', id),
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    console.log(data);
                    $('#serviceModalLabel').text(data.title);
                    $('#serviceModal').modal('show');

                    $('#serviceModal img').attr('src', data.image ? "{{ asset('') }}" + data.image :
                        "{{ asset('default.jpg') }}");
                    $('#serviceModal .modal-body table tr:nth-child(1) td').text(data.title);
                    $('#serviceModal .modal-body table tr:nth-child(2) td').text(data.owner.name);
                    $('#serviceModal .modal-body table tr:nth-child(3) td').text(data.price);
                    $('#serviceModal .modal-body table tr:nth-child(4) td').text(data.description);
                    $('#serviceModal .modal-body table tr:nth-child(5) td').html('');
                    if (data.time_slots.length > 0) {
                        $.each(data.time_slots, function(index, time_slot) {
                            let formattedTime = formatTime(time_slot.time);
                            $('#serviceModal .modal-body table tr:nth-child(5) td').append(
                                '<span class="badge badge-secondary me-1">' + formattedTime +
                                '</span>');
                        });
                    } else {
                        $('#serviceModal .modal-body table tr:nth-child(5) td').text('No time slots available');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error getting data from ajax');
                }
            });
        }
    </script>
@endpush
