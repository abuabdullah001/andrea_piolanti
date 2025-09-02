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
                        <table id="data-table" class="table table-striped table-bordered table-hover"
                            style="width: 100%">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Service Image</th>
                                    <th>Service Title</th>
                                    <th>Service By</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <img src="{{ asset('default.jpg') }}"
                                            alt="Service Image" class="img-fluid" width="50">
                                    </td>
                                    <td>Website Development</td>
                                    <td>John Doe</td>
                                    <td>$500</td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#serviceModal"><i class="fa fa-eye"></i> View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>
                                        <img src="{{ asset('default.jpg') }}"
                                            alt="Service Image" class="img-fluid" width="50">
                                    </td>
                                    <td>SEO Optimization</td>
                                    <td>Sarah Smith</td>
                                    <td>$300</td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#serviceModal"><i class="fa fa-eye"></i> View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>
                                        <img src="{{ asset('default.jpg') }}"
                                            alt="Service Image" class="img-fluid" width="50">
                                    </td>
                                    <td>Graphic Design</td>
                                    <td>Alex Brown</td>
                                    <td>$200</td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#serviceModal"><i class="fa fa-eye"></i> View</a>
                                    </td>
                                </tr>
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
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <img src="{{ asset('default.jpg') }}" alt="Service Image" class="img-fluid">
                    </div>
                    <div class="col-md-9">
                        <table class="table table-stripe">
                            <tr>
                                <th>Service Title</th>
                                <td>Website Development</td>
                            </tr>
                            <tr>
                                <th>Service By</th>
                                <td>John Doe</td>
                            </tr>
                            <tr>
                                <th>Price</th>
                                <td>$500</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, tempore.</td>
                            </tr>
                            <tr>
                                <th>Available <br> Time Slots</th>
                                <td class="text-start">
                                    <ul class="d-flex flex-wrap justify-content-center align-items-center list-unstyled" style="gap: 10px;">
                                        <li><span class="badge badge-secondary">08:00 - 10:00</span></li>
                                        <li><span class="badge badge-secondary">10:00 - 12:00</span></li>
                                        <li><span class="badge badge-secondary">12:00 - 14:00</span></li>
                                        <li><span class="badge badge-secondary">14:00 - 16:00</span></li>
                                        <li><span class="badge badge-secondary">18:00 - 20:00</span></li>
                                        <li><span class="badge badge-secondary">20:00 - 22:00</span></li>
                                        <li><span class="badge badge-secondary">22:00 - 24:00</span></li>
                                        <li><span class="badge badge-secondary">24:00 - 02:00</span></li>
                                        <li><span class="badge badge-secondary">02:00 - 04:00</span></li>
                                        <li><span class="badge badge-secondary">04:00 - 06:00</span></li>
                                        <li><span class="badge badge-secondary">06:00 - 08:00</span></li>
                                    </ul>
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

@endpush
