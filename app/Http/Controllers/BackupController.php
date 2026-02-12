<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function index()
    {
        return view('backup.index');
    }

    public function listBackups()
    {
        $path = storage_path('app/backup');
        $files = collect(File::files($path))
            ->filter(fn($file) => $file->getExtension() === 'sql')
            ->map(fn($file) => [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'date' => $file->getMTime(),
            ])
            ->sortByDesc('date')
            ->values();

        return response()->json($files);
    }

    public function create()
    {
        $fileName = 'backup_' . date('Ymd_His') . '.sql';
        $backupPath = storage_path('app/backup/' . $fileName);

        $command = sprintf(
            'mysqldump -u%s -p%s %s > "%s"',
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DATABASE'),
            $backupPath
        );

        exec($command, $output, $result);

        if ($result === 0) {
            return response()->json(['status' => 'done', 'file' => $fileName]);
        } else {
            return response()->json(['status' => 'failed', 'error' => $output], 500);
        }
    }

    public function download($file)
    {
        $path = storage_path('app/backup/' . $file);
        return file_exists($path) ? response()->download($path) : abort(404);
    }

    public function delete($file)
    {
        $path = storage_path('app/backup/' . $file);
        if (file_exists($path)) unlink($path);
        return back();
    }

public function restore($file)
{
    $sqlPath = storage_path('app/backup/' . $file);

    if (!file_exists($sqlPath)) {
        return response()->json(['status' => 'failed', 'error' => 'الملف غير موجود'], 404);
    }

    $username = env('DB_USERNAME');
    $password = env('DB_PASSWORD');
    $database = env('DB_DATABASE');
    $host     = env('DB_HOST', '127.0.0.1');

    $command = sprintf(
        'mysql -u%s -p%s -h%s %s < "%s"',
        $username,
        $password,
        $host,
        $database,
        $sqlPath
    );

    Log::info('تشغيل أمر استرجاع: ' . $command); // أضفنا لوج هنا

    exec($command, $output, $result);

    if ($result === 0) {
        return response()->json(['status' => 'restored']);
    } else {
        Log::error('فشل تنفيذ الاستعادة:', ['output' => $output, 'result' => $result]);
        return response()->json(['status' => 'failed', 'error' => 'فشل تنفيذ الاستعادة.']);
    }
}







}
