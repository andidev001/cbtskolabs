<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\BankSoal;
use App\Models\Subject;
use App\Models\Question;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;

class BankSoalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BankSoal::with('subject')->withCount('questions');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('subject_name', function ($row) {
                return $row->subject->name ?? '-';
            })
                ->addColumn('status_label', function ($row) {
                $checked = $row->status ? 'checked' : '';
                return '<div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input toggle-status" type="checkbox" data-id="' . $row->id . '" ' . $checked . '>
                            </div>';
            })
                ->addColumn('action', function ($row) {
                $btn = '<div class="text-center">';
                $btn .= '<a href="' . route('admin.bank-soal.show', $row->id) . '" class="btn btn-sm btn-info text-white me-1"><i class="align-middle" data-feather="plus"></i> Soal</a>';
                $btn .= '<a href="' . route('admin.bank-soal.edit', $row->id) . '" class="btn btn-sm btn-primary me-1" title="Edit" data-bs-toggle="tooltip"><i class="align-middle" data-feather="edit-2"></i></a>';
                $btn .= '<form action="' . route('admin.bank-soal.destroy', $row->id) . '" method="POST" class="d-inline">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger btn-delete"  title="Hapus"><i class="align-middle" data-feather="trash-2"></i></button>
                                </form>';
                $btn .= '</div>';
                return $btn;
            })
                ->rawColumns(['status_label', 'action'])
                ->make(true);
        }
        return view('admin.bank-soal.index');
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.bank-soal.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'code' => 'required|unique:bank_soals,code',
            'name' => 'required',
            'pg_count' => 'numeric', 'pg_percent' => 'numeric',
            'pgk_count' => 'numeric', 'pgk_percent' => 'numeric',
            'ls_count' => 'numeric', 'ls_percent' => 'numeric',
            'mj_count' => 'numeric', 'mj_percent' => 'numeric',
            'is_count' => 'numeric', 'is_percent' => 'numeric',
            'ur_count' => 'numeric', 'ur_percent' => 'numeric',
            'mt_count' => 'numeric', 'mt_percent' => 'numeric',
            'bs_count' => 'numeric', 'bs_percent' => 'numeric',
            'st_count' => 'numeric', 'st_percent' => 'numeric',
            'status' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        BankSoal::create($data);

        return redirect()->route('admin.bank-soal.index')->with('success', 'Bank Soal berhasil dibuat.');
    }

    public function show(string $id)
    {
        $bankSoal = BankSoal::with(['subject', 'questions'])->findOrFail($id);
        return view('admin.bank-soal.show', compact('bankSoal'));
    }

    public function edit(string $id)
    {
        $bankSoal = BankSoal::findOrFail($id);
        $subjects = Subject::all();
        return view('admin.bank-soal.edit', compact('bankSoal', 'subjects'));
    }

    public function update(Request $request, string $id)
    {
        $bankSoal = BankSoal::findOrFail($id);

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'code' => 'required|unique:bank_soals,code,' . $id,
            'name' => 'required',
            'pg_count' => 'numeric', 'pg_percent' => 'numeric',
            'pgk_count' => 'numeric', 'pgk_percent' => 'numeric',
            'ls_count' => 'numeric', 'ls_percent' => 'numeric',
            'mj_count' => 'numeric', 'mj_percent' => 'numeric',
            'is_count' => 'numeric', 'is_percent' => 'numeric',
            'ur_count' => 'numeric', 'ur_percent' => 'numeric',
            'mt_count' => 'numeric', 'mt_percent' => 'numeric',
            'bs_count' => 'numeric', 'bs_percent' => 'numeric',
            'st_count' => 'numeric', 'st_percent' => 'numeric',
            'status' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        $bankSoal->update($data);

        return redirect()->route('admin.bank-soal.index')->with('success', 'Bank Soal berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, string $id)
    {
        $bankSoal = BankSoal::findOrFail($id);
        $bankSoal->update(['status' => !$bankSoal->status]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status Bank Soal berhasil diubah.'
            ]);
        }

        return redirect()->back()->with('success', 'Status Bank Soal berhasil diubah.');
    }

    public function importWord(string $bankSoalId)
    {
        $bankSoal = BankSoal::findOrFail($bankSoalId);
        return view('admin.bank-soal.import', compact('bankSoal'));
    }

    public function processImportWord(Request $request, string $bankSoalId)
    {
        $bankSoal = BankSoal::findOrFail($bankSoalId);

        $request->validate([
            'file' => 'required|file|mimes:docx|max:20480',
            'format' => 'required'
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();

            $images = [];
            $zip = new \ZipArchive();
            if ($zip->open($path) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entry = $zip->getNameIndex($i);
                    if (strpos($entry, 'word/media/') === 0) {
                        $imageName = basename($entry);
                        $imageData = $zip->getFromIndex($i);
                        $extension = pathinfo($imageName, PATHINFO_EXTENSION);

                        $newName = uniqid('word_img_') . '.' . $extension;
                        \Storage::disk('public')->put('questions/images/' . $newName, $imageData);
                        $images[$imageName] = \Storage::url('questions/images/' . $newName);
                    }
                }
                $zip->close();
            }

            $phpWord = IOFactory::load($path);
            $fullText = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $fullText .= $element->getText() . "\n";
                    }
                    elseif ($element instanceof \PhpOffice\PhpWord\Element\Image) {
                        $fullText .= "[IMAGE_PLACEHOLDER]\n";
                    }
                    elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $fullText .= $childElement->getText();
                            }
                            elseif ($childElement instanceof \PhpOffice\PhpWord\Element\Image) {
                                $fullText .= "[IMAGE_PLACEHOLDER]";
                            }
                        }
                        $fullText .= "\n";
                    }
                }
            }

            if (strpos($fullText, '[IMAGE_PLACEHOLDER]') !== false && !empty($images)) {
                $imageUrls = array_values($images);
                $imgIndex = 0;
                $fullText = preg_replace_callback('/\[IMAGE_PLACEHOLDER\]/', function ($matches) use ($imageUrls, &$imgIndex) {
                    $url = isset($imageUrls[$imgIndex]) ? $imageUrls[$imgIndex] : '';
                    $imgIndex++;
                    return $url ? '<img src="' . $url . '" class="img-fluid my-2 d-block">' : '';
                }, $fullText);
            }

            $fullText = preg_replace('/TIPE:\s*(PG|PGK|LS|MJ|MT|BS|ST|IS|UR|Essay)/i', '###TIPE:###', $fullText);
            
            // Because TIPE might appear *before* S:X), we should split by S:X) but include the TIPE if it's there.
            // A better way is to split by S:X), but the chunk BEFORE S:1) might contain TIPE: PGK.
            $questionBlocks = preg_split('/(###TIPE:[a-zA-Z]+###\s*)?S:\d+\)/i', $fullText, -1, PREG_SPLIT_DELIM_CAPTURE);
            $importedCount = 0;

            $blocks = [];
            $currentType = 'PG';
            
            foreach ($questionBlocks as $block) {
                $block = trim($block);
                if ($block == '') continue;
                
                if (preg_match('/^###TIPE:([a-zA-Z]+)###/', $block, $m)) {
                    $currentType = strtoupper($m[1]);
                    if($currentType == 'ESSAY') $currentType = 'UR';
                } else {
                    $blocks[] = ['type' => $currentType, 'content' => $block];
                    $currentType = 'PG'; // reset default
                }
            }

            foreach ($blocks as $qBlock) {
                $lines = explode("\n", trim($qBlock['content']));
                $questionData = [
                    'bank_soal_id' => $bankSoal->id,
                    'type' => $qBlock['type'],
                    'layout' => 'v'
                ];

                $questionBody = '';
                $currentOption = null;
                
                $mjLeft = [];
                $mjRight = [];
                $mtItems = [];

                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line == '') continue;

                    if (preg_match('/^A:\)/i', $line)) {
                        $questionData['option_a'] = trim(substr($line, 3));
                        $currentOption = 'option_a';
                    }
                    elseif (preg_match('/^B:\)/i', $line)) {
                        $questionData['option_b'] = trim(substr($line, 3));
                        $currentOption = 'option_b';
                    }
                    elseif (preg_match('/^C:\)/i', $line)) {
                        $questionData['option_c'] = trim(substr($line, 3));
                        $currentOption = 'option_c';
                    }
                    elseif (preg_match('/^D:\)/i', $line)) {
                        $questionData['option_d'] = trim(substr($line, 3));
                        $currentOption = 'option_d';
                    }
                    elseif (preg_match('/^E:\)/i', $line)) {
                        $questionData['option_e'] = trim(substr($line, 3));
                        $currentOption = 'option_e';
                    }
                    elseif (preg_match('/^KIRI:\)/i', $line)) {
                        $mjLeft[] = trim(substr($line, 6));
                        $currentOption = 'mj_left';
                    }
                    elseif (preg_match('/^KANAN:\)/i', $line)) {
                        $mjRight[] = trim(substr($line, 7));
                        $currentOption = 'mj_right';
                    }
                    elseif (preg_match('/^ITEM:\)/i', $line)) {
                        $mtItems[] = trim(substr($line, 6));
                        $currentOption = 'mt_item';
                    }
                    elseif (preg_match('/^JAWAB:\s*(.*)/i', $line, $matches)) {
                        $ansRaw = trim($matches[1]);
                        
                        if ($questionData['type'] === 'PGK') {
                            // parse "A, B, C" to ["a", "b", "c"]
                            $arr = array_map('trim', explode(',', $ansRaw));
                            $arr = array_map('strtolower', $arr);
                            $questionData['answer'] = json_encode(array_values(array_filter($arr)));
                        } else if (in_array($questionData['type'], ['BS', 'ST'])) {
                            if (stripos($ansRaw, 'benar') !== false || stripos($ansRaw, 'setuju') !== false || strtolower($ansRaw) == 'a') {
                                $questionData['answer'] = 'a';
                            } else {
                                $questionData['answer'] = 'b';
                            }
                        } else {
                            $questionData['answer'] = $questionData['type'] == 'PG' ? strtolower(substr($ansRaw, 0, 1)) : $ansRaw;
                        }
                        $currentOption = 'answer';
                    }
                    else {
                        if (!isset($questionData['option_a']) && empty($mjLeft) && empty($mtItems) && !isset($questionData['answer'])) {
                            $sep = (strpos($line, '<img') === 0 || !$questionBody) ? "" : "<br>";
                            $questionBody .= $sep . $line;
                        }
                        else if ($currentOption == 'option_a') $questionData['option_a'] .= " " . $line;
                        else if ($currentOption == 'option_b') $questionData['option_b'] .= " " . $line;
                        else if ($currentOption == 'option_c') $questionData['option_c'] .= " " . $line;
                        else if ($currentOption == 'option_d') $questionData['option_d'] .= " " . $line;
                        else if ($currentOption == 'option_e') $questionData['option_e'] .= " " . $line;
                        else if ($currentOption == 'answer') $questionData['answer'] .= " " . $line;
                        else if ($currentOption == 'mj_left' && count($mjLeft) > 0) $mjLeft[count($mjLeft)-1] .= " " . $line;
                        else if ($currentOption == 'mj_right' && count($mjRight) > 0) $mjRight[count($mjRight)-1] .= " " . $line;
                        else if ($currentOption == 'mt_item' && count($mtItems) > 0) $mtItems[count($mtItems)-1] .= " " . $line;
                    }
                }

                if ($questionBody) {
                    $questionData['question_text'] = $questionBody;
                    
                    // Finalize BS/ST options
                    if ($questionData['type'] === 'BS') {
                        $questionData['option_a'] = 'Benar';
                        $questionData['option_b'] = 'Salah';
                    } else if ($questionData['type'] === 'ST') {
                        $questionData['option_a'] = 'Setuju';
                        $questionData['option_b'] = 'Tidak Setuju';
                    }
                    
                    // Finalize Complex Options
                    if ($questionData['type'] === 'MJ') {
                        $questionData['complex_options'] = json_encode([
                            'left' => $mjLeft,
                            'right' => $mjRight
                        ]);
                    } else if ($questionData['type'] === 'MT') {
                        $questionData['complex_options'] = json_encode([
                            'items' => $mtItems
                        ]);
                    }

                    Question::create($questionData);
                    $importedCount++;
                }
            }

            if ($importedCount > 0) {
                return redirect()->route('admin.bank-soal.show', $bankSoal->id)
                    ->with('success', "Berhasil mengimpor $importedCount soal dari file Word (termasuk deteksi gambar).");
            }
            else {
                return redirect()->back()
                    ->with('error', "Gagal mengimpor soal. Pastikan format file sesuai dengan template.");
            }

        }
        catch (\Exception $e) {
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat memproses file: " . $e->getMessage());
        }
    }

    public function downloadTemplateWord()
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('PANDUAN IMPORT SOAL SKOLABS CBT', ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER]);
        $section->addText('1. Jangan mengubah format S:X) untuk nomor soal.');
        $section->addText('2. Secara default soal dianggap PG. Untuk tipe lain, tambahkan TIPE: [Jenis] di atas nomor soal.');
        $section->addText('3. Jenis TIPE yang didukung: PG, PGK, MJ, MT, BS, ST, IS, UR.');
        $section->addTextBreak(1);

        $section->addText('TIPE: PG');
        $section->addText('S:1) Ini adalah contoh soal Pilihan Ganda biasa.');
        $section->addText('A:) Pilihan Pertama');
        $section->addText('B:) Pilihan Kedua');
        $section->addText('C:) Pilihan Ketiga');
        $section->addText('D:) Pilihan Keempat');
        $section->addText('E:) Pilihan Kelima');
        $section->addText('JAWAB: B');
        $section->addTextBreak(1);

        $section->addText('TIPE: PGK');
        $section->addText('S:2) Ini contoh soal Pilihan Ganda Kompleks (Jawaban benar lebih dari satu).');
        $section->addText('A:) Pilihan A');
        $section->addText('B:) Pilihan B');
        $section->addText('C:) Pilihan C');
        $section->addText('D:) Pilihan D');
        $section->addText('JAWAB: A, C');
        $section->addTextBreak(1);

        $section->addText('TIPE: MJ');
        $section->addText('S:3) Ini contoh soal Menjodohkan.');
        $section->addText('KIRI:) Indonesia');
        $section->addText('KANAN:) Jakarta');
        $section->addText('KIRI:) Malaysia');
        $section->addText('KANAN:) Kuala Lumpur');
        $section->addTextBreak(1);

        $section->addText('TIPE: MT');
        $section->addText('S:4) Ini contoh soal Mengurutkan. Tulis dari urutan pertama ke terakhir.');
        $section->addText('ITEM:) Pertama');
        $section->addText('ITEM:) Kedua');
        $section->addText('ITEM:) Ketiga');
        $section->addTextBreak(1);

        $section->addText('TIPE: BS');
        $section->addText('S:5) Ini contoh soal Benar / Salah. JAWAB diisi BENAR atau SALAH.');
        $section->addText('JAWAB: BENAR');
        $section->addTextBreak(1);
        
        $section->addText('TIPE: IS');
        $section->addText('S:6) Ini contoh soal Isian Singkat.');
        $section->addText('JAWAB: KataKunci');
        $section->addTextBreak(1);

        $section->addText('TIPE: UR');
        $section->addText('S:7) Ini contoh soal Uraian / Essay.');
        $section->addText('JAWAB: Referensi cara menjawab untuk guru (opsional)');
        $section->addTextBreak(1);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $tempFile = tempnam(sys_get_temp_dir(), 'template_soal_');
        $objWriter->save($tempFile);

        return response()->download($tempFile, 'template_impor_soal.docx')->deleteFileAfterSend(true);
    }

    public function destroy(string $id)
    {
        $bankSoal = BankSoal::findOrFail($id);
        $bankSoal->delete();

        return redirect()->route('admin.bank-soal.index')->with('success', 'Bank Soal berhasil dihapus.');
    }
}
