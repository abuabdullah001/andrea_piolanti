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
                                    <th>User Image</th>
                                    <th>User Info</th>
                                    <th>Subscription Plan</th>
                                    <th>Transition ID</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <img src="{{ asset('default/user.png') }}"
                                            alt="User Image" class="img-fluid" width="50">
                                    </td>
                                    <td class="text-start">
                                        <h5 class="mb-0">John Doe</h5>
                                        <p class="mb-0">User</p>
                                        <p class="mb-0">john_doe@me.com</p>
                                    </td>
                                    <td>Basic/Monthly Package</td>
                                    <td>#TXI88SHFU42</td>
                                    <td>
                                        <span class="badge badge-success">Paid</span>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-success"><i class="fa fa-check"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-danger"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>



@endsection

@push('script')

@endpush
