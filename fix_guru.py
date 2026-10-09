import os

create_path = 'resources/views/guru/exam-schedule/create.blade.php'
if os.path.exists(create_path):
    with open(create_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    target = '''<div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Token (Opsional)</label>
                            <input type="text" name="token" class="form-control" placeholder="Kosongkan jika tidak pakai token">
                        </div>'''
    replacement = '''<div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Token & Interval</label>
                            <div class="input-group">
                                <input type="text" name="token" class="form-control" placeholder="Token statis">
                                <input type="number" name="token_interval" class="form-control" placeholder="Ubah tiap (menit)">
                            </div>
                            <small class="text-muted mt-1 d-block">Isi kolom kedua dengan angka (misal: 15) untuk token dinamis.</small>
                        </div>'''
    if target in content:
        content = content.replace(target, replacement)
        with open(create_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print('Guru Create updated')

edit_path = 'resources/views/guru/exam-schedule/edit.blade.php'
if os.path.exists(edit_path):
    with open(edit_path, 'r', encoding='utf-8') as f:
        content2 = f.read()
    
    target2 = '''<div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Token (Opsional)</label>
                            <input type="text" name="token" class="form-control" value="{{ $examSchedule->token }}" placeholder="Kosongkan jika tidak pakai token">
                        </div>'''
    replacement2 = '''<div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Token & Interval</label>
                            <div class="input-group">
                                <input type="text" name="token" class="form-control" value="{{ $examSchedule->token }}" placeholder="Token statis">
                                <input type="number" name="token_interval" class="form-control" value="{{ $examSchedule->token_interval }}" placeholder="Ubah tiap (menit)">
                            </div>
                            <small class="text-muted mt-1 d-block">Isi kolom kedua dengan angka (misal: 15) untuk token dinamis.</small>
                        </div>'''
    if target2 in content2:
        content2 = content2.replace(target2, replacement2)
        with open(edit_path, 'w', encoding='utf-8') as f:
            f.write(content2)
        print('Guru Edit updated')
