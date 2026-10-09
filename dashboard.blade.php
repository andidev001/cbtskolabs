Created At: 2026-10-06T21:50:34+07:00
Completed At: 2026-10-06T21:50:34+07:00
File Path: `file:///c:/laragon/www/digti-cbt/resources/views/siswa/dashboard.blade.php`
Total Lines: 575
Total Bytes: 25362
Showing lines 408 to 450
The following code has been modified to include a line number before every line, in the format: <line_number>: <original_line>. Please note that any changes targeting the original code should remove the line number, colon, and leading space.
408:         {{-- SEDANG BERLANGSUNG --}}
409:         @if($ongoing->count() > 0)
410:         <div class="section-header">
411:             <h2>Ujian Berlangsung</h2>
412:             <div class="line"></div>
413:         </div>
414:         @foreach($ongoing as $exam)
415:         <div class="exam-card ongoing">
416:             <div class="card-status-indicator" {!! $isBlocked ? 'style="background:#fee2e2;"' : '' !!}></div>
417:             <div class="card-body-content">
418:                 <span class="exam-tag tag-ongoing">🔴 Sedang Berlangsung</span>
419:                 <div class="exam-title">{{ $exam->name }}</div>
420:                 <div class="exam-event">
421:                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
422:                     {{ $exam->examEvent->name }}
423:                 </div>
424:                 <div class="exam-details">
425:                     <div class="detail-item">
426:                         <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
427:                         <b>{{ $exam->duration }} Menit</b>
428:                     </div>
429:                     <div class="detail-item">
430:                         <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
431:                         Hingga <b>{{ $exam->end_at->format('H:i') }} WIB</b>
432:                     </div>
433:                 </div>
434:             </div>
435:             <div class="card-action-box">
436:                 @if($exam->token)
437:                     <input type="text" id="token_{{ $exam->id }}" class="token-input" placeholder="Masukkan Token" autocomplete="off">
438:                 @endif
439:                 <form action="{{ route('siswa.exam.start', $exam->id) }}" method="POST" id="form_start_{{ $exam->id }}" style="width: 100%;">
440:                     @csrf
441:                     <input type="hidden" name="token" id="hidden_token_{{ $exam->id }}">
442:                     <button type="submit" class="btm-mulai" onclick="return checkToken({{ $exam->id }}, '{{ $exam->token }}', this)">
443:                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
444:                         Mulai Ujian
445:                     </button>
446:                 </form>
447:             </div>
448:         </div>
449:         @endforeach
450:         @endif
The above content does NOT show the entire file contents. If you need to view any lines of the file which were not shown to complete your task, call this tool again to view those lines.
