@extends('backend.app')

@section('title', 'View Booking Details')

@section('content')
<main class="app-content content">
    <div class="container mt-4">
        <div class="card shadow p-4">
            <h4 class="mb-3">Booking Details <span class="text-uppercase float-right badge
                @if($booking->status == 'pending') badge-warning
                @elseif($booking->status == 'confirmed') badge-success
                @else badge-secondary @endif">
                {{ $booking->status }}
            </span></h4>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Booking Type:</strong>
                    <span class="badge {{ $booking->booking_type == 'custom' ? 'badge-info' : 'badge-secondary' }}">
                        {{ ucfirst($booking->booking_type) }}
                    </span>
                </div>
                <div class="col-md-6 text-right">
                    <strong>Date:</strong> {{ $booking->created_at->format('Y-m-d') }}
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Customer Info</h5>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('user.png') }}" width="40" class="rounded-circle mr-2">
                        <div>
                            <strong>{{ $booking->customer->name }}</strong><br>
                            <small>{{ $booking->customer->email }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Owner Info</h5>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('user.png') }}" width="40" class="rounded-circle mr-2">
                        <div>
                            <strong>{{ $booking->owner->name }}</strong><br>
                            <small>{{ $booking->owner->email }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <h5>Service Info</h5>
            <div class="d-flex align-items-center mb-3">
                <img src="{{ asset('default.png') }}" width="60" class="rounded mr-3">
                <div>
                    <strong>{{ $booking->service->title }}</strong><br>
                    <small>${{ $booking->service->price }}</small>
                </div>
            </div>

            @if ($booking->items->count())
                <h5>Included Items</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($booking->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td class="text-right">${{ number_format($item->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="row mt-4">
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 text-right">
                    <strong>Subtotal:</strong> ${{ number_format($booking->subtotal, 2) }} <br>
                    <strong>Tax:</strong> ${{ number_format($booking->tax, 2) }} <br>
                    <strong>Total:</strong> ${{ number_format($booking->total, 2) }}
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('booking.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Bookings
                </a>
            </div>
        </div>
    </div>
</main>
@endsection

