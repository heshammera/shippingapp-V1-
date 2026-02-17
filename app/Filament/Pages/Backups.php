<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Backups extends Page
{
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationLabel = 'النسخ الاحتياطي';
    protected static ?string $navigationGroup = '⚙️ الإعدادات والربط';
    protected static ?string $title = 'النسخ الاحتياطي';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.backups';

    public $backups = [];

    public function mount()
    {
        $this->refreshBackups();
    }

    public function refreshBackups()
    {
        $path = storage_path('app/backup');
        
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $this->backups = collect(File::files($path))
            ->filter(fn($file) => $file->getExtension() === 'sql')
            ->map(fn($file) => [
                'name' => $file->getFilename(),
                'size' => $file->getSize(), // Bytes
                'date' => $file->getMTime(),
                'path' => $file->getPathname(),
            ])
            ->sortByDesc('date')
            ->values()
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_backup')
                ->label('إنشاء نسخة احتياطية جديدة')
                ->icon('heroicon-o-plus')
                ->action(function () {
                    $this->createBackup();
                })
                ->color('primary'),
        ];
    }

    public function createBackup()
    {
        try {
            $fileName = 'backup_' . date('Ymd_His') . '.sql';
            $backupPath = storage_path('app/backup/' . $fileName);
            
            // Ensure directory exists
            if (!File::exists(dirname($backupPath))) {
                File::makeDirectory(dirname($backupPath), 0755, true);
            }

            $connection = config('database.default');
            $driver = config("database.connections.{$connection}.driver");

            $username = config("database.connections.{$connection}.username");
            $password = config("database.connections.{$connection}.password");
            $database = config("database.connections.{$connection}.database");
            $host     = config("database.connections.{$connection}.host");
            $port     = config("database.connections.{$connection}.port");

            $output = [];
            $result = null;

            if ($driver === 'pgsql') {
                $port = $port ?: '5432';
                // Set password via environment variable for pg_dump
                putenv("PGPASSWORD={$password}");
                
                $command = sprintf(
                    'pg_dump -U %s -h %s -p %s -d %s > "%s"',
                    escapeshellarg($username),
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($database),
                    $backupPath
                );
            } elseif ($driver === 'mysql') {
                $port = $port ?: '3306';
                $passwordPart = !empty($password) ? "-p" . escapeshellarg($password) : "";
                
                $command = sprintf(
                    'mysqldump -u%s %s -h%s -P%s %s > "%s"',
                    escapeshellarg($username),
                    $passwordPart,
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($database),
                    $backupPath
                );
            } else {
                throw new \Exception("نوع قاعدة البيانات '$driver' غير مدعوم للنسخ الاحتياطي التلقائي حالياً.");
            }

            // Execute command
            // Note: On Windows '2>&1' handles stderr.
            exec($command . ' 2>&1', $output, $result);

            // Verify file creation and size (pg_dump might write empty file on auth fail)
            if ($result === 0 && File::exists($backupPath) && File::size($backupPath) > 0) {
                Notification::make()
                    ->title('تم إنشاء النسخة الاحتياطية بنجاح')
                    ->success()
                    ->send();
                $this->refreshBackups();
            } else {
                Log::error('Backup failed', ['driver' => $driver, 'output' => $output]);
                
                // Friendly error message detection
                $errorMsg = implode("\n", $output);
                if (str_contains($errorMsg, 'is not recognized')) {
                    $tool = ($driver === 'pgsql') ? 'pg_dump' : 'mysqldump';
                    $errorMsg = "الأداة '$tool' غير موجودة في النظام. يرجى تثبيتها وإضافتها لمسار النظام (Path).";
                }

                Notification::make()
                    ->title('فشل إنشاء النسخة الاحتياطية')
                    ->body($errorMsg)
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('حدث خطأ غير متوقع')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function restoreBackup($fileName)
    {
        $path = storage_path('app/backup/' . $fileName);
        
        if (!File::exists($path)) {
             Notification::make()
                ->title('ملف النسخة الاحتياطية غير موجود')
                ->danger()
                ->send();
             return;
        }

        try {
            $connection = config('database.default');
            $driver = config("database.connections.{$connection}.driver");

            $username = config("database.connections.{$connection}.username");
            $password = config("database.connections.{$connection}.password");
            $database = config("database.connections.{$connection}.database");
            $host     = config("database.connections.{$connection}.host");
            $port     = config("database.connections.{$connection}.port");

            $output = [];
            $result = null;

            if ($driver === 'pgsql') {
                $port = $port ?: '5432';
                putenv("PGPASSWORD={$password}");
                
                // psql is used for restoring .sql files generated by pg_dump (non-binary)
                $command = sprintf(
                    'psql -U %s -h %s -p %s -d %s < "%s"',
                    escapeshellarg($username),
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($database),
                    $path
                );
            } elseif ($driver === 'mysql') {
                $port = $port ?: '3306';
                $passwordPart = !empty($password) ? "-p" . escapeshellarg($password) : "";
                
                $command = sprintf(
                    'mysql -u%s %s -h%s -P%s %s < "%s"',
                    escapeshellarg($username),
                    $passwordPart,
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($database),
                    $path
                );
            } else {
                throw new \Exception("نوع قاعدة البيانات '$driver' غير مدعوم للاستعادة.");
            }

            exec($command . ' 2>&1', $output, $result);

            if ($result === 0) {
                Notification::make()
                    ->title('تم استعادة النسخة الاحتياطية بنجاح')
                    ->success()
                    ->send();
            } else {
                Log::error('Restore failed', ['driver' => $driver, 'output' => $output]);
                $errorMsg = implode("\n", $output);
                 if (str_contains($errorMsg, 'is not recognized')) {
                    $tool = ($driver === 'pgsql') ? 'psql' : 'mysql';
                    $errorMsg = "الأداة '$tool' غير موجودة في النظام.";
                }

                Notification::make()
                    ->title('فشل عملية الاستعادة')
                    ->body($errorMsg)
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
             Notification::make()
                ->title('حدث خطأ')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // Helper to format bytes
    public function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
