@extends('backend.app')

@section('title', 'Business Owner Profile Page')

@push('style')
@endpush

@section('content')
    <main class="app-content content">
        <div class="row mb-2">
            <!-- Profile Overview -->
            <div class="col-md-4">
                <div class="card text-center p-3 shadow rounded-4">
                    <img src="{{ asset($user->avatar) }}" class="rounded-circle mx-auto mb-3" width="120" height="120"
                        alt="Avatar">
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <hr>
                    <h6 class="text-uppercase text-secondary">About Me</h6>
                    <p>{{ $user->about_me ?? 'No information available' }}</p>
                    <h6 class="text-uppercase text-secondary mt-3">Description</h6>
                    <p>{{ $user->description ?? 'No information available' }}</p>
                </div>
            </div>

            <!-- Image Gallery -->
            <div class="col-md-8">
                <div class="card p-3 shadow rounded-4">
                    <h5 class="mb-3">Gallery</h5>
                    <div class="row">
                        @forelse($user->images as $img)
                            <div class="col-md-4 mb-3">
                                <img src="{{ $img }}" class="img-fluid rounded shadow-sm" alt="Gallery Image">
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">No images available.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Services -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card p-3 shadow rounded-4">
                    <h5 class="mb-3">Services</h5>
                    <div class="row">
                        @forelse($user->services as $service)
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body rounded-3" style="box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="card-service-img">
                                                <img src="{{ asset($service->image) }}" width="50" class=""
                                                    alt="Service Image">
                                            </div>
                                            <div class="card-service-info flex-grow-1">
                                                <h6 class="card-title mb-0">{{ $service->title }}</h6>
                                                <p class="card-text text-primary fw-bold">${{ $service->price }}</p>
                                            </div>
                                            <div class="status_switch">
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input"
                                                        onclick="changeStatus({{ $service->id }})" type="checkbox"
                                                        {{ $service->status == 'active' ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">No services available.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        {{-- <div class="row">
        <div class="col-12">
            <div class="card p-3 shadow rounded-4">
                <h5 class="mb-3">Reviews</h5>
                @forelse($user->reviews as $review)
                    <div class="mb-3 border-bottom pb-2">
                        <strong>{{ $review->user_name }}</strong>
                        <span class="text-muted small">({{ $review->created_at->format('M d, Y') }})</span>
                        <p>{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="text-muted">No reviews yet.</p>
                @endforelse
            </div>
        </div>
    </div> --}}
    </main>
@endsection

@push('script')
    <script>
        function changeStatus(id) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to update the status?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '{{ route('businessowners.status', ':id') }}';
                    $.ajax({
                        type: "GET",
                        url: url.replace(':id', id),
                        success: function(resp) {
                            if (resp.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: resp.message,
                                    showConfirmButton: false,
                                    timer: 800
                                });

                                setTimeout(function() {
                                    location.reload();
                                }, 800);

                            } else if (resp.errors) {
                                Swal.fire({
                                    icon: "error",
                                    title: resp.errors[0],
                                    showConfirmButton: false,
                                    timer: 800
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: resp.message,
                                    showConfirmButton: false,
                                    timer: 800
                                });
                            }
                        },
                        error: function(error) {
                            // location.reload();
                        }
                    })
                }
            });
        }
    </script>
@endpush
