@extends('backend.app')

@section('title', 'Subcription Plans')

@push('styles')
    <style>
        .subs_plan {
            background: #fff;
            transition: all 0.3s ease-in-out;
        }

        .subs_plan:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .subs_plan h5 {
            font-size: 1.25rem;
        }

        .subs_plan ul li {
            font-size: 0.875rem;
        }

        .hover-shadow:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
        }
    </style>
@endpush

@section('content')
    <main class="app-content content">
        <div class="p-3">
            <div class="card">
                <div class="card-header mb-3">
                    <h4 class="card-title text-center">Subscription Plans</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($subscriptions as $plan)
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="subs_plan card h-100 border-0 shadow-sm p-3 text-center rounded-4 hover-shadow">
                                    <h5 class="fw-bold text-primary mb-1">{{ $plan->name }}</h5>
                                    <p class="text-muted small">{{ $plan->description }}</p>

                                    <hr>

                                    <div class="fw-semibold text-dark mb-2">
                                        @foreach ($plan->pricingOptions as $key => $pricePeriod)
                                            <span class="d-inline-block">
                                                {{ $pricePeriod->price ?? 'Custom' }}/{{ $pricePeriod->billing_period ?? 'Custom' }}
                                                @if (!$loop->last)
                                                    <span class="text-secondary mx-1">|</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>

                                    <hr>

                                    <ul class="list-unstyled text-start ps-2 mb-3">
                                        @foreach ($plan->features as $feature)
                                            <li class="my-1">
                                                <i class="fa fa-check text-success me-2"></i> {{ $feature->name }}
                                            </li>
                                        @endforeach
                                    </ul>

                                    <hr>

                                    <div class="alert alert-info py-1 mb-3">
                                        Total Users: 0
                                    </div>

                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <a href="{{ route('subscription.edit', $plan->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-edit me-1"></i> Edit
                                        </a>
                                        {{-- <a href="#" class="btn btn-sm btn-outline-info">
                                            <i class="fa fa-user me-1"></i> View Users
                                        </a> --}}
                                        <a href="javascript:void(0);" onclick="status({{ $plan->id }})" class="btn btn-sm btn-outline-{{ $plan->status == 'active' ? 'success' : 'danger' }}">
                                            <i class="fa {{ $plan->status == 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }} me-1"></i> {{ $plan->status == 'active' ? 'Enable' : 'Disable' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('script')
{{-- Status --}}
<script>
    function status(id) {
        $.ajax({
            url: "{{ route('subscription.status') }}",
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
