<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    private function authorizedBankSoal($bankSoalId)
    {
        $guru = Auth::guard('admin')->user();
        $subjectIds = $guru->subjects()->pluck('subjects.id')->toArray();
        return BankSoal::whereIn('subject_id', $subjectIds)->findOrFail($bankSoalId);
    }

    public function create(Request $request, BankSoal $bankSoal)
    {
        $this->authorizedBankSoal($bankSoal->id);
        $type = strtolower($request->get('type', 'pg'));
        if ($type === 'essay') $type = 'ur';
        return view('admin.question.create', compact('bankSoal', 'type'));
    }

    public function store(BankSoal $bankSoal, Request $request)
    {
        $this->authorizedBankSoal($bankSoal->id);

        $rules = [
            'type' => 'required',
            'question_text' => 'required',
            'layout' => 'nullable',
            'audio_file' => 'nullable|file|mimes:mp3,wav|max:5120',
        ];

        // Custom validations based on type
        $type = strtolower($request->type);
        if ($type === 'essay') $type = 'ur';

        if ($type == 'pg' || $type == 'ls') {
            $rules['option_a'] = 'required';
            $rules['option_b'] = 'required';
            $rules['answer'] = 'required';
        }

        $request->validate($rules);

        $data = $request->except(['audio_file', 'complex_options_array']);
        $data['type'] = strtoupper($type);
        $data['bank_soal_id'] = $bankSoal->id;

        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('public/audios');
            $data['audio_path'] = str_replace('public/', '', $path);
        }

        // Store complex data if provided
        if ($request->has('complex_options_array')) {
            $data['complex_options'] = json_encode($request->input('complex_options_array'));
        }
        
        if (is_array($request->answer)) {
            $data['answer'] = json_encode($request->answer);
        }

        Question::create($data);

        return redirect()->route('admin.bank-soal.show', $bankSoal->id)->with('success', 'Soal berhasil ditambahkan.');
    }

    public function show(Question $question)
    {
        $this->authorizedBankSoal($question->bank_soal_id);
        return view('admin.question.show', compact('question'));
    }

    public function edit(Question $question)
    {
        $bankSoal = $this->authorizedBankSoal($question->bank_soal_id);
        $type = strtolower($question->type);
        if ($type === 'essay') $type = 'ur';
        return view('admin.question.edit', compact('question', 'bankSoal', 'type'));
    }

    public function update(Question $question, Request $request)
    {
        $this->authorizedBankSoal($question->bank_soal_id);

        $rules = [
            'question_text' => 'required',
            'layout' => 'nullable',
            'audio_file' => 'nullable|file|mimes:mp3,wav|max:5120',
        ];

        $type = strtolower($question->type);
        if ($type == 'pg' || $type == 'ls') {
            $rules['option_a'] = 'required';
            $rules['option_b'] = 'required';
            $rules['answer'] = 'required';
        }

        $request->validate($rules);

        $data = $request->except(['audio_file', 'complex_options_array']);
        $data['type'] = strtoupper($type);

        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('public/audios');
            $data['audio_path'] = str_replace('public/', '', $path);
        }

        if ($request->has('complex_options_array')) {
            $data['complex_options'] = json_encode($request->input('complex_options_array'));
        }
        
        if (is_array($request->answer)) {
            $data['answer'] = json_encode($request->answer);
        }

        $question->update($data);

        return redirect()->route('admin.bank-soal.show', $question->bank_soal_id)->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        $this->authorizedBankSoal($question->bank_soal_id);
        $bankSoalId = $question->bank_soal_id;
        $question->delete();
        return redirect()->route('admin.bank-soal.show', $bankSoalId)->with('success', 'Soal berhasil dihapus.');
    }
    
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;

            $request->file('upload')->storeAs('public/images/questions', $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('storage/images/questions/' . $fileName);
            $msg = 'Gambar berhasil diunggah';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            echo $response;
            exit;
        }
    }
}
