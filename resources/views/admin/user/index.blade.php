@extends('layouts.admin')

@section('title', 'Data User Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Data User Admin</h1>
    <a href="{{ route('admin.user.create') }}" class="btn btn-primary">
        <i class="align-middle" data-feather="plus"></i> Tambah User
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover mb-0 w-100" id="user-table">
                <thead class="bg-light">
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    $('#user-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.user.index') }}",
        columns: [
            {data: 'name', name: 'name'},
            {data: 'username', name: 'username'},
            {data: 'email', name: 'email'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
        }
    });
});
</script>
@endpush
@endsection
