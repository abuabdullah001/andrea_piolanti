@extends('backend.app')

@section('title', 'Promo Page')

@push('style')
    <link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet" />
    <style>
        .dropify-wrapper {
            width: 160px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
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
                        <h4 class="m-0">Promo <span id="Promotitle">Create</span></h4>
                    </div>
                    <div class="card-body">
                        <form id="createPromo" action="{{ route('promo.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-lg-4">
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Customer <span
                                                class="text-danger">*</span></label>
                                        <div class="col-9">
                                            <select class="form-control" name="user_id">
                                                <option value="">-- Select Customer --</option>
                                                @foreach ($customers as $s)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Code <span
                                                class="text-danger">*</span></label>
                                        <div class="col-9">
                                            <input type="text" name="promo_code" class="form-control"
                                                placeholder="PROMO2025..." value="{{ old('promo_code') }}">
                                            <div style="display: none" class="text-danger codeExists">
                                                This Code Already Taken
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Start Date</label>
                                        <div class="col-9">
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ old('start_date') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Discount Type <span
                                                class="text-danger">*</span></label>
                                        <div class="col-9">
                                            <select class="form-control" name="discount_type" id="">
                                                <option value="">-- Select Type --</option>
                                                <option value="fixed">Fixed</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Value <span
                                                class="text-danger">*</span></label>
                                        <div class="col-9">
                                            <input type="number" name="discount_value" class="form-control"
                                                placeholder="amount" value="{{ old('discount_value') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">End Date</label>
                                        <div class="col-9">
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ old('end_date') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Title</label>
                                        <div class="col-9">
                                            <input type="text" name="title" class="form-control"
                                                placeholder="2025 Mega Discount" value="{{ old('title') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Description</label>
                                        <div class="col-9">
                                            <textarea name="description" class="form-control" placeholder="description..." value="{{ old('description') }}"
                                                rows="4"></textarea>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-sm btn-primary">Create</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form style="display: none;" id="editPromo" action="{{ route('promo.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Customer <span
                                                class="text-danger">*</span></label>
                                        <div class="col-9">
                                            <select class="form-control" name="user_id">
                                                <option value="">-- Select Customer --</option>
                                                @foreach ($customers as $s)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Code <span
                                                class="text-danger">*</span></label>
                                        <div class="col-9">
                                            <input type="text" name="promo_code" class="form-control"
                                                placeholder="PROMO2025..." value="{{ old('promo_code') }}">
                                            <div style="display: none" class="text-danger codeExists">
                                                This Code Already Taken
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Start Date</label>
                                        <div class="col-9">
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ old('start_date') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Discount Type <span
                                                class="text-danger">*</span></label>
                                        <div class="col-9">
                                            <select class="form-control" name="discount_type" id="">
                                                <option value="">-- Select Type --</option>
                                                <option value="fixed">Fixed</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Value <span
                                                class="text-danger">*</span></label>
                                        <div class="col-9">
                                            <input type="number" name="discount_value" class="form-control"
                                                placeholder="amount" value="{{ old('discount_value') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">End Date</label>
                                        <div class="col-9">
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ old('end_date') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Title</label>
                                        <div class="col-9">
                                            <input type="text" name="title" class="form-control"
                                                placeholder="2025 Mega Discount" value="{{ old('title') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="" class="col-3 col-form-label">Description</label>
                                        <div class="col-9">
                                            <textarea name="description" class="form-control" placeholder="description..." value="{{ old('description') }}"
                                                rows="4"></textarea>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mb-1">
                <div class="card">
                    <div class="card-header">
                        <h4 class="m-0">Promo List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-table" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Code</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
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
        </div>
    </main>
@endsection

@push('script')
    <script src="{{ asset('backend/assets/datatable/js/datatables.min.js') }}"></script>

    {{-- Date Formating --}}
    <script>
        function formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toISOString().split('T')[0];
        }
    </script>

    {{-- Datatable --}}
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });

            if (!$.fn.DataTable.isDataTable('#data-table')) {
                $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('promo.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'promo_code',
                            name: 'promo_code'
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
                            data: 'discount_type',
                            name: 'discount_type'
                        },
                        {
                            data: 'discount_value',
                            name: 'discount_value'
                        },
                        {
                            data: 'start_date',
                            name: 'start_date'
                        },
                        {
                            data: 'end_date',
                            name: 'end_date'
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
            }
        });
    </script>

    {{-- Exists Check --}}
    <script>
        $('input[name="promo_code"]').on('keyup', function() {
            let input = $(this).val();
            let PromoId = $('input[name="id"]').val();
            checkPromoName(PromoId, input);
        });

        function checkPromoName(id, code) {
            $.ajax({
                url: "{{ route('promo.get') }}",
                type: "GET",
                data: {
                    id: id,
                    code: code,
                },
                success: function(response) {
                    if (response.length > 0) {
                        $('.codeExists').show();
                    } else {
                        $('.codeExists').hide();
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>

    {{-- Edit --}}
    <script>
        function editPromo(id) {
            $.ajax({
                url: "{{ route('promo.get') }}",
                type: "GET",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response) {
                        let promo = response[0];

                        $('#editPromo input[name="id"]').val(promo.id);
                        $('#editPromo select[name="user_id"]').val(promo.user_id);
                        $('#editPromo input[name="promo_code"]').val(promo.promo_code);
                        $('#editPromo input[name="title"]').val(promo.title);
                        $('#editPromo textarea[name="description"]').val(promo.description);
                        $('#editPromo select[name="discount_type"]').val(promo.discount_type);
                        $('#editPromo input[name="discount_value"]').val(promo.discount_value);
                        $('#editPromo input[name="start_date"]').val(formatDate(promo.start_date));
                        $('#editPromo input[name="end_date"]').val(formatDate(promo.end_date));

                        $('#editPromo').show();
                        $('#createPromo').hide();

                        $('html, body').animate({
                            scrollTop: 0
                        }, 'slow');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>

    {{-- Status --}}
    <script>
        function toggleStatus(id) {
            $.ajax({
                url: "{{ route('promo.status') }}",
                type: "GET",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });

                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Something Went Wrong'
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>

@endpush
