@extends('layouts.app')

@section('title', 'تعديل صلاحيات الدور')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>تعديل صلاحيات الدور: {{ $role->name }}</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('roles.permissions.update', $role) }}" method="POST">
            @csrf

            <div class="row">
                @foreach($permissions as $permission)
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                id="perm_{{ $permission->id }}"
                                {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-save"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
