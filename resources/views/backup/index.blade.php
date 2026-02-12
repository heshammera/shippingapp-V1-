@extends('layouts.app')
@section('title', 'النسخ الاحتياطية')

@section('content')
<div class="container">
    <h4 class="mb-3">النسخ الاحتياطية</h4>

    <div class="mb-3">
        <button class="btn btn-primary me-2" onclick="runBackup(false)">🔄 نسخة للبرنامج بالكامل</button>
        <button class="btn btn-secondary" onclick="runBackup(true)">💾 نسخة قاعدة بيانات فقط</button>
    </div>

    <div class="progress mb-3" style="height: 20px; display: none;" id="progress-container">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
             style="width: 0%;" id="progress-bar">0%</div>
    </div>

    <table class="table table-bordered" id="backups-table">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>الحجم</th>
                <th>التاريخ</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
function runBackup(onlyDb = false) {
    const url = onlyDb ? '{{ route('backup.create.db') }}' : '{{ route('backup.create') }}';
    const progress = document.getElementById('progress-container');
    const bar = document.getElementById('progress-bar');

    bar.style.width = '0%';
    bar.innerText = '0%';
    progress.style.display = 'block';

    let percent = 0;
    const interval = setInterval(() => {
        percent += 10;
        if (percent > 90) percent = 90;
        bar.style.width = percent + '%';
        bar.innerText = percent + '%';
    }, 400);

    fetch(url, {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
        .then(res => res.json())
        .then(data => {
            clearInterval(interval);
            bar.style.width = '100%';
            bar.innerText = 'تم ✅';
            loadBackups();
            setTimeout(() => progress.style.display = 'none', 1500);
        });
}

function loadBackups() {
    fetch('{{ route('backup.list') }}')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#backups-table tbody');
            tbody.innerHTML = '';
            data.forEach(file => {
                const row = `<tr>
                    <td>${file.name}</td>
                    <td>${(file.size / 1024 / 1024).toFixed(2)} MB</td>
                    <td>${new Date(file.date * 1000).toLocaleString()}</td>
                    <td>
                        <a href="/backup/download/${file.name}" class="btn btn-sm btn-success">تحميل</a>
                        <a href="/backup/delete/${file.name}" class="btn btn-sm btn-danger" onclick="return confirm('حذف؟')">حذف</a>
                        <button class="btn btn-sm btn-warning" onclick="restoreBackup('${file.name}')">استرجاع</button>
                    </td>
                </tr>`;
                tbody.innerHTML += row;
            });
        });
}

function restoreBackup(file) {
    if (!confirm('هل أنت متأكد من استرجاع النسخة؟')) return;
    const progress = document.getElementById('progress-container');
    const bar = document.getElementById('progress-bar');
    bar.style.width = '0%';
    bar.innerText = '0%';
    progress.style.display = 'block';

    let percent = 0;
    const interval = setInterval(() => {
        percent += 10;
        if (percent > 90) percent = 90;
        bar.style.width = percent + '%';
        bar.innerText = percent + '%';
    }, 500);

    fetch('/backup/restore/' + file)
        .then(res => res.json())
        .then(() => {
            clearInterval(interval);
            bar.style.width = '100%';
            bar.innerText = 'تم ✅';
            loadBackups();
            setTimeout(() => progress.style.display = 'none', 1500);
        });
}

document.addEventListener('DOMContentLoaded', loadBackups);
</script>
@endsection
