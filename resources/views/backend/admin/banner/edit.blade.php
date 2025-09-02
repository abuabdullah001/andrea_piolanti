@extends('backend.app')

@section('content')

<div class="app-content content">
    <div class="container">
        <h1 class="mb-4">Edit Banner</h1>

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

        {{-- Banner Edit Form --}}
        <form action="{{ route('admin.banner.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Banner Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $banner->title) }}" >
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Banner Description</label>
                <textarea name="description" id="description" class="form-control summernote" rows="4" >{{ old('description', $banner->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Banner Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                @if($banner->image)
                    <img src="{{ asset($banner->image) }}" width="150" class="mt-2">
                @endif
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" >
                    <option value="1" {{ old('status', $banner->status) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $banner->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update Banner</button>
            <a href="{{ route('admin.banner.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
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
