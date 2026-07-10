<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->getBackups();
        return view('admin.backups.index', compact('backups'));
    }

    public function create()
    {
        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';

        $path = database_path('backups');
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $fullPath = $path . '/' . $filename;

        // For SQLite, just copy the database file
        $dbPath = database_path('database.sqlite');
        if (File::exists($dbPath)) {
            File::copy($dbPath, $fullPath);
        } else {
            // For MySQL, try to use mysqldump
            $host = config('database.connections.mysql.host', '127.0.0.1');
            $port = config('database.connections.mysql.port', '3306');
            $database = config('database.connections.mysql.database', 'phone_shop');
            $username = config('database.connections.mysql.username', 'root');
            $password = config('database.connections.mysql.password', '');

            $command = "mysqldump --host={$host} --port={$port} --user={$username}";
            if ($password) {
                $command .= " --password={$password}";
            }
            $command .= " {$database} > \"{$fullPath}\"";

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                return back()->with('error', 'Backup failed. mysqldump returned error code: ' . $returnVar);
            }
        }

        return back()->with('success', "Backup created: {$filename}");
    }

    public function download($filename)
    {
        $path = database_path('backups/' . $filename);

        if (!File::exists($path)) {
            return back()->with('error', 'Backup file not found.');
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    public function delete($filename)
    {
        $path = database_path('backups/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
        }

        return back()->with('success', 'Backup deleted successfully.');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        $file = $request->file('backup_file');
        $filename = $file->getClientOriginalName();

        $path = database_path('backups');
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $fullPath = $path . '/' . $filename;
        $file->move($path, $filename);

        // For SQLite, restore by copying
        $dbPath = database_path('database.sqlite');
        if (File::exists($dbPath)) {
            File::copy($fullPath, $dbPath);
        }

        return redirect()->route('admin.backups.index')->with('success', 'Database restored from ' . $filename . '. Please log in again.');
    }

    private function getBackups(): array
    {
        $path = database_path('backups');
        if (!File::isDirectory($path)) {
            return [];
        }

        $files = File::files($path);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $file->getSize(),
                'date' => $file->getMTime(),
            ];
        }

        usort($backups, fn ($a, $b) => $b['date'] - $a['date']);

        return $backups;
    }
}
