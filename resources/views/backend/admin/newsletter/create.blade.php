@extends('backend.app')

@section('content')
<div class="app-content content">
    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Create Newsletter</h4>
            <a href="{{ route('admin.newsletter.index') }}" class="btn btn-secondary">Back to List</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                {{-- Show validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Newsletter form --}}
                <form action="{{ route('admin.newsletter.store') }}" method="POST">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">Newsletter Title</label>
                        <input type="text" name="title" id="title" class="form-control"
                               placeholder="Enter newsletter title" value="{{ old('title') }}" required>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="6" class="form-control summernote"
                                  placeholder="Enter newsletter content" required>{{ old('description') }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-success">Create Newsletter</button>
                    <a href="{{ route('admin.newsletter.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@push('script')
    <!-- Summernote CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">

    <!-- jQuery (required for Summernote) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Summernote JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200, // editor height
                placeholder: 'Write here...',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['fontsize', 'color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>


@endpush

