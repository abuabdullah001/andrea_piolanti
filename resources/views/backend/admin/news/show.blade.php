@extends('backend.app')

@section('content')
<div class="app-content content">
    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">📰 News Details</h3>
            <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Back to News List
            </a>
        </div>

        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body p-4">

                {{-- Meta Info --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">
                        <i class="fa fa-user"></i> User ID: <strong>{{ $news->user_id }}</strong>
                    </span>
                    <span class="badge bg-{{ $news->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($news->status) }}
                    </span>
                    <span class="text-muted">
                        <i class="fa fa-calendar"></i> {{ $news->created_at->format('d M Y') }}
                    </span>
                </div>

                {{-- News Image --}}
                @if($news->image)
                    <div class="text-center mb-4">
                        <img src="{{ asset($news->image) }}" alt="News Image"
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 350px; object-fit: cover;">
                    </div>
                @endif

                {{-- News Title --}}
                <h2 class="fw-bold mb-3 text-primary">{{ $news->title }}</h2>

                {{-- News Description --}}
                <div class="fs-6 lh-lg text-dark" style="white-space: pre-line;">
                    {!! $news->description !!}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
