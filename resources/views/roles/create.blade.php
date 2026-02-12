@extends('layouts.app')

@section('title', 'إضافة دور جديد')

@section('actions')
    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للقائمة
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>إضافة دور جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">اسم الدور <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="description" class="form-label">الوصف</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">الصلاحيات:</label>
                <div class="row">
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="col-md-4 mb-3">
                            <h6 class="text-primary">{{ $group }}</h6>
                            @foreach($groupPermissions as $permission)
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="permissions[]"
                                           value="{{ $permission->id }}"
                                           id="perm_{{ $permission->id }}"
                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                        {{ $permission->description ?? $permission->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> حفظ الدور
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
