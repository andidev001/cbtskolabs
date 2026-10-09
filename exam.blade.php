Created At: 2026-10-06T18:14:14+07:00
Completed At: 2026-10-06T18:14:14+07:00
File Path: `file:///c:/laragon/www/digti-cbt/resources/views/siswa/exam.blade.php`
Total Lines: 832
Total Bytes: 32688
Showing lines 780 to 800
The following code has been modified to include a line number before every line, in the format: <line_number>: <original_line>. Please note that any changes targeting the original code should remove the line number, colon, and leading space.
780:         if (isSaving) {
781:             setTimeout(confirmFinish, 500);
782:             return;
783:         }
784:         
785:         const answered = document.querySelectorAll('.num-btn.answered').length;
786:         const flagged = document.querySelectorAll('.num-btn.flagged').length;
787:         const total = totalSoal;
788:         
789:         const summary = document.getElementById('finishSummary');
790:         summary.innerHTML = `
791:             <div class="d-flex justify-content-between mb-2"><span>Total Soal:</span> <b>${total}</b></div>
792:             <div class="d-flex justify-content-between mb-2"><span>Sudah Dijawab:</span> <b class="text-success">${answered}</b></div>
793:             <div class="d-flex justify-content-between mb-2"><span>Ragu-ragu:</span> <b class="text-warning">${flagged}</b></div>
794:             <div class="d-flex justify-content-between"><span>Belum Dijawab:</span> <b class="text-danger">${total - answered - flagged}</b></div>
795:         `;
796:         document.getElementById('finishModal').classList.add('show');
797:     }
798: 
799:     // â”€â”€ Anti-Cheat Logic â”€â”€
800:     @if($schedule->enforce_focus)
The above content does NOT show the entire file contents. If you need to view any lines of the file which were not shown to complete your task, call this tool again to view those lines.
