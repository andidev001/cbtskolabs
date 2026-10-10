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
                $file = $request->file('logo');
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);
                Setting::set('logo', 'uploads/' . $filename);
                continue;
            }
            if ($key == 'kop_surat' && $request->hasFile('kop_surat')) {
                $file = $request->file('kop_surat');
                $filename = 'kop_surat_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);
                Setting::set('kop_surat', 'uploads/' . $filename);
                continue;
            }
            Setting::set($key, $value);
        }

        return back()->with('success', 'Informasi instansi berhasil diperbarui.');
    }
    public function backup()
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');
        
        $date = date('Y-m-d_H-i-s');
        $filename = "backup_{$database}_{$date}.sql";
        $path = storage_path("app/public/{$filename}");
        
        $passArg = $password ? "-p\"$password\"" : "";
        $command = "mysqldump --user=\"$username\" $passArg --host=\"$host\" \"$database\" > \"$path\"";
        
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0 && file_exists($path)) {
            return response()->download($path)->deleteFileAfterSend(true);
        }
        
        return back()->with('error', 'Gagal membuat backup database. Pastikan mysqldump tersedia di server.');
    }

    public function restore(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file'
        ]);
        
        try {
            $file = $request->file('backup_file');
            
            if ($file->getClientOriginalExtension() !== 'sql') {
                return back()->with('error', 'File harus berformat .sql');
            }
            
            $sql = file_get_contents($file->getRealPath());
            
            \Illuminate\Support\Facades\DB::unprepared($sql);
            
            return back()->with('success', 'Database berhasil di-restore.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal me-restore database: ' . $e->getMessage());
        }
    }
}

