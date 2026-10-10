<?php
$files = [
    'resources/views/guru/monitoring/show.blade.php',
    'resources/views/admin/monitoring/show.blade.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);

    $btnHtml = <<<HTML
                        <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalBeritaAcara">
                            <i data-feather="printer" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Berita Acara
                        </button>
                        <button class="btn btn-primary btn-sm" id="btnRefresh">
HTML;
    $content = preg_replace('/<button class="btn btn-primary btn-sm" id="btnRefresh">/', $btnHtml, $content);

    $prefix = strpos($file, 'guru') !== false ? 'guru' : 'admin';
    $modalHtml = <<<HTML
<!-- Modal Berita Acara -->
<div class="modal fade" id="modalBeritaAcara" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cetak Berita Acara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('$prefix.monitoring.berita-acara', \$schedule->id) }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ruang Ujian</label>
                        <input type="text" name="ruang" class="form-control" placeholder="Contoh: Ruang 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sesi Ujian</label>
                        <input type="text" name="sesi" class="form-control" placeholder="Contoh: Sesi 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Proktor</label>
                        <input type="text" name="proktor" class="form-control" placeholder="Nama Proktor" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pengawas</label>
                        <input type="text" name="pengawas" class="form-control" placeholder="Nama Pengawas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Selama Ujian</label>
                        <textarea name="catatan" class="form-control" rows="3">Ujian berjalan dengan tertib dan lancar.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Cetak Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
HTML;
    $content = preg_replace('/@push\(\'scripts\'\)/', $modalHtml, $content);

    file_put_contents($file, $content);
    echo "$file updated.\n";
}
