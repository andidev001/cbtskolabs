<?php
$content = file_get_contents('resources/views/siswa/exam.blade.php');

$newJs = <<<'JS'
    function renderQuestion(data) {
        currentNo = data.current_no;
        document.getElementById('current_no').value = currentNo;
        document.getElementById('current_question_id').value = data.question.id;
        document.querySelector('.soal-info h2').textContent = `Soal nomor ${currentNo}`;
        
        let qTextHtml = data.question.question_text;
        if (data.question.type === 'LS' && data.question.audio_path) {
            qTextHtml = `<div class="mb-3"><audio controls class="w-100" controlsList="nodownload"><source src="/storage/${data.question.audio_path}" type="audio/mpeg">Browser Anda tidak mendukung audio.</audio></div>` + qTextHtml;
        }
        document.getElementById('qText').innerHTML = qTextHtml;
        
        const isFlagged = data.current_flagged;
        document.getElementById('is_flagged_val').value = isFlagged ? '1' : '0';
        document.getElementById('flagBtn').classList.toggle('flagged', isFlagged);

        const container = document.getElementById('optionsContainer');
        container.innerHTML = '';
        
        const type = data.question.type;
        const curAns = data.current_answer;

        if (['PG', 'LS', 'BS', 'ST'].includes(type)) {
            ['a', 'b', 'c', 'd', 'e'].forEach(opt => {
                const text = data.question[`option_${opt}`];
                if (text) {
                    const key = (type === 'BS' || type === 'ST') ? opt : opt.toUpperCase();
                    const isSelected = curAns === key;
                    const label = document.createElement('label');
                    label.className = `option-item ${isSelected ? 'selected' : ''}`;
                    label.onclick = () => selectOption(key, 'radio');
                    label.innerHTML = `
                        <input type="radio" name="answer" value="${key}" id="ans_${key}" ${isSelected ? 'checked' : ''} style="display:none;">
                        <div class="option-circle">${key.toUpperCase()}</div>
                        <div class="option-text">${text}</div>
                    `;
                    container.appendChild(label);
                }
            });
        } else if (type === 'PGK') {
            const curAnsArr = Array.isArray(curAns) ? curAns : [];
            ['a', 'b', 'c', 'd', 'e'].forEach(opt => {
                const text = data.question[`option_${opt}`];
                if (text) {
                    const key = opt.toUpperCase();
                    const isSelected = curAnsArr.includes(key);
                    const label = document.createElement('label');
                    label.className = `option-item ${isSelected ? 'selected' : ''}`;
                    label.onclick = (e) => { e.preventDefault(); toggleCheckboxOption(key); };
                    label.innerHTML = `
                        <input type="checkbox" name="answer[]" value="${key}" id="ans_${key}" ${isSelected ? 'checked' : ''} style="display:none;">
                        <div class="option-circle" style="border-radius:4px;"><i data-feather="check" style="opacity:${isSelected?'1':'0'};"></i></div>
                        <div class="option-text">${text}</div>
                    `;
                    container.appendChild(label);
                }
            });
            feather.replace();
        } else if (type === 'MJ') {
            const lefts = data.question.mj_left || [];
            const rights = data.question.mj_right || [];
            let html = '<div class="table-responsive"><table class="table table-bordered bg-white"><tbody>';
            const curAnsObj = (typeof curAns === 'object' && curAns !== null) ? curAns : {};
            
            lefts.forEach((leftItem, idx) => {
                html += `<tr>
                    <td class="align-middle fw-bold" style="width:50%;">${leftItem}</td>
                    <td style="width:50%;">
                        <select class="form-select mj-select" data-idx="${idx}" onchange="saveAnswer()">
                            <option value="">-- Pilih Pasangan --</option>`;
                rights.forEach(rightItem => {
                    const sel = (curAnsObj[idx] === rightItem) ? 'selected' : '';
                    html += `<option value="${rightItem.replace(/"/g, '&quot;')}" ${sel}>${rightItem}</option>`;
                });
                html += `</select></td></tr>`;
            });
            html += '</tbody></table></div>';
            container.innerHTML = html;
        } else if (type === 'MT') {
            const items = data.question.mt_items || [];
            let html = '<p class="text-muted small mb-2 fw-bold">Pilih item untuk setiap posisi (dari atas ke bawah):</p>';
            const curAnsArr = Array.isArray(curAns) ? curAns : [];
            
            html += '<div class="d-flex flex-column gap-2">';
            for(let i=0; i<items.length; i++) {
                html += `<div class="input-group shadow-sm">
                    <span class="input-group-text fw-bold bg-light">Posisi ${i+1}</span>
                    <select class="form-select mt-select border-start-0" data-pos="${i}" onchange="saveAnswer()">
                        <option value="">-- Pilih Item --</option>`;
                items.forEach(item => {
                    const sel = (curAnsArr[i] === item) ? 'selected' : '';
                    html += `<option value="${item.replace(/"/g, '&quot;')}" ${sel}>${item}</option>`;
                });
                html += `</select></div>`;
            }
            html += '</div>';
            container.innerHTML = html;
        } else if (type === 'IS') {
            const input = document.createElement('input');
            input.type = 'text';
            input.id = 'shortAnswer';
            input.className = 'form-control form-control-lg';
            input.placeholder = "Ketik jawaban singkat Anda...";
            input.value = curAns || '';
            input.onblur = saveAnswer;
            container.appendChild(input);
        } else {
            const textarea = document.createElement('textarea');
            textarea.id = 'essayAnswer';
            textarea.className = 'form-control';
            textarea.rows = 8;
            textarea.placeholder = "Tuliskan jawaban Anda di sini...";
            textarea.value = curAns || '';
            textarea.onblur = saveAnswer;
            container.appendChild(textarea);
        }

        document.getElementById('btnPrev').disabled = (currentNo <= 1);
        document.getElementById('btnNext').style.display = (currentNo >= totalSoal) ? 'none' : 'flex';
        document.getElementById('btnFinish').style.display = (currentNo < totalSoal) ? 'none' : 'flex';

        document.querySelectorAll('.num-btn').forEach(btn => btn.classList.remove('current'));
        document.getElementById(`grid-${currentNo}`).classList.add('current');
        
        if (data.status_map) {
            Object.keys(data.status_map).forEach(btnNo => {
                const btn = document.getElementById(`grid-${btnNo}`);
                if (btn) {
                    btn.classList.remove('answered', 'flagged');
                    if (data.status_map[btnNo].flagged) btn.classList.add('flagged');
                    else if (data.status_map[btnNo].answered) btn.classList.add('answered');
                }
            });
        }
        window.scrollTo(0, 0);
    }

    function selectOption(key, inputType) {
        if(inputType === 'radio') {
            document.querySelectorAll('.option-item').forEach(el => el.classList.remove('selected'));
            const radio = document.getElementById('ans_' + key);
            if(radio) {
                radio.checked = true;
                radio.parentElement.classList.add('selected');
                saveAnswer();
            }
        }
    }
    
    function toggleCheckboxOption(key) {
        const checkbox = document.getElementById('ans_' + key);
        if(checkbox) {
            checkbox.checked = !checkbox.checked;
            const icon = checkbox.parentElement.querySelector('i');
            if(checkbox.checked) {
                checkbox.parentElement.classList.add('selected');
                if(icon) icon.style.opacity = '1';
            } else {
                checkbox.parentElement.classList.remove('selected');
                if(icon) icon.style.opacity = '0';
            }
            saveAnswer();
        }
    }

    function saveAnswer() {
        const qid = document.getElementById('current_question_id').value;
        const isFlagged = document.getElementById('is_flagged_val').value;
        let answer = null;
        
        const radio = document.querySelector('input[type="radio"][name="answer"]:checked');
        if (radio) {
            answer = radio.value;
        } else if (document.querySelector('input[type="checkbox"][name="answer[]"]')) {
            const checks = document.querySelectorAll('input[type="checkbox"][name="answer[]"]:checked');
            if(checks.length > 0) {
                answer = Array.from(checks).map(c => c.value);
            }
        } else if (document.querySelector('.mj-select')) {
            answer = {};
            let hasAns = false;
            document.querySelectorAll('.mj-select').forEach(sel => {
                if(sel.value) { answer[sel.dataset.idx] = sel.value; hasAns = true; }
            });
            if(!hasAns) answer = null;
        } else if (document.querySelector('.mt-select')) {
            answer = [];
            let hasAns = false;
            document.querySelectorAll('.mt-select').forEach(sel => {
                if(sel.value) { answer[sel.dataset.pos] = sel.value; hasAns = true; }
            });
            if(!hasAns) answer = null;
        } else if (document.getElementById('shortAnswer')) {
            answer = document.getElementById('shortAnswer').value;
            if(!answer.trim()) answer = null;
        } else if (document.getElementById('essayAnswer')) {
            answer = document.getElementById('essayAnswer').value;
            if(!answer.trim()) answer = null;
        }

        isSaving = true;
        fetch(`{{ url('siswa/exam') }}/${scheduleId}/answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ question_id: qid, answer: answer, is_flagged: isFlagged == '1' })
        })
        .then(res => res.json())
        .then(data => {
            isSaving = false;
            if (data.success) {
                const btn = document.getElementById(`grid-${currentNo}`);
                if (data.flagged) {
                    btn.classList.add('flagged');
                    btn.classList.remove('answered');
                } else if (data.answered) {
                    btn.classList.add('answered');
                    btn.classList.remove('flagged');
                } else {
                    btn.classList.remove('answered', 'flagged');
                }
            }
        });
    }
JS;

$content = preg_replace('/function renderQuestion\(data\).*?\}\n\s*\}\n/s', $newJs . "\n", $content);
file_put_contents('resources/views/siswa/exam.blade.php', $content);
echo "DONE\n";

