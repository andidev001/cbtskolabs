<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class ExamSessionController extends Controller
{
    public function index()
    {
        $sessions = ExamSession::orderBy('name')->get();
        return view('admin.exam-session.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:exam_sessions,name',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        ExamSession::create($request->all());
        return redirect()->route('admin.exam-session.index')->with('success', 'Sesi berhasil ditambahkan.');
    }

    public function update(Request $request, ExamSession $examSession)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:exam_sessions,name,' . $examSession->id,
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $examSession->update($request->all());
        return redirect()->route('admin.exam-session.index')->with('success', 'Sesi berhasil diperbarui.');
    }

    public function destroy(ExamSession $examSession)
    {
        $examSession->delete();
        return redirect()->route('admin.exam-session.index')->with('success', 'Sesi berhasil dihapus.');
    }
}
