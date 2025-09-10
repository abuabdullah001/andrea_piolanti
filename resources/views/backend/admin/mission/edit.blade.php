@extends('backend.app')

@section('content')
<div class="app-content content">
    <div class="mt-4">
        <h2>Edit Mission</h2>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.mission.update', $mission->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Mission Title</label>
                <input type="text" name="title" id="title" 
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $mission->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mission Description</label>
                <textarea name="description" id="description" rows="5"
                          class="form-control @error('description') is-invalid @enderror"
                          required>{{ old('description', $mission->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="active" {{ old('status', $mission->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $mission->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update Mission</button>
            <a href="{{ route('admin.mission.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
