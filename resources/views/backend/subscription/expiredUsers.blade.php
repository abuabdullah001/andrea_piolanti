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
                                    <th>Expired In</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $key => $u)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        <img src="{{ asset($u->user->avatar) }}"
                                            alt="User Image" class="img-fluid" width="50">
                                    </td>
                                    <td class="text-start">
                                        <h5 class="mb-0">{{ $u->user->name }}</h5>
                                        <p class="mb-0">{{ $u->user->email }}</p>
                                        <p class="mb-0">{{ $u->user->phone }}</p>
                                    </td>
                                    <td>{{ $u->subscriptionPricingOption->subscriptionPlan->name }}/{{ $u->subscriptionPricingOption->billing_period }} Package</td>
                                    <td><span class="badge badge-danger">{{ Carbon\Carbon::parse($u->end_date)->format('d M Y') }}</span></td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-success">Send Renewal Request</a>
                                        <a href="{{ route('show.user', $u->user->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No Data Found</td>
                                </tr>
                                @endforelse
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

