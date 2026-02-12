@extends('layouts.app')

@section('title', 'تفاصيل المستخدم')

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil"></i> تعديل
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-right"></i> العودة للقائمة
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>معلومات المستخدم</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl mb-3">
                        <span class="avatar-initial rounded-circle bg-primary">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    
                    @if($user->is_active)
                        <span class="badge bg-success">نشط</span>
                    @else
                        <span class="badge bg-danger">غير نشط</span>
                    @endif
                </div>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>الدور</span>
                        <span class="badge bg-info">{{ $user->role ?? 'غير محدد' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>رقم الهاتف</span>
                        <span>{{ $user->phone ?? 'غير محدد' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>آخر تسجيل دخول</span>
                        <span>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'لم يسجل الدخول بعد' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>تاريخ الإنشاء</span>
                        <span>{{ $user->created_at->format('Y-m-d') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @if($user->expires_at)
                    <div class="text-muted mt-1">
                       <span> تاريخ الانتهاء: {{ $user->expires_at }} / الأيام المتبقية: {{ $expires_days }}</span>
                    </div>
                        @endif
                    </li>

                </ul>
            </div>
        </div>


        @if($user->address)
            <div class="card mb-4">
                <div class="card-header">
                    <h5>العنوان</h5>
                </div>
                <div class="card-body">
                    <p>{{ $user->address }}</p>
                </div>
            </div>
        @endif
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>الصلاحيات</h5>
            </div>
<div class="card-body">
    @php
        // جلب جميع الصلاحيات الفعالة للمستخدم (سواء المباشرة أو عبر الأدوار)
        $permissions = $user->getAllPermissions();
    @endphp

    @if($permissions->count() > 0)
        <div style="max-height: 300px; overflow-y: auto;">
            <ul class="list-group">
                @foreach($permissions as $permission)
                    <li class="list-group-item py-1">
                        <i class="bi bi-check-circle-fill text-success me-2 small"></i> 
                        <span class="small">{{ $permission->name }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="alert alert-info">
            لا توجد صلاحيات محددة لهذا المستخدم.
        </div>
    @endif
</div>

        </div>
        
<div class="card">
    <div class="card-header">
        <h5>سجل النشاط</h5>
    </div>
    <div class="card-body">
        @php
            use Spatie\Activitylog\Models\Activity;

            // عرض النشاطات التي تمت *على* هذا المستخدم (تعديل، تغيير دور، إلخ)
            // Subject = User (Current User Model)
            $activities = Activity::where('subject_type', \App\Models\User::class)
                ->where('subject_id', $user->id)
                ->with('causer')
                ->latest()
                ->take(10)
                ->get();
        @endphp

        @if ($activities->isNotEmpty())
            <ul class="list-group">
                @foreach ($activities as $activity)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <i class="bi bi-clock-history me-2 text-secondary"></i>
                                <strong>{{ $activity->description }}</strong>
                                <br>
                                <small class="text-muted">
                                    بواسطة: {{ $activity->causer ? $activity->causer->name : 'النظام' }}
                                </small>
                            </div>
                            <small class="text-muted">
                                {{ $activity->created_at->format('Y-m-d H:i') }} <br>
                                {{ $activity->created_at->diffForHumans() }}
                            </small>
                        </div>
                        {{-- عرض التغييرات إذا وجدت --}}
                        @if($activity->properties && $activity->properties->has('attributes'))
                             <div class="mt-2 small bg-light p-2 rounded">
                                @foreach($activity->properties['attributes'] as $key => $value)
                                    @if(isset($activity->properties['old'][$key]))
                                        <span class="d-block text-danger">- {{ $key }}: {{ $activity->properties['old'][$key] }}</span>
                                        <span class="d-block text-success">+ {{ $key }}: {{ $value }}</span>
                                    @else
                                        <span class="d-block text-success">+ {{ $key }}: {{ $value }}</span>
                                    @endif
                                @endforeach
                             </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-info">
                لا يوجد سجل تعديلات لهذا المستخدم حتى الآن.
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
