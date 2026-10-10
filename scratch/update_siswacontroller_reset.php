<?php
$file = 'app/Http/Controllers/Admin/SiswaController.php';
$content = file_get_contents($file);

$method = <<<'EOD'
    public function resetLogin($id)
    {
        $siswa = \App\Models\Siswa::findOrFail($id);
        $siswa->update([
            'is_logged_in' => false,
            'session_id' => null
        ]);
        return redirect()->back()->with('success', 'Status login siswa berhasil direset.');
    }
}
EOD;

$content = preg_replace('/}\s*$/', "\n" . $method, $content);
file_put_contents($file, $content);
echo "Added resetLogin.\n";

