@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('actions')
    <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-person-plus"></i> إضافة مستخدم جديد
    </a>
@endsection

@section('content')



<div class="card">
    <div class="card-header">
        <h5>قائمة المستخدمين</h5>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الدور</th>
                            <th>الحالة</th>
                            <th>آخر تسجيل دخول</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @php
    if (is_null($user->expires_at)) {
        $statusText = 'مدى الحياة';
        $statusColor = 'success'; // أخضر
    } else {
        $diff = now()->diffInDays($user->expires_at, false); // ممكن يطلع بالسالب

        if ($diff < 1) {
            $statusText = 'أقل من يوم';
            $statusColor = 'danger'; // أحمر
        } elseif ($diff < 3) {
            $statusText = "$diff يوم";
            $statusColor = 'warning'; // برتقالي
        } else {
            $statusText = "$diff يوم";
            $statusColor = 'primary'; // أزرق
        }
    }
@endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
<td>{{ $labels[$user->role] ?? 'غير محدد' }}</td>

</td>





                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>
<td>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'لم يسجل الدخول بعد' }}</td>
<td>
    <span class="badge bg-{{ $statusColor }}">
        {{ $statusText }}
    </span>
</td>


<td>
    <div class="btn-group" role="group">
        @if($user && $user->id)
            <a href="{{ route('users.show', ['user' => $user->id]) }}" class="btn btn-sm btn-info">
                <i class="bi bi-eye"></i>
            </a>

            <a href="{{ route('users.edit', ['user' => $user->id]) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i>
            </a>

            @if(auth()->id() !== $user->id)
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                    <i class="bi bi-trash"></i>
                </button>
            @endif
        @endif
    </div>

    {{-- مودال تأكيد الحذف --}}
    @if($user && $user->id && auth()->id() !== $user->id)
        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">تأكيد الحذف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        هل أنت متأكد من حذف المستخدم "{{ $user->name }}"؟
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>

                        <form action="{{ route('users.destroy', ['user' => $user->id]) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">حذف</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                لا يوجد مستخدمين مضافين حتى الآن. <a href="{{ route('users.create') }}">إضافة مستخدم جديد</a>
            </div>
        @endif
    </div>
    </div>
</div>
</form>

@endsection

@section('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    function submitBulk(action) {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('الرجاء تحديد مستخدم واحد على الأقل.');
            return;
        }
        
        document.getElementById('bulkActionInput').value = action;
        document.getElementById('mainBulkForm').submit();
    }
</script>
@endsection
