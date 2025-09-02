@extends('backend.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Ecommerce Starts -->
                <section id="dashboard-ecommerce">
                    <div class="row match-height">
                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="card card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="info">
                                        <h5>{{ $greetings['message'] }} {{ auth()->user()->name }}</h5>
                                        <p class="card-text font-small-3">Your Site Updates</p>
                                    </div>
                                    <div class="img">
                                        @if ($greetings['type'] == 'morning')
                                            <img src="{{ asset('backend/assets/greetings/004-sunrise.png') }}"
                                                alt="Gooddo Morning" />
                                        @elseif ($greetings['type'] == 'afternoon')
                                            <img src="{{ asset('backend/assets/greetings/002-sunsets.png') }}"
                                                alt="Gooddo Afternoon">
                                        @else
                                            <img src="{{ asset('backend/assets/greetings/003-cloudy-night.png') }}"
                                                alt="Gooddo Night">
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <div class="card shadow-sm border-0 rounded-lg">
                                <div class="card-body dash-card p-3">
                                    <div class="d-flex align-items-center height-100 justify-content-center">
                                        <div class="icon mr-3">
                                            <img src="{{ asset('admin/images/customers.png') }}" alt=""
                                                style="width: 50px; height: 50px;">
                                        </div>
                                        <div class="info">
                                            <h5 class="font-weight-bold mb-1" style="font-size: 24px; color: #2c3e50;">
                                                {{ $totalCustomers }}</h5>
                                            <p class="card-text font-small-3 mb-0" style="font-size: 14px; color: #7f8c8d;">
                                                Total Customer's</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <div class="card shadow-sm border-0 rounded-lg">
                                <div class="card-body dash-card p-3">
                                    <div class="d-flex align-items-center height-100 justify-content-center">
                                        <div class="icon mr-3">
                                            <img src="{{ asset('admin/images/owners.png') }}" alt=""
                                                style="width: 50px; height: 50px;">
                                        </div>
                                        <div class="info">
                                            <h5 class="font-weight-bold mb-1" style="font-size: 24px; color: #2c3e50;">
                                                {{ $totalOwners }}</h5>
                                            <p class="card-text font-small-3 mb-0" style="font-size: 14px; color: #7f8c8d;">
                                                Total Owner's</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <div class="card shadow-sm border-0 rounded-lg">
                                <div class="card-body dash-card p-3">
                                    <div class="d-flex align-items-center height-100 justify-content-center">
                                        <div class="icon mr-3">
                                            <img src="{{ asset('admin/images/services.png') }}" alt=""
                                                style="width: 50px; height: 50px;">
                                        </div>
                                        <div class="info">
                                            <h5 class="font-weight-bold mb-1" style="font-size: 24px; color: #2c3e50;">
                                                {{ $totalServices }}</h5>
                                            <p class="card-text font-small-3 mb-0" style="font-size: 14px; color: #7f8c8d;">
                                                Total Services</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <div class="card shadow-sm border-0 rounded-lg">
                                <div class="card-body dash-card p-3">
                                    <div class="d-flex align-items-center height-100 justify-content-center">
                                        <div class="icon mr-3">
                                            <img src="{{ asset('admin/images/subscribtion.png') }}" alt=""
                                                style="width: 50px; height: 50px;">
                                        </div>
                                        <div class="info">
                                            <h5 class="font-weight-bold mb-1" style="font-size: 24px; color: #2c3e50;">
                                                0</h5>
                                            <p class="card-text font-small-3 mb-0" style="font-size: 14px; color: #7f8c8d;">
                                                Total Subscribers</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-md-12 col-12">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h6 class="mb-0">New Business Owners</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th>Avatar</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Register Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($owners as $o)
                                                <tr>
                                                    <td><img src="{{ asset($o->avatar) }}" width="30"
                                                            class="rounded-circle mr-1"></td>
                                                    <td>{{ $o->name }}</td>
                                                    <td>{{ $o->email }}</td>
                                                    <td>{{ $o->created_at->diffForHumans() }}</td>
                                                    <td><a href="{{ route('businessowners.profile', $o->username) }}"
                                                            class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> View
                                                            Profile</a></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No new owners found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-12 col-12">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h6 class="mb-0">New Customer's</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th>Avatar</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Register Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($customers as $c)
                                                <tr>
                                                    <td><img src="{{ asset($c->avatar) }}" width="30"
                                                            class="rounded-circle mr-1"></td>
                                                    <td>{{ $c->name }}</td>
                                                    <td>{{ $c->email }}</td>
                                                    <td>{{ $c->created_at->diffForHumans() }}</td>
                                                    <td><a href="{{ route('show.user', $c->id) }}" class="btn btn-sm btn-primary"><i
                                                                class="fa fa-eye"></i> View Profile</a></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No new customers found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xxl-4 col-md-6 col-12">
                            <div class="card">
                                <div class="card-header border-bottom d-flex justify-content-between mb-0">
                                    <div class="mb-0">
                                        <h6 class="mb-0">🔔 Subscription User's</h6>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        @forelse ($subscriptions_users as $su)
                                            <li class="my-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="p-2 me-2 rounded"><img width="40"
                                                            src="{{ asset($su->user->avatar) }}" alt=""></div>
                                                    <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                                                        <div class="me-2">
                                                            <h6 class="mb-0">{{ $su->user->name }}</h6>
                                                            <small class="text-body">{{ $su->user->email }}</small>
                                                        </div>
                                                        <div class="me-2">
                                                            <h6 class="mb-0">Subscription Plan</h6>
                                                            <small class="text-body">{{ $su->subscriptionPricingOption->subscriptionPlan->name }}
                                                                @if(App\Models\UserFreeTrial::where('user_id', $su->user->id)->where('subscription_plan_id', $su->subscriptionPricingOption->subscriptionPlan->id)->whereDate('end_date', '>=', now())->exists())
                                                                <span class="badge bg-danger ms-1" style="text-transform: capitalize">Trial</span>
                                                                @else
                                                                <span class="badge bg-success ms-1" style="text-transform: capitalize">{{ $su->subscriptionPricingOption->billing_period }}</span>

                                                                @endif

                                                            </small>
                                                        </div>
                                                        <div class="">
                                                            @if(App\Models\UserFreeTrial::where('user_id', $su->user->id)->where('subscription_plan_id', $su->subscriptionPricingOption->subscriptionPlan->id)->whereDate('end_date', '>=', now())->exists())
                                                                <p class="mb-0"><strong class="text-primary">Trial End In</strong>
                                                                    {{ Carbon\Carbon::parse(App\Models\UserFreeTrial::where('user_id', $su->user->id)->where('subscription_plan_id', $su->subscriptionPricingOption->subscriptionPlan->id)->first()->end_date)->format('M d, Y') }}
                                                                </p>
                                                            @elseif ($su->end_date > now())
                                                                <p class="mb-0"><strong class="text-success">Active</strong>
                                                                    {{ Carbon\Carbon::parse($su->start_date)->format('M d, Y') }}
                                                                </p>
                                                            @else
                                                                <p class="mb-0"><strong class="text-danger">Expired</strong>
                                                                    {{ Carbon\Carbon::parse($su->end_date)->format('M d, Y') }}
                                                                </p>
                                                            @endif
                                                            <small class="text-body"><a style="text-decoration: none;"
                                                                    class="text-danger"
                                                                    href="{{ route('show.user', $su->user->id) }}"><i
                                                                        class="fa fa-arrow-right"></i></a></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </li>
                                        @empty
                                            <li class="my-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="p-2 me-2 rounded"><img width="40" src=""
                                                            alt=""></div>
                                                    <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                                                        <div class="me-2">
                                                            <h6 class="mb-0">No Subscription User Found</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-12 col-12">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h6 class="mb-0">👤 Top Customers</h6>
                                </div>
                                <div class="card-body">
                                    @forelse ($topCustomers as $tc)
                                        <div class="d-flex align-items-center my-3 p-1 border rounded shadow-sm">
                                            <div class="avatar bg-light-info me-1">
                                                <div class="avatar-content">
                                                    <img src="{{ asset($tc->avatar) }}" alt="Customer Avatar"
                                                        class="img-fluid rounded-circle"
                                                        style="width: 30px; height: 30px;">
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $tc->name }}</h6>
                                                <small class="text-muted">{{ $tc->confirmed_count }} bookings</small>
                                            </div>
                                            <div class="ms-auto text-end">
                                                <span class="text-success">💲{{ $tc->confirmed_total ?? 0 }} Spent</span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted">No top customers found.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-12 col-12">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h6 class="mb-0">🔥 Top Services</h6>
                                </div>
                                <div class="card-body">
                                    @forelse ($topServices as $index => $service)
                                        <div class="d-flex align-items-center my-3 p-1 border rounded shadow-sm">
                                            <div class="avatar bg-light-primary me-1">
                                                <div class="avatar-content">
                                                    <img src="{{ asset($service->image) }}" alt="Service Image"
                                                        class="img-fluid rounded-circle"
                                                        style="width: 30px; height: 30px;">
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $service->title }}</h6>
                                                <small class="text-muted">By {{ $service->owner->name ?? 'N/A' }}</small>
                                            </div>
                                            <div class="ms-auto text-end">
                                                <span class="badge bg-success rounded-pill">{{ $service->total_bookings }}
                                                    Bookings</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="d-flex align-items-center my-3 p-1 border rounded shadow-sm">
                                            <p class="text-muted p-2 m-0">No top services found.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Dashboard Ecommerce ends -->
            </div>
        </div>
    </div>
@endsection
