<?php
$content = file_get_contents('resources/views/admin/monitoring/show.blade.php');

$top_section = <<<'EOD'
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.monitoring.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-feather="arrow-left"></i>
    </a>
    <div class="flex-grow-1">
        <h4 class="mb-1"><strong>Monitoring Peserta: {{ $schedule->name }}</strong></h4>
        <small class="text-muted">Total Soal: <strong>{{ $totalQuestions }}</strong> | Token: <strong>{{ $schedule->token ?? '-' }}</strong></small>
    </div>
    <div class="d-flex gap-2">
        <select name="group_id" class="form-select form-select-sm" style="min-width: 150px;">
            <option value="">Semua Kelas</option>
            @foreach($groups as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
            @endforeach
        </select>
        <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-1" id="btn-refresh">
            <i data-feather="refresh-cw" style="width: 14px; height: 14px;"></i> Refresh Status
        </button>
    </div>
</div>
EOD;

$content = preg_replace('/<div class="d-flex align-items-center gap-2 mb-3">.*?<\/div>\s*<\/div>/s', $top_section, $content);

$table_body = <<<'EOD'
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">NO</th>
                                <th>NO. PESERTA</th>
                                <th>NAMA SISWA</th>
                                <th>GRUP/KELAS</th>
                                <th class="text-center">STATUS</th>
                                <th width="120" class="text-center">PROGRES</th>
                                <th class="text-center">SKOR</th>
                                <th width="120" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th { border-top: none; font-size: 0.82rem; }
    .table tbody td { font-size: 0.82rem; }
    .badge { font-weight: 500; font-size: 0.70rem; padding: 3px 8px; }
    .progress { background-color: #e9ecef; border-radius: 10px; }
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let table = $('#tableMonitoring').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.monitoring.show', $schedule->id) }}",
            data: function(d) {
                d.group_id = $('select[name="group_id"]').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center align-middle'},
            {data: 'nomer_ujian', name: 'nomer_ujian', className: 'align-middle'},
            {data: 'name', name: 'name', className: 'align-middle fw-bold'},
            {data: 'group_name', name: 'group.name', className: 'align-middle'},
            {data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center align-middle'},
            {data: 'progress', name: 'progress', orderable: false, searchable: false, className: 'align-middle'},
            {data: 'score', name: 'score', orderable: false, searchable: false, className: 'text-center align-middle'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'align-middle text-center'}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
        }
    });

    $('select[name="group_id"]').on('change', function() {
        table.ajax.reload();
    });

    $('#btn-refresh').on('click', function() {
        table.ajax.reload(null, false);
    });

    // Auto refresh every 30 seconds
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);
});
</script>
@endpush
EOD;

$content = preg_replace('/<thead class="bg-light">.*@endpush/s', $table_body, $content);
file_put_contents('resources/views/admin/monitoring/show.blade.php', $content);
echo "Done";

