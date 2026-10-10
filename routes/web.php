<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\GuruLoginController;
use App\Http\Controllers\Auth\SiswaLoginController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ReligionController;
use App\Http\Controllers\Admin\BankSoalController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\ExamEventController;
use App\Http\Controllers\Admin\ExamScheduleController;
use App\Http\Controllers\Admin\ExamSessionController;
use App\Http\Controllers\Admin\ExamScheduleParticipantController;
use App\Http\Controllers\Admin\ExamStatusController;
use App\Http\Controllers\Admin\ExamMonitoringController;
use App\Http\Controllers\Admin\ExamProcessingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ExamController as SiswaExamController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\BankSoalController as GuruBankSoalController;
use App\Http\Controllers\Guru\QuestionController as GuruQuestionController;
use App\Http\Controllers\Guru\ProcessingController as GuruProcessingController;
use App\Http\Controllers\Guru\ExamScheduleController as GuruExamScheduleController;
use App\Http\Controllers\Guru\ProfileController as GuruProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('siswa.login');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });
    Route::get('/login', [AdminLoginController::class , 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class , 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminLoginController::class , 'logout'])->name('admin.logout');

    Route::middleware('auth:admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class , 'index'])->name('admin.dashboard');

            // Setting Routes
            Route::get('/settings', [SettingController::class , 'index'])->name('admin.settings.index');
            Route::post('/settings', [SettingController::class , 'store'])->name('admin.settings.store');
            Route::get('/settings/backup', [SettingController::class , 'backup'])->name('admin.settings.backup');
            Route::post('/settings/restore', [SettingController::class , 'restore'])->name('admin.settings.restore');

            // CRUD Data Master Sub Menu
            Route::resource('peserta', SiswaController::class)->except(['show'])->names([
                'index' => 'admin.peserta.index',
                'create' => 'admin.peserta.create',
                'store' => 'admin.peserta.store',
                'edit' => 'admin.peserta.edit',
                'update' => 'admin.peserta.update',
                'destroy' => 'admin.peserta.destroy',
            ]);
            Route::post('peserta/{id}/reset-login', [SiswaController::class , 'resetLogin'])->name('admin.peserta.reset-login');
            Route::get('peserta/export-template', [SiswaController::class , 'exportTemplate'])->name('admin.peserta.export-template');
            Route::post('peserta/import', [SiswaController::class , 'import'])->name('admin.peserta.import');
            Route::get('peserta/card-preview', [SiswaController::class , 'cardPreview'])->name('admin.peserta.card-preview');
            Route::get('peserta/print-cards', [SiswaController::class , 'printCards'])->name('admin.peserta.print-cards');

            Route::resource('group', GroupController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])->names([
                'index' => 'admin.group.index',
                'create' => 'admin.group.create',
                'store' => 'admin.group.store',
                'edit' => 'admin.group.edit',
                'update' => 'admin.group.update',
                'destroy' => 'admin.group.destroy',
            ]);
            Route::get('group/{id}', [GroupController::class , 'show'])->name('admin.group.show');
            Route::post('group/{id}/member', [GroupController::class , 'storeMember'])->name('admin.group.member.store');
            Route::delete('group/{groupId}/member/{siswaId}', [GroupController::class , 'destroyMember'])->name('admin.group.member.destroy');

            Route::resource('jurusan', JurusanController::class)->except(['show'])->names([
                'index' => 'admin.jurusan.index',
                'create' => 'admin.jurusan.create',
                'store' => 'admin.jurusan.store',
                'edit' => 'admin.jurusan.edit',
                'update' => 'admin.jurusan.update',
                'destroy' => 'admin.jurusan.destroy',
            ]);

                        Route::post('subject/import', [SubjectController::class, 'import'])->name('admin.subject.import');
            Route::get('subject/template', [SubjectController::class, 'downloadTemplate'])->name('admin.subject.template');
            Route::resource('subject', SubjectController::class)->except(['show'])->names([
                'index' => 'admin.subject.index',
                'create' => 'admin.subject.create',
                'store' => 'admin.subject.store',
                'edit' => 'admin.subject.edit',
                'update' => 'admin.subject.update',
                'destroy' => 'admin.subject.destroy',
            ]);

                        // Guru Import
            Route::get('guru/import', [GuruController::class, 'importView'])->name('admin.guru.import.view');
            Route::post('guru/import', [GuruController::class, 'import'])->name('admin.guru.import');
            Route::get('guru/template', [GuruController::class, 'downloadTemplate'])->name('admin.guru.template');

            // Guru Management
            Route::resource('guru', GuruController::class)->except(['show'])->names([
                'index' => 'admin.guru.index',
                'create' => 'admin.guru.create',
                'store' => 'admin.guru.store',
                'edit' => 'admin.guru.edit',
                'update' => 'admin.guru.update',
                'destroy' => 'admin.guru.destroy',
            ]);
            Route::get('guru/{id}/subjects', [GuruController::class , 'subjects'])->name('admin.guru.subjects');
            Route::post('guru/{id}/subjects/sync', [GuruController::class , 'syncSubjects'])->name('admin.guru.subjects.sync');

            Route::resource('user', AdminUserController::class)->except(['show'])->names([
                'index' => 'admin.user.index',
                'create' => 'admin.user.create',
                'store' => 'admin.user.store',
                'edit' => 'admin.user.edit',
                'update' => 'admin.user.update',
                'destroy' => 'admin.user.destroy',
            ]);

            Route::resource('religion', ReligionController::class)->except(['show'])->names([
                'index' => 'admin.religion.index',
                'create' => 'admin.religion.create',
                'store' => 'admin.religion.store',
                'edit' => 'admin.religion.edit',
                'update' => 'admin.religion.update',
                'destroy' => 'admin.religion.destroy',
            ]);

            // Bank Soal Routes
            Route::get('bank-soal/download-template-word', [BankSoalController::class , 'downloadTemplateWord'])->name('admin.bank-soal.download-template-word');
            Route::resource('exam-session', ExamSessionController::class)->names('admin.exam-session');
            Route::resource('bank-soal', BankSoalController::class)->names([
                'index' => 'admin.bank-soal.index',
                'create' => 'admin.bank-soal.create',
                'store' => 'admin.bank-soal.store',
                'edit' => 'admin.bank-soal.edit',
                'update' => 'admin.bank-soal.update',
                'destroy' => 'admin.bank-soal.destroy',
            ]);
            Route::get('bank-soal/{id}/show', [BankSoalController::class , 'show'])->name('admin.bank-soal.show');
            Route::patch('bank-soal/{id}/toggle-status', [BankSoalController::class , 'toggleStatus'])->name('admin.bank-soal.toggle-status');
            Route::get('bank-soal/{bankSoal}/import-word', [BankSoalController::class , 'importWord'])->name('admin.bank-soal.import-word');
            Route::post('bank-soal/{bankSoal}/process-import-word', [BankSoalController::class , 'processImportWord'])->name('admin.bank-soal.process-import-word');
            Route::resource('exam-event', ExamEventController::class)->names('admin.exam-event');
            Route::resource('exam-schedule', ExamScheduleController::class)->names('admin.exam-schedule');
            Route::get('exam-schedule/{schedule}/participants', [ExamScheduleParticipantController::class , 'show'])->name('admin.exam-schedule.participants');
            Route::post('exam-schedule/{schedule}/participants/sync', [ExamScheduleParticipantController::class , 'syncGroups'])->name('admin.exam-schedule.participants.sync');

            // Status Ujian & Rilis Token
            Route::get('exam-status', [ExamStatusController::class , 'index'])->name('admin.exam-status.index');
            Route::post('exam-status/{schedule}/release-token', [ExamStatusController::class , 'releaseToken'])->name('admin.exam-status.release-token');
            Route::post('exam-status/{schedule}/clear-token', [ExamStatusController::class , 'clearToken'])->name('admin.exam-status.clear-token');
            Route::post('exam-status/{schedule}/update-interval', [ExamStatusController::class , 'updateInterval'])->name('admin.exam-status.update-interval');

            // Monitoring (Status Peserta)
                        Route::get('monitoring', [ExamMonitoringController::class, 'index'])->name('admin.monitoring.index');
            Route::get('berita-acara', [\App\Http\Controllers\Admin\BeritaAcaraController::class, 'index'])->name('admin.berita-acara.index');
            Route::post('berita-acara/{schedule}/store', [\App\Http\Controllers\Admin\BeritaAcaraController::class, 'store'])->name('admin.berita-acara.store');
            Route::get('berita-acara/{schedule}/cetak-pdf', [\App\Http\Controllers\Admin\BeritaAcaraController::class, 'cetakPdf'])->name('admin.berita-acara.cetak-pdf');
            Route::get('monitoring/{schedule}', [ExamMonitoringController::class, 'show'])->name('admin.monitoring.show');
                        Route::post('monitoring/{schedule}/{siswa}/force-finish', [ExamMonitoringController::class, 'forceFinish'])->name('admin.monitoring.force-finish');
            Route::post('monitoring/{schedule}/{siswa}/reset', [ExamMonitoringController::class , 'resetExam'])->name('admin.monitoring.reset');
            Route::post('monitoring/{schedule}/{siswa}/unblock', [ExamMonitoringController::class , 'unblock'])->name('admin.monitoring.unblock');

            // Pengolahan
            Route::get('pengolahan/koreksi', [ExamProcessingController::class , 'koreksiIndex'])->name('admin.processing.koreksi.index');
            Route::get('pengolahan/koreksi/{schedule}', [ExamProcessingController::class , 'koreksiShow'])->name('admin.processing.koreksi.show');
            Route::get('pengolahan/koreksi/{schedule}/siswa/{siswa}', [ExamProcessingController::class , 'koreksiSiswa'])->name('admin.processing.koreksi.siswa');
            Route::post('pengolahan/koreksi/{schedule}/siswa/{siswa}', [ExamProcessingController::class , 'koreksiStore'])->name('admin.processing.koreksi.store');
            Route::get('pengolahan/hasil', [ExamProcessingController::class , 'hasilIndex'])->name('admin.processing.hasil.index');
            Route::get('pengolahan/hasil/{schedule}', [ExamProcessingController::class , 'hasilShow'])->name('admin.processing.hasil.show');
            Route::delete('pengolahan/hasil/{id}', [ExamProcessingController::class , 'hasilDestroy'])->name('admin.processing.hasil.destroy');
            Route::post('pengolahan/hasil/{id}/reset', [ExamProcessingController::class , 'hasilReset'])->name('admin.processing.hasil.reset');
            Route::get('pengolahan/hasil/{id}/export-excel', [ExamProcessingController::class , 'hasilExportExcel'])->name('admin.processing.hasil.export-excel');
            Route::get('pengolahan/leger', [ExamProcessingController::class , 'legerIndex'])->name('admin.processing.leger.index');
            Route::get('pengolahan/leger/export-excel', [ExamProcessingController::class , 'legerExportExcel'])->name('admin.processing.leger.export-excel');
            Route::get('pengolahan/leger/export-pdf', [ExamProcessingController::class , 'legerExportPdf'])->name('admin.processing.leger.export-pdf');
            Route::get('pengolahan/analisis', [ExamProcessingController::class , 'analisisIndex'])->name('admin.processing.analisis.index');
            Route::get('pengolahan/analisis/{schedule}', [ExamProcessingController::class , 'analisisShow'])->name('admin.processing.analisis.show');
            Route::get('pengolahan/analisis/{id}/export-excel', [ExamProcessingController::class , 'analisisExportExcel'])->name('admin.processing.analisis.export-excel');
            Route::get('pengolahan/setting', [ExamProcessingController::class , 'settingIndex'])->name('admin.processing.setting.index');
            Route::post('pengolahan/setting/{id}', [ExamProcessingController::class , 'settingUpdate'])->name('admin.processing.setting.update');


            // Question Management within Bank Soal
            Route::get('bank-soal/{bankSoal}/question/create', [QuestionController::class , 'create'])->name('admin.question.create');
            Route::post('bank-soal/{bankSoal}/question', [QuestionController::class , 'store'])->name('admin.question.store');
            Route::post('question/upload-image', [QuestionController::class , 'uploadImage'])->name('admin.question.upload-image');
            Route::get('question/{question}/show', [QuestionController::class , 'show'])->name('admin.question.show');
            Route::get('question/{question}/edit', [QuestionController::class , 'edit'])->name('admin.question.edit');
            Route::put('question/{question}', [QuestionController::class , 'update'])->name('admin.question.update');
            Route::delete('question/{question}', [QuestionController::class , 'destroy'])->name('admin.question.destroy');

            // Profile Routes
            Route::get('/profile', [ProfileController::class , 'edit'])->name('admin.profile.edit');
            Route::post('/profile', [ProfileController::class , 'update'])->name('admin.profile.update');
        }
        );
    });


// Guru Routes
Route::prefix('guru')->group(function () {
    Route::get('/', function () {
        return redirect()->route('guru.login');
    });
    Route::get('/login', [GuruLoginController::class , 'showLoginForm'])->name('guru.login');
    Route::post('/login', [GuruLoginController::class , 'login'])->name('guru.login.submit');
    Route::post('/logout', [GuruLoginController::class , 'logout'])->name('guru.logout');

    Route::middleware('auth:guru')->group(function () {
            // Dashboard
            Route::get('/dashboard', [GuruDashboardController::class , 'index'])->name('guru.dashboard');

            // Bank Soal (hanya mapel yang diampu)
            Route::resource('bank-soal', GuruBankSoalController::class)->except(['show'])->names([
                'index' => 'guru.bank-soal.index',
                'create' => 'guru.bank-soal.create',
                'store' => 'guru.bank-soal.store',
                'edit' => 'guru.bank-soal.edit',
                'update' => 'guru.bank-soal.update',
                'destroy' => 'guru.bank-soal.destroy',
            ]);
            Route::get('bank-soal/{id}/show', [GuruBankSoalController::class , 'show'])->name('guru.bank-soal.show');
            Route::get('bank-soal/download-template-word', [GuruBankSoalController::class , 'downloadTemplateWord'])->name('guru.bank-soal.download-template-word');
            Route::get('bank-soal/{bankSoal}/import-word', [GuruBankSoalController::class , 'importWord'])->name('guru.bank-soal.import-word');
            Route::post('bank-soal/{bankSoal}/process-import-word', [GuruBankSoalController::class , 'processImportWord'])->name('guru.bank-soal.process-import-word');

            // Question Management dalam Bank Soal
            Route::post('question/upload-image', [GuruQuestionController::class , 'uploadImage'])->name('guru.question.upload-image');
            Route::get('bank-soal/{bankSoal}/question/create', [GuruQuestionController::class , 'create'])->name('guru.question.create');
            Route::post('bank-soal/{bankSoal}/question', [GuruQuestionController::class , 'store'])->name('guru.question.store');
            Route::get('question/{question}/show', [GuruQuestionController::class , 'show'])->name('guru.question.show');
            Route::get('question/{question}/edit', [GuruQuestionController::class , 'edit'])->name('guru.question.edit');
            Route::put('question/{question}', [GuruQuestionController::class , 'update'])->name('guru.question.update');
            Route::delete('question/{question}', [GuruQuestionController::class , 'destroy'])->name('guru.question.destroy');

            // Event dan Jadwal Ujian (CRUD untuk mapel yang diampu)
            Route::resource('exam-event', \App\Http\Controllers\Guru\ExamEventController::class)->names('guru.exam-event');
            Route::resource('exam-schedule', GuruExamScheduleController::class)->names('guru.exam-schedule');

            // Pengolahan Nilai
            Route::get('processing/koreksi', [GuruProcessingController::class , 'koreksiIndex'])->name('guru.processing.koreksi.index');
            Route::get('processing/koreksi/{schedule}', [GuruProcessingController::class , 'koreksiShow'])->name('guru.processing.koreksi.show');
            Route::get('processing/koreksi/{schedule}/siswa/{siswa}', [GuruProcessingController::class , 'koreksiSiswa'])->name('guru.processing.koreksi.siswa');
            Route::post('processing/koreksi/{schedule}/siswa/{siswa}', [GuruProcessingController::class , 'koreksiStore'])->name('guru.processing.koreksi.store');
            Route::get('processing/hasil', [GuruProcessingController::class , 'hasilIndex'])->name('guru.processing.hasil.index');
            Route::get('processing/hasil/{schedule}', [GuruProcessingController::class , 'hasilShow'])->name('guru.processing.hasil.show');
            Route::get('processing/hasil/{id}/export-excel', [GuruProcessingController::class, 'hasilExportExcel'])->name('guru.processing.hasil.export-excel');


            // Analisis & Leger untuk Guru
            Route::get('processing/analisis', [GuruProcessingController::class , 'analisisIndex'])->name('guru.processing.analisis.index');

            // Status Ujian & Rilis Token
            Route::get('exam-status', [\App\Http\Controllers\Guru\ExamStatusController::class , 'index'])->name('guru.exam-status.index');
            Route::post('exam-status/{schedule}/release-token', [\App\Http\Controllers\Guru\ExamStatusController::class , 'releaseToken'])->name('guru.exam-status.release-token');
            Route::post('exam-status/{schedule}/clear-token', [\App\Http\Controllers\Guru\ExamStatusController::class , 'clearToken'])->name('guru.exam-status.clear-token');
            Route::post('exam-status/{schedule}/update-interval', [\App\Http\Controllers\Guru\ExamStatusController::class , 'updateInterval'])->name('guru.exam-status.update-interval');

            // Monitoring (Status Peserta)
                        Route::get('monitoring', [\App\Http\Controllers\Guru\ExamMonitoringController::class, 'index'])->name('guru.monitoring.index');
            Route::get('berita-acara', [\App\Http\Controllers\Guru\BeritaAcaraController::class, 'index'])->name('guru.berita-acara.index');
            Route::post('berita-acara/{schedule}/store', [\App\Http\Controllers\Guru\BeritaAcaraController::class, 'store'])->name('guru.berita-acara.store');
            Route::get('berita-acara/{schedule}/cetak-pdf', [\App\Http\Controllers\Guru\BeritaAcaraController::class, 'cetakPdf'])->name('guru.berita-acara.cetak-pdf');
            Route::get('monitoring/{schedule}', [\App\Http\Controllers\Guru\ExamMonitoringController::class, 'show'])->name('guru.monitoring.show');
                        Route::post('monitoring/{schedule}/{siswa}/force-finish', [\App\Http\Controllers\Guru\ExamMonitoringController::class, 'forceFinish'])->name('guru.monitoring.force-finish');
            Route::post('monitoring/{schedule}/{siswa}/reset', [\App\Http\Controllers\Guru\ExamMonitoringController::class , 'resetExam'])->name('guru.monitoring.reset');
            Route::post('monitoring/{schedule}/{siswa}/unblock', [\App\Http\Controllers\Guru\ExamMonitoringController::class , 'unblock'])->name('guru.monitoring.unblock');
            Route::post('monitoring/{siswa}/reset-login', [\App\Http\Controllers\Guru\ExamMonitoringController::class , 'resetLogin'])->name('guru.monitoring.reset-login');
            Route::get('processing/analisis/{schedule}', [GuruProcessingController::class , 'analisisShow'])->name('guru.processing.analisis.show');
            Route::get('processing/analisis/{id}/export-excel', [GuruProcessingController::class , 'analisisExportExcel'])->name('guru.processing.analisis.export-excel');
            
            Route::get('processing/leger', [GuruProcessingController::class , 'legerIndex'])->name('guru.processing.leger.index');
            Route::get('processing/leger/export-excel', [GuruProcessingController::class , 'legerExportExcel'])->name('guru.processing.leger.export-excel');
            Route::get('processing/leger/export-pdf', [GuruProcessingController::class , 'legerExportPdf'])->name('guru.processing.leger.export-pdf');

            // Profile Routes
            Route::get('/profile', [GuruProfileController::class , 'edit'])->name('guru.profile.edit');
            Route::post('/profile', [GuruProfileController::class , 'update'])->name('guru.profile.update');
        }
        );
    });

// Siswa Routes
Route::prefix('siswa')->group(function () {
    Route::get('/login', [SiswaLoginController::class , 'showLoginForm'])->name('siswa.login');
    Route::post('/login', [SiswaLoginController::class , 'login'])->name('siswa.login.submit');
    Route::post('/logout', [SiswaLoginController::class , 'logout'])->name('siswa.logout');

    Route::middleware('auth:siswa')->group(function () {
            Route::get('/dashboard', [SiswaDashboardController::class , 'index'])->name('siswa.dashboard');
            Route::post('/exam/{schedule}/start', [SiswaExamController::class , 'start'])->name('siswa.exam.start');
            Route::get('/exam/{schedule}/soal/{no}', [SiswaExamController::class , 'show'])->name('siswa.exam.show');
            Route::post('/exam/{schedule}/answer', [SiswaExamController::class , 'answer'])->name('siswa.exam.answer');
            Route::match(['get', 'post'], '/exam/{schedule}/finish', [SiswaExamController::class , 'finish'])->name('siswa.exam.finish');
            Route::post('/exam/{schedule}/block', [SiswaExamController::class , 'block'])->name('siswa.exam.block');
        }
        );
    });


