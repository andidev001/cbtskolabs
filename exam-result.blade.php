Created At: 2026-10-06T17:38:14+07:00
Completed At: 2026-10-06T17:38:14+07:00
File Path: `file:///c:/laragon/www/digti-cbt/resources/views/siswa/exam-result.blade.php`
Total Lines: 130
Total Bytes: 5032
Showing lines 1 to 130
The following code has been modified to include a line number before every line, in the format: <line_number>: <original_line>. Please note that any changes targeting the original code should remove the line number, colon, and leading space.
1: <!DOCTYPE html>
2: <html lang="id">
3: <head>
4:     <meta charset="utf-8">
5:     <meta name="viewport" content="width=device-width, initial-scale=1">
6:     <title>Hasil Ujian | DIGTI CBT</title>
7:     <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
8:     <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
9:     <style>
10:         * { font-family: 'Inter', sans-serif; }
11:         body { background: linear-gradient(135deg, #0f0e1a 0%, #1a1040 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 30px 16px; }
12:         .result-card {
13:             background: rgba(255,255,255,0.04);
14:             border: 1px solid rgba(255,255,255,0.1);
15:             border-radius: 20px;
16:             padding: 40px;
17:             max-width: 460px;
18:             width: 100%;
19:             text-align: center;
20:             backdrop-filter: blur(10px);
21:         }
22:         .score-ring {
23:             width: 140px; height: 140px;
24:             border-radius: 50%;
25:             display: flex; align-items: center; justify-content: center;
26:             flex-direction: column;
27:             margin: 0 auto 24px;
28:             position: relative;
29:         }
30:         .score-ring::before {
31:             content: '';
32:             position: absolute; inset: 0;
33:             border-radius: 50%;
34:             background: conic-gradient(
35:                 var(--score-color) calc(var(--pct) * 1%), rgba(255,255,255,0.06) 0%
36:             );
37:         }
38:         .score-ring::after {
39:             content: '';
40:             position: absolute; inset: 16px;
41:             border-radius: 50%;
42:             background: #1a1040;
43:         }
44:         .score-inner { position: relative; z-index: 1; }
45:         .score-num { font-size: 2.2rem; font-weight: 800; line-height: 1; }
46:         .score-label { font-size: .7rem; color: #9ca3af; margin-top: 2px; }
47:         h2 { color: #fff; font-weight: 700; }
48:         .stat-row { display: flex; gap: 12px; margin: 20px 0; }
49:         .stat-item {
50:             flex: 1;
51:             background: rgba(255,255,255,0.06);
52:             border: 1px solid rgba(255,255,255,0.08);
53:             border-radius: 12px;
54:             padding: 14px 10px;
55:         }
56:         .stat-item .num { font-size: 1.4rem; font-weight: 700; }
57:         .stat-item .lbl { font-size: .7rem; color: #9ca3af; margin-top: 2px; }
58:         .btn-back {
59:             display: inline-block;
60:             background: linear-gradient(135deg, #7c3aed, #a855f7);
61:             color: #fff; border: none; border-radius: 10px;
62:             padding: 12px 32px; font-weight: 700; font-size: .9rem;
63:             text-decoration: none; transition: opacity .2s; margin-top: 8px;
64:         }
65:         .btn-back:hover { color: #fff; opacity: .85; }
66:         p.sub { color: #9ca3af; font-size: .85rem; }
67:     </style>
68: </head>
69: <body>
70: <div class="result-card">
71:     @php
72:         $pct = $score;
73:         $color = $score >= 70 ? '#34d399' : ($score >= 50 ? '#f59e0b' : '#ef4444');
74:         $label = $score >= 70 ? '🎉 Lulus!' : ($score >= 50 ? '😐 Cukup' : '😞 Perlu Belajar Lagi');
75:     @endphp
76: 
77:     @if($schedule->show_score)
78:     <div class="score-ring" style="--pct: {{ $pct }}; --score-color: {{ $color }};">
79:         <div class="score-inner">
80:             <div class="score-num" style="color: {{ $color }};">
81:                 {{ $score }}
82:             </div>
83:             <div class="score-label">NILAI</div>
84:         </div>
85:     </div>
86:     <h2>{{ $label }}</h2>
87:     @else
88:     <div class="mb-4">
89:         <div class="display-1 text-primary mb-3">
90:             <i data-feather="check-circle" style="width: 80px; height: 80px;"></i>
91:         </div>
92:         <h2 class="mt-3">Ujian Selesai!</h2>
93:         <p class="text-muted">Jawaban Anda telah berhasil dikirim dan tersimpan di sistem.</p>
94:     </div>
95:     @endif
96: 
97:     <p class="sub">Ujian <strong style="color:#c4b5fd;">{{ $schedule->name }}</strong> telah selesai.</p>
98: 
99:     @if($schedule->show_score)
100:     <div class="stat-row">
101:         <div class="stat-item">
102:             <div class="num" style="color:#34d399;">{{ $correct }}</div>
103:             <div class="lbl">Benar</div>
104:         </div>
105:         <div class="stat-item">
106:             <div class="num" style="color:#ef4444;">{{ $total - $correct }}</div>
107:             <div class="lbl">Salah</div>
108:         </div>
109:         <div class="stat-item">
110:             <div class="num" style="color:#9ca3af;">{{ $total }}</div>
111:             <div class="lbl">Total Soal</div>
112:         </div>
113:     </div>
114:     @endif
115: 
116:     <p class="sub mb-3" style="font-size:.75rem;">
117:         Terima kasih telah mengerjakan ujian ini, {{ $siswa->name }}.<br>
118:         Hasil sudah tercatat oleh sistem.
119:     </p>
120: 
121:     <a href="{{ route('siswa.dashboard') }}" class="btn-back">← Kembali ke Dashboard</a>
122: </div>
123: 
124: <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
125: <script>
126:     feather.replace();
127: </script>
128: </body>
129: </html>
130: 
The above content shows the entire, complete file contents of the requested file.
