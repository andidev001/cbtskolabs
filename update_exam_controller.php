<?php
$content = file_get_contents('app/Http/Controllers/Siswa/ExamController.php');

$hook = <<<'PHP'
        // Hide answer and format complex options
        unset($question->answer); // Never send the answer to the student!
        if (in_array($question->type, ['MJ', 'MT']) && $question->complex_options) {
            $opts = json_decode($question->complex_options, true);
            if ($question->type === 'MJ') {
                $right = $opts['right'] ?? [];
                // Use a seeded shuffle based on question id so it doesn't change on reload
                srand($question->id);
                shuffle($right);
                srand(); // reset
                $question->mj_left = $opts['left'] ?? [];
                $question->mj_right = $right;
            } elseif ($question->type === 'MT') {
                $items = $opts['items'] ?? [];
                srand($question->id);
                shuffle($items);
                srand();
                $question->mt_items = $items;
            }
            unset($question->complex_options);
        }
        
        // Ensure answer string is parsed properly for frontend
        $currAns = $answers->get($questionId)?->answer;
        if (in_array($question->type, ['PGK', 'MT', 'MJ'])) {
            $currAns = json_decode($currAns, true) ?: $currAns;
        }

        if ($request->ajax()) {
            return response()->json([
                'question' => $question,
                'current_no' => $no,
                'total' => $total,
                'current_answer' => $currAns,
                'current_flagged' => (bool)$answers->get($questionId)?->is_flagged,
                'remaining_sec' => $remainingSec,
                'status_map' => $statusMap
            ]);
        }
PHP;

$content = preg_replace('/if \(\$request->ajax\(\)\) \{\s*return response\(\)->json\(\[\s*\'question\' => \$question,.*?\n\s*\]\);\s*\}/s', $hook, $content);

$finishLogic = <<<'PHP'
        $totalScore = 0;
        $correctPG = 0;
        $totalQ = $questions->count();
        $grouped = $questions->groupBy('type');
        
        // Helper map from DB types to setting percent column
        $typePercentMap = [
            'PG' => 'pg_percent', 'PGK' => 'pgk_percent', 'LS' => 'ls_percent',
            'MJ' => 'mj_percent', 'IS' => 'is_percent', 'UR' => 'ur_percent',
            'Essay' => 'ur_percent', 'MT' => 'mt_percent', 'BS' => 'bs_percent', 'ST' => 'st_percent'
        ];

        foreach ($grouped as $type => $qs) {
            $count = $qs->count();
            if ($count === 0) continue;
            
            $pctField = $typePercentMap[$type] ?? null;
            $percent = $pctField ? ($schedule->bankSoal->$pctField ?? 0) : 0;
            $earned = 0;
            
            foreach ($qs as $q) {
                $stuAns = $q->stu_ans ?? '';
                $corAns = $q->cor_ans ?? '';
                
                if (in_array($type, ['PG', 'LS', 'BS', 'ST'])) {
                    if (strtoupper($stuAns) === strtoupper($corAns) && $stuAns !== '') {
                        $earned++;
                        if ($type == 'PG') $correctPG++;
                    }
                } elseif ($type == 'IS') {
                    if (strtolower(trim($stuAns)) === strtolower(trim($corAns)) && $stuAns !== '') {
                        $earned++;
                    }
                } elseif ($type == 'PGK') {
                    $stuArr = json_decode($stuAns, true) ?: [];
                    $corArr = json_decode($corAns, true) ?: [];
                    if (count($stuArr) > 0 && empty(array_diff($corArr, $stuArr)) && empty(array_diff($stuArr, $corArr))) {
                        $earned++;
                    }
                } elseif ($type == 'MJ') {
                    $stuMap = json_decode($stuAns, true) ?: [];
                    $corOpts = json_decode($q->complex_options ?? '{}', true);
                    $corLeft = $corOpts['left'] ?? [];
                    $corRight = $corOpts['right'] ?? [];
                    $correctPairs = 0;
                    $totalPairs = count($corLeft);
                    
                    if ($totalPairs > 0 && count($stuMap) > 0) {
                        foreach ($corLeft as $idx => $leftItem) {
                            if (isset($stuMap[$idx]) && $stuMap[$idx] === ($corRight[$idx] ?? '')) {
                                $correctPairs++;
                            }
                        }
                        $earned += ($correctPairs / $totalPairs);
                    }
                } elseif ($type == 'MT') {
                    $stuItems = json_decode($stuAns, true) ?: [];
                    $corOpts = json_decode($q->complex_options ?? '{}', true);
                    $corItems = $corOpts['items'] ?? [];
                    
                    if (count($stuItems) > 0 && $stuItems === $corItems) {
                        $earned++;
                    }
                } elseif (in_array($type, ['UR', 'Essay'])) {
                    $earned += ($q->es_score ?: 0) / 100;
                }
            }
            
            if ($percent > 0) {
                $totalScore += ($earned / $count) * $percent;
            }
        }
PHP;

$content = preg_replace('/\$totalScore = 0;\s*\$correctPG = 0;\s*\$totalQ = \$questions->count\(\);\s*\$grouped = \$questions->groupBy\(\'type\'\);\s*foreach \(\$grouped as \$type => \$qs\) \{.*?\n\s*\}/s', $finishLogic, $content);

$answerLogic = <<<'PHP'
    public function answer(Request $request, $scheduleId)
    {
        $siswa = Auth::guard('siswa')->user();
        $ans = $request->answer;
        if (is_array($ans)) {
            $ans = json_encode($ans);
        }
        
        \App\Models\ExamAnswer::updateOrCreate(
            ['exam_schedule_id' => $scheduleId, 'siswa_id' => $siswa->id, 'question_id' => $request->question_id],
            ['answer' => $ans, 'is_flagged' => $request->boolean('is_flagged')]
        );

        if ($request->ajax()) {
            return response()->json(['success' => true, 'answered' => !empty($ans), 'flagged' => $request->boolean('is_flagged')]);
        }
        return redirect()->back();
    }
PHP;

$content = preg_replace('/public function answer\(Request \$request, \$scheduleId\).*?\n\s*\}\n/s', $answerLogic . "\n", $content);

$queryReplace = <<<'PHP'
$questions = Question::where('bank_soal_id', $schedule->bank_soal_id)
            ->leftJoin('exam_answers', function ($j) use ($scheduleId, $siswa) {
            $j->on('questions.id', '=', 'exam_answers.question_id')->where('exam_answers.exam_schedule_id', $scheduleId)->where('exam_answers.siswa_id', $siswa->id);
        })->select('questions.id', 'questions.type', 'questions.answer as cor_ans', 'questions.complex_options', 'exam_answers.answer as stu_ans', 'exam_answers.score as es_score')->get();
PHP;
$content = preg_replace('/\$questions = Question::where\(\'bank_soal_id\'.*?select\(\'questions\.id\', \'questions\.type\', \'questions\.answer as cor_ans\', \'exam_answers\.answer as stu_ans\', \'exam_answers\.score as es_score\'\)->get\(\);/s', $queryReplace, $content);

file_put_contents('app/Http/Controllers/Siswa/ExamController.php', $content);
echo "DONE\n";

