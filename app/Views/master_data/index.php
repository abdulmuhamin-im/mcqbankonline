<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($cfg['title']) ?> — MCQ Bank</title>
    <style>
        :root{--navy:#12345a;--blue:#1769aa;--blue2:#2878c8;--ink:#18324f;--muted:#647995;--line:#dbe5f1;--surface:#f2f6fc;--white:#fff}
        *{box-sizing:border-box}
        body{margin:0;padding:26px 22px;font-family:Inter,"Segoe UI",Arial,sans-serif;background:linear-gradient(180deg,#eaf2fc 0,#f5f8fd 260px,#f4f7fb 100%);color:var(--ink);font-size:14px}
        main{max-width:1180px;margin:0 auto}
        .top{position:relative;display:flex;justify-content:space-between;align-items:center;gap:20px;padding:25px 28px;margin-bottom:22px;border:1px solid rgba(255,255,255,.18);border-radius:17px;background:linear-gradient(120deg,#102b4b 0%,#174b7e 60%,#1769aa 100%);color:#fff;box-shadow:0 12px 28px rgba(18,52,90,.14);overflow:hidden}
        .top:after{content:"";position:absolute;width:260px;height:260px;right:-85px;top:-150px;border:1px solid rgba(255,255,255,.12);border-radius:50%;box-shadow:0 0 0 20px rgba(255,255,255,.025),0 0 0 42px rgba(255,255,255,.025);pointer-events:none}
        .top h1{font-size:clamp(23px,3vw,31px);letter-spacing:-.5px;line-height:1.2;margin:0 0 8px;font-weight:750;color:#fff}
        .top .muted{color:#d8e8fb;font-size:13px;letter-spacing:.2px}
        .top a{position:relative;z-index:1;display:inline-flex;align-items:center;gap:8px;padding:10px 15px;border-radius:9px;background:#fff;color:#174f88;font-weight:700;font-size:13px;white-space:nowrap;box-shadow:0 3px 10px rgba(0,0,0,.08);transition:transform .15s,background .15s}
        .top a:hover{background:#eaf3ff;transform:translateY(-1px)}
        .panel{background:var(--white);border:1px solid var(--line);border-radius:15px;padding:23px 24px;margin-bottom:20px;box-shadow:0 6px 20px rgba(26,58,95,.045)}
        .panel h2{display:flex;align-items:center;gap:10px;margin:0 0 20px;color:var(--navy);font-size:18px;font-weight:750;letter-spacing:-.2px}
        .panel h2:before{content:"";display:inline-block;width:4px;height:21px;border-radius:5px;background:linear-gradient(180deg,#3989df,#1769aa)}
        label{display:block;font-size:13px;font-weight:700;color:#274665;margin:0 0 5px}
        input,select,button{font:inherit;padding:11px 12px;border:1px solid #cbd9e9;border-radius:9px;font-size:14px;transition:border-color .15s,box-shadow .15s,background .15s}
        input,select{display:block;width:100%;margin:0 0 15px;background:#fff;color:#1d3552;min-height:42px}
        input:focus,select:focus{outline:none;border-color:#3987d5;box-shadow:0 0 0 3px rgba(57,135,213,.13)}
        input::placeholder{color:#93a4b8}
        button{background:linear-gradient(135deg,#1769aa,#2479c7);color:#fff;border:0;cursor:pointer;font-weight:700;padding:11px 20px;min-height:40px;box-shadow:0 4px 10px rgba(23,105,170,.16)}
        button:hover:not(:disabled){background:linear-gradient(135deg,#12588f,#1769aa);transform:translateY(-1px)}
        button:disabled,select:disabled{opacity:.55;cursor:not-allowed;box-shadow:none}
        table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;font-size:13px}
        th,td{text-align:left;padding:13px 14px;border-bottom:1px solid #e8eef6;vertical-align:top}
        th{background:#edf4fc;color:#234d78;font-size:12px;text-transform:uppercase;letter-spacing:.45px;font-weight:800}
        th:first-child{border-top-left-radius:9px}th:last-child{border-top-right-radius:9px}
        tbody tr:nth-child(even){background:#fbfdff}tbody tr:hover{background:#f1f7ff}
        tbody tr:last-child td{border-bottom:0}
        .muted{color:var(--muted);font-size:13px}
        .msg{padding:12px 15px;border-radius:10px;margin:0 0 16px;font-size:13px;font-weight:600;border:1px solid transparent}
        .ok{background:#eaf8ef;color:#176534;border-color:#bde8ca}.err{background:#fff0f0;color:#9b1c1c;border-color:#f5c4c4}
        a{color:var(--blue);text-decoration:none}
        #discipline-status,#system-status{margin:-8px 0 14px;font-size:12px}
        [hidden]{display:none!important}
        .name-search-results{display:none;margin:-10px 0 14px;border:1px solid #cbd9e9;border-radius:9px;background:#fff;box-shadow:0 8px 18px rgba(18,52,90,.10);overflow:hidden;max-width:720px}
        .name-search-results button{display:block;width:100%;text-align:left;border-radius:0;box-shadow:none;background:#fff;color:#18324f;border-bottom:1px solid #edf1f7;padding:10px 12px;min-height:0;font-weight:600}
        .name-search-results button:hover{background:#edf6ff;color:#12588f;transform:none}
        .name-search-results small{display:block;color:#647995;font-weight:400;margin-top:3px}
        @media(max-width:700px){body{padding:13px}.top{align-items:flex-start;flex-direction:column;padding:21px 19px;border-radius:13px}.top a{align-self:flex-start}.panel{padding:18px 15px;border-radius:12px}.panel h2{font-size:17px}th,td{padding:10px;font-size:12px}}
    </style>
</head>
<body>
<main>
    <div class="top">
        <div><div style="font-size:11px;letter-spacing:1.5px;font-weight:800;text-transform:uppercase;color:#a9d3ff;margin-bottom:7px">Riphah International University · Peshawar Campus</div><h1><?= esc($cfg['title']) ?></h1><div class="muted">MCQ Data Bank · Academic Master Data</div></div>
        <a href="<?= site_url('dashboard/admin') ?>">← Dashboard</a>
    </div>

    <?php if (! empty($success)): ?><div class="msg ok"><?= esc($success) ?></div><?php endif; ?>
    <?php if (! empty($error)): ?><div class="msg err"><?= esc($error) ?></div><?php endif; ?>

    <section class="panel">
        <h2 id="form-heading">Add <?= esc(rtrim($cfg['title'], 's')) ?></h2>
        <form method="post" action="<?= current_url() ?>" id="master-data-form">
            <?= csrf_field() ?>
            <input type="hidden" name="record_id" id="record_id" value="<?= esc((string) ($editId ?? '')) ?>">

            <?php if (in_array(($cfg['title'] ?? ''), ['Systems', 'Topics'], true)): ?>
                <label for="subject_id">Subject</label>
                <select id="subject_id" name="subject_id" required>
                    <option value="">Select Subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= esc((string) $subject['id']) ?>" <?= (string) $selectedSubjectId === (string) $subject['id'] ? 'selected' : '' ?>><?= esc($subject['label']) ?></option>
                    <?php endforeach; ?>
                </select>

                <div id="discipline-wrap" hidden>
                    <label for="subdiscipline_id">Sub Discipline</label>
                    <select id="subdiscipline_id" name="subdiscipline_id">
                        <option value="">Select Sub Discipline</option>
                    </select>
                    <div id="discipline-status" class="muted" role="status" aria-live="polite"></div>
                </div>
            <?php endif; ?>

            <?php if (($cfg['title'] ?? '') === 'Topics'): ?>
                <label for="parent_id">Main System</label>
                <select id="parent_id" name="parent_id" required disabled>
                    <option value="">Select Subject first</option>
                </select>
                <div id="system-status" class="muted" role="status" aria-live="polite"></div>
            <?php endif; ?>

            <label for="name"><?= esc($cfg['nameLabel']) ?></label>
            <input id="name" name="name" type="text" maxlength="255" required autocomplete="off" placeholder="Type a name to add or find an existing record">
            <?php if (($cfg['title'] ?? '') === 'Departments'): ?>
                <div id="name-search-results" class="name-search-results" role="listbox" aria-label="Matching departments"></div>
                <div class="muted" id="name-search-hint" style="margin:-8px 0 14px">Type to find an existing department. Select a match to edit it.</div>
            <?php endif; ?>

            <?php if (! in_array(($cfg['title'] ?? ''), ['Systems', 'Topics'], true) && $cfg['parent'] !== null): ?>
                <label for="parent_id"><?= esc($cfg['parent']['title']) ?></label>
                <select id="parent_id" name="parent_id" required>
                    <option value="">Select <?= esc($cfg['parent']['title']) ?></option>
                    <?php foreach ($parents as $parent): ?>
                        <option value="<?= esc((string) $parent['id']) ?>"><?= esc($parent['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <button type="submit" id="save-button"><?= ! empty($editId) ? 'Update Record' : 'Save' ?></button>
            <button type="button" id="cancel-edit" style="display:<?= ! empty($editId) ? 'inline-flex' : 'none' ?>;margin-left:8px;background:#647995">Cancel Edit</button>
        </form>
    </section>

    <section class="panel">
        <h2>Existing records (<span id="visible-count"><?= count($records) ?></span> / <?= count($records) ?>)</h2>
        <div class="muted" style="margin:-7px 0 13px">Use the Add/Edit name box above to search; click a department row here to edit it as well.</div>
        <div style="overflow-x:auto">
            <table>
                <thead><tr>
                    <?php foreach ($cfg['columns'] as $field => $label): ?><th><?= esc($label) ?></th><?php endforeach; ?>
                </tr></thead>
                <tbody>
                <?php if (! $records): ?>
                    <tr><td colspan="<?= count($cfg['columns']) ?>">No records found.</td></tr>
                <?php else: foreach ($records as $record): ?>
                    <tr class="record-row" data-record-id="<?= esc((string) ($record[$cfg['id']] ?? '')) ?>" data-record-name="<?= esc((string) ($record[$cfg['name']] ?? '')) ?>" style="<?= ($cfg['title'] ?? '') === 'Departments' ? 'cursor:pointer' : '' ?>">
                        <?php foreach ($cfg['columns'] as $field => $label): ?>
                            <td><?= esc((string) ($record[$field] ?? '')) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
(() => {
    const rows = Array.from(document.querySelectorAll('tbody tr.record-row'));
    const count = document.getElementById('visible-count');
    const isDepartments = <?= json_encode(($cfg['title'] ?? '') === 'Departments') ?>;
    const form = document.getElementById('master-data-form');
    const nameInput = document.getElementById('name');
    const recordId = document.getElementById('record_id');
    const save = document.getElementById('save-button');
    const cancel = document.getElementById('cancel-edit');
    const heading = document.getElementById('form-heading');
    const results = document.getElementById('name-search-results');
    const hint = document.getElementById('name-search-hint');
    const rowsById = new Map(rows.map(row => [row.dataset.recordId, row]));

    function filterRows(query) {
        let visible = 0;
        rows.forEach(row => {
            const match = !query || row.textContent.toLocaleLowerCase().includes(query);
            row.hidden = !match;
            if (match) visible++;
        });
        if (count) count.textContent = visible;
    }
    function setEditMode(row) {
        if (!row) return;
        recordId.value = row.dataset.recordId;
        nameInput.value = row.dataset.recordName;
        heading.textContent = 'Edit Department';
        save.textContent = 'Update Department';
        cancel.style.display = 'inline-flex';
        rows.forEach(r => r.style.outline = '');
        row.style.outline = '2px solid #3987d5';
        if (results) { results.style.display = 'none'; results.innerHTML = ''; }
        if (hint) hint.textContent = 'Editing selected department. Change the name and click Update Department.';
        filterRows(row.dataset.recordName.toLocaleLowerCase());
        form.scrollIntoView({behavior: 'smooth', block: 'start'});
        nameInput.focus({preventScroll: true});
    }
    function resetEditMode(keepText = false) {
        recordId.value = '';
        if (!keepText) form.reset();
        heading.textContent = 'Add Department';
        save.textContent = 'Save';
        cancel.style.display = 'none';
        rows.forEach(r => r.style.outline = '');
        if (hint) hint.textContent = 'Type to find an existing department. Select a match to edit it.';
    }
    if (isDepartments) {
        nameInput.addEventListener('input', () => {
            const q = nameInput.value.trim().toLocaleLowerCase();
            // Typing a different name returns the form to Add mode until a match is selected.
            if (recordId.value && nameInput.value !== (rowsById.get(recordId.value)?.dataset.recordName || '')) resetEditMode(true);
            filterRows(q);
            if (!results) return;
            results.innerHTML = '';
            if (q.length === 0) { results.style.display = 'none'; return; }
            const matches = rows.filter(row => row.dataset.recordName.toLocaleLowerCase().includes(q)).slice(0, 8);
            if (!matches.length) {
                results.innerHTML = '<div style="padding:11px 12px;color:#647995">No matching department. You can add it as a new record.</div>';
            } else {
                matches.forEach(row => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.setAttribute('role', 'option');
                    option.innerHTML = '';
                    option.textContent = row.dataset.recordName;
                    const detail = document.createElement('small');
                    detail.textContent = 'Click to load this record for editing';
                    option.appendChild(detail);
                    option.addEventListener('click', () => setEditMode(row));
                    results.appendChild(option);
                });
            }
            results.style.display = 'block';
        });
        rows.forEach(row => row.addEventListener('click', event => {
            if (event.target.closest('a,button,input,select,label')) return;
            setEditMode(row);
        }));
        cancel.addEventListener('click', () => {
            resetEditMode(false);
            filterRows('');
            if (results) { results.style.display = 'none'; results.innerHTML = ''; }
            nameInput.focus();
        });
        document.addEventListener('click', event => {
            if (results && !results.contains(event.target) && event.target !== nameInput) results.style.display = 'none';
        });
        if (recordId.value && rowsById.has(recordId.value)) setEditMode(rowsById.get(recordId.value));
    }
})();
</script>

<?php if (in_array(($cfg['title'] ?? ''), ['Systems', 'Topics'], true)): ?>
<script>
(() => {
    const subjectSelect = document.getElementById('subject_id');
    const disciplineWrap = document.getElementById('discipline-wrap');
    const disciplineSelect = document.getElementById('subdiscipline_id');
    const disciplineStatus = document.getElementById('discipline-status');
    const systemSelect = document.getElementById('parent_id');
    const systemStatus = document.getElementById('system-status');
    const saveButton = document.getElementById('save-button');
    const isTopicForm = <?= json_encode(($cfg['title'] ?? '') === 'Topics') ?>;
    const disciplinesUrl = <?= json_encode(site_url('master/subdisciplines-by-subject')) ?>;
    const systemsUrl = <?= json_encode(site_url('master/systems-by-subject')) ?>;

    async function loadSubjectOptions(subjectId) {
        disciplineWrap.hidden = true;
        disciplineSelect.required = false;
        disciplineSelect.innerHTML = '';
        disciplineSelect.add(new Option('Select Sub Discipline', ''));
        disciplineStatus.textContent = '';
        if (isTopicForm) {
            systemSelect.innerHTML = '';
            systemSelect.disabled = true;
            systemSelect.add(new Option('Select Subject first', ''));
            systemStatus.textContent = '';
        }
        saveButton.disabled = true;
        if (!subjectId) return;

        try {
            const response = await fetch(disciplinesUrl + '/' + encodeURIComponent(subjectId), {
                headers: {'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json'}
            });
            const data = await response.json();
            if (!response.ok || data.status !== 'success') throw new Error(data.message || 'Unable to load Sub Disciplines.');

            const items = data.subdisciplines || [];
            if (items.length) {
                disciplineWrap.hidden = false;
                disciplineSelect.required = true;
                items.forEach(item => disciplineSelect.add(new Option(item.name, item.id)));
                disciplineStatus.textContent = 'This Subject requires a Sub Discipline.';
                if (isTopicForm) systemSelect.add(new Option('Select Sub Discipline first', ''));
            } else {
                disciplineWrap.hidden = true;
                disciplineSelect.required = false;
                if (isTopicForm) systemSelect.add(new Option('Loading systems...', ''));
            }

            if (isTopicForm) await loadSystems(subjectId, items.length ? '' : null);
            else saveButton.disabled = false;
        } catch (e) {
            disciplineWrap.hidden = false;
            disciplineStatus.textContent = e.message || 'Unable to load Sub Disciplines.';
            saveButton.disabled = true;
        }
    }

    async function loadSystems(subjectId, disciplineId) {
        if (!isTopicForm) return;
        systemSelect.innerHTML = '';
        systemSelect.disabled = true;
        saveButton.disabled = true;
        if (!subjectId) {
            systemSelect.add(new Option('Select Subject first', ''));
            return;
        }
        if (disciplineId === '') {
            systemSelect.add(new Option('Select Sub Discipline first', ''));
            systemStatus.textContent = 'Select a Sub Discipline to load its systems.';
            return;
        }
        systemSelect.add(new Option('Loading systems...', ''));
        const url = systemsUrl + '/' + encodeURIComponent(subjectId) + (disciplineId ? '?subdiscipline_id=' + encodeURIComponent(disciplineId) : '');
        try {
            const response = await fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
            const data = await response.json();
            systemSelect.innerHTML = '';
            if (!response.ok || data.status !== 'success') throw new Error(data.message || 'Unable to load systems.');
            if (!data.systems.length) {
                systemSelect.add(new Option('No systems found', ''));
                systemStatus.textContent = 'No systems are available for this selection. Add a system first.';
                return;
            }
            systemSelect.add(new Option('Select Main System', ''));
            data.systems.forEach(item => systemSelect.add(new Option(item.name, item.id)));
            systemSelect.disabled = false;
            systemStatus.textContent = 'Select the Main System, then enter the topic name.';
            saveButton.disabled = false;
        } catch (e) {
            systemSelect.innerHTML = '';
            systemSelect.add(new Option('Unable to load systems', ''));
            systemStatus.textContent = e.message || 'A network error occurred while loading systems.';
        }
    }

    subjectSelect.addEventListener('change', () => loadSubjectOptions(subjectSelect.value));
    disciplineSelect.addEventListener('change', () => {
        if (isTopicForm) loadSystems(subjectSelect.value, disciplineSelect.value);
        else saveButton.disabled = !subjectSelect.value || !disciplineSelect.value;
    });
    if (isTopicForm) {
        systemSelect.addEventListener('change', () => saveButton.disabled = !subjectSelect.value || !systemSelect.value || (!disciplineWrap.hidden && !disciplineSelect.value));
    }
    if (subjectSelect.value) loadSubjectOptions(subjectSelect.value);
    else if (isTopicForm) {
        systemSelect.innerHTML = '';
        systemSelect.add(new Option('Select Subject first', ''));
    }
})();
</script>
<?php endif; ?>
</body>
</html>
