<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'nama_instansi' => Setting::get('nama_instansi'),
            'alamat' => Setting::get('alamat'),
            'telepon' => Setting::get('telepon'),
            'email' => Setting::get('email'),
            'kepala_sekolah' => Setting::get('kepala_sekolah'),
            'nip_kepala_sekolah' => Setting::get('nip_kepala_sekolah'),
            'jenjang' => Setting::get('jenjang'),
            'status' => Setting::get('status'),
            'logo' => Setting::get('logo'),
            'kop_surat' => Setting::get('kop_surat'),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:255',
            'jenjang' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kop_surat' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        foreach ($data as $key => $value) {
            if ($key == 'logo' && $request->hasFile('logo')) {
                $targetDir = public_path('uploads');
                if (!file_exists($targetDir)) {
                    @mkdir($targetDir, 0775, true);
                }
                $file = $request->file('logo');
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($targetDir, $filename);
                Setting::set('logo', 'uploads/' . $filename);
                continue;
            }
            if ($key == 'kop_surat' && $request->hasFile('kop_surat')) {
                $targetDir = public_path('uploads');
                if (!file_exists($targetDir)) {
                    @mkdir($targetDir, 0775, true);
                }
                $file = $request->file('kop_surat');
                $filename = 'kop_surat_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($targetDir, $filename);
                Setting::set('kop_surat', 'uploads/' . $filename);
                continue;
            }
            Setting::set($key, $value);
        }

        return back()->with('success', 'Informasi instansi berhasil diperbarui.');
    }
    public function backup()
    {
        try {
            $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
            $database = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();

            $date = date('Y-m-d_H-i-s');
            $filename = "backup_{$database}_{$date}.sql";

            $sql = "-- Database Backup: {$database}\n";
            $sql .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n";

            $tablesStmt = $pdo->query('SHOW FULL TABLES WHERE Table_type = \'BASE TABLE\'');
            $tables = $tablesStmt->fetchAll(\PDO::FETCH_NUM);

            foreach ($tables as $tableRow) {
                $table = $tableRow[0];

                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "-- Struktur dari tabel `{$table}`\n";
                $sql .= "-- --------------------------------------------------------\n\n";
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

                $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                $createRow = $createStmt->fetch(\PDO::FETCH_NUM);
                if (!empty($createRow[1])) {
                    $sql .= $createRow[1] . ";\n\n";
                }

                $rowsStmt = $pdo->query("SELECT * FROM `{$table}`");
                $rows = $rowsStmt->fetchAll(\PDO::FETCH_ASSOC);

                if (!empty($rows)) {
                    $columns = array_map(fn($col) => "`{$col}`", array_keys($rows[0]));
                    $colList = implode(', ', $columns);

                    foreach (array_chunk($rows, 100) as $chunk) {
                        $valuesList = [];
                        foreach ($chunk as $row) {
                            $vals = [];
                            foreach ($row as $val) {
                                if (is_null($val)) {
                                    $vals[] = 'NULL';
                                } else {
                                    $vals[] = $pdo->quote((string) $val);
                                }
                            }
                            $valuesList[] = '(' . implode(', ', $vals) . ')';
                        }
                        $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $valuesList) . ";\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            return response($sql, 200, [
                'Content-Type' => 'application/sql; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    public function restore(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file'
        ]);
        
        try {
            $file = $request->file('backup_file');
            
            if (strtolower($file->getClientOriginalExtension()) !== 'sql') {
                return back()->with('error', 'File harus berformat .sql');
            }
            
            $sql = file_get_contents($file->getRealPath());
            
            \Illuminate\Support\Facades\DB::unprepared("SET FOREIGN_KEY_CHECKS=0;\n" . $sql . "\nSET FOREIGN_KEY_CHECKS=1;");
            
            return back()->with('success', 'Database berhasil di-restore.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal me-restore database: ' . $e->getMessage());
        }
    }
}

