@extends('backend.app')

@section('title', 'Subcription Plans Edit')

@section('content')
<main class="app-content content">
    <form action="{{ route('subscription.update') }}" method="POST">@csrf
        <input type="hidden" name="id" value="{{ $plan->id }}">
        <div class="p-3">
            <div class="card">
                <div class="card-header mb-3">
                    <h4 class="card-title text-center">Edit {{ $plan->name }} Subscription Plans</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <label for=""><b><span class="text-uppercase">{{ $plan->name }} </span>Plan Info</b></label>
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <label for="" class="form-label">Plan Title</label>
                                <input type="text" name="name" class="form-control" placeholder="Plan Title..."
                                    value="{{ old('name', $plan->name) }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <label for="" class="form-label">Plan Description</label>
                                <input type="text" name="description" class="form-control" placeholder="Plan description..."
                                    value="{{ old('description', $plan->description) }}">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                            <div class="mb-2">
                                <div class="row">
                                    @foreach ($plan->pricingOptions as $price)
                                        <div class="col-lg-6">
                                            <label for=""><b><span class="text-uppercase">{{ $price->billing_period }}</span> Pricing Plan</b></label>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="mb-2">
                                                        <label for="" class="form-label">Price</label>
                                                        <input readonly type="text" name="price[{{ $price->id }}]" class="form-control"
                                                            placeholder="Price..." value="{{ old('price', $price->price) }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-2">
                                                        <label for="" class="form-label">Period</label>
                                                        <input readonly type="text" name="billing_period" class="form-control cursor-not-allowed"
                                                            placeholder="billing_period..." value="{{ old('billing_period', $price->billing_period) }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-2">
                                                        <label for="" class="form-label">Duration</label>
                                                        <input readonly type="text" name="duration_days" class="form-control cursor-not-allowed"
                                                            placeholder="duration_days..." value="{{ old('duration_days', $price->duration_days) }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-2">
                                                        <label for="" class="form-label">Note</label>
                                                        <input readonly type="text" name="discount_note[{{ $price->id }}]" class="form-control"
                                                            placeholder="discount_note..." value="{{ old('discount_note', $price->discount_note) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                            <div class="mb-2">
                                <label for=""><b><span class="text-uppercase">Features</span></b></label>
                                <div class="row">
                                    @foreach ($features->chunk(6) as $chunk)
                                        <div class="col-lg-3">
                                            <ul class="list-unstyled">
                                                @foreach ($chunk as $feat)
                                                    <li>
                                                        <input type="checkbox" name="features[]" value="{{ $feat->id }}"
                                                            {{ in_array($feat->id, $plan->features->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                        {{ $feat->name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-2">
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection
