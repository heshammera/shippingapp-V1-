@extends('layouts.app')

@section('title', 'تعديل الدور')

@section('actions')
    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للقائمة
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>تعديل الدور: {{ $role->name }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('roles.update', $role->id) }}">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">اسم الدور <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الوصف</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description', $role->description) }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">الصلاحيات المرتبطة بالدور:</label>
                <div class="row">
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="col-md-4">
                            <h6 class="text-primary">{{ $group }}</h6>
                            @foreach($groupPermissions as $permission)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                           value="{{ $permission->id }}"
                                           id="perm_{{ $permission->id }}"
                                           {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
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
                    <i class="bi bi-save"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
