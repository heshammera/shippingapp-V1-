@extends('layouts.app')

@section('title', 'تفاصيل الدور')

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil"></i> تعديل
        </a>
        <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-right"></i> العودة للقائمة
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>معلومات الدور</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl mb-3">
                        <span class="avatar-initial rounded-circle bg-primary">{{ substr($role->name, 0, 1) }}</span>
                    </div>
                    <h4>{{ $role->name }}</h4>
                    @if($role->description)
                        <p class="text-muted">{{ $role->description }}</p>
                    @endif
                </div>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>عدد المستخدمين</span>
                        <span class="badge bg-info">{{ $role->users->count() }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>عدد الصلاحيات</span>
                        <span class="badge bg-primary">{{ $role->permissions->count() }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>تاريخ الإنشاء</span>
                        <span>{{ $role->created_at->format('Y-m-d') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>الصلاحيات</h5>
            </div>
            <div class="card-body">
                @if($role->permissions->count() > 0)
                    <div class="row">
                        @php
                            $groupedPermissions = $role->permissions->groupBy('group');
                        @endphp
                        
                        @foreach($groupedPermissions as $group => $permissions)
                            <div class="col-md-6 mb-4">
                                <h6>{{ $group }}</h6>
                                <ul class="list-group">
                                    @foreach($permissions as $permission)
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            {{ $permission->description }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        لا توجد صلاحيات محددة لهذا الدور.
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>المستخدمين في هذا الدور</h5>
                <span class="badge bg-info">{{ $users->total() }}</span>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-danger">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        لا يوجد مستخدمين في هذا الدور.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
    }
    
    .avatar-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 600;
        color: #fff;
    }
</style>
@endsection
