<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($cfg['title']) ?> — MCQ Bank</title>
    <style>
        *{box-sizing:border-box}body{margin:0;padding:24px;font-family:Arial,sans-serif;background:#f4f7fb;color:#1f2937}
        main{max-width:1100px;margin:auto}.top,.panel{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:18px}
        .top{display:flex;justify-content:space-between;align-items:center;gap:12px}.muted{color:#64748b}
        input,select,button{padding:10px;border:1px solid #cbd5e1;border-radius:7px;font-size:15px}
        input,select{width:100%;margin:6px 0 14px}button{background:#1769aa;color:white;border:0;cursor:pointer}
        button:disabled,select:disabled{opacity:.55;cursor:not-allowed}
        table{width:100%;border-collapse:collapse;background:#fff}th,td{text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;vertical-align:top}
        th{background:#edf4fb}.msg{padding:10px;border-radius:7px;margin:10px 0}.ok{background:#e8f7ed;color:#176534}.err{background:#fff0f0;color:#9b1c1c}
        a{color:#1769aa;text-decoration:none}
    </style>
</head>
<body>
<main>
    <div class="top">
        <div><h1><?= esc($cfg['title']) ?></h1><div class="muted">MCQ Data Bank · Admin</div></div>
        <a href="<?= site_url('dashboard/admin') ?>">← Dashboard</a>
    </div>

    <?php if (! empty($success)): ?><div class="msg ok"><?= esc($success) ?></div><?php endif; ?>
    <?php if (! empty($error)): ?><div class="msg err"><?= esc($error) ?></div><?php endif; ?>

    <section class="panel">
        <h2>Add <?= esc(rtrim($cfg['title'], 's')) ?></h2>
        <form method="post" action="<?= current_url() ?>" id="master-data-form">
            <?= csrf_field() ?>
            <?php if (($cfg['title'] ?? '') === 'Topics'): ?>
                <label for="subject_id">Subject</label>
                <select id="subject_id" name="subject_id" required>
                    <option value="">Select Subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= esc((string) $subject['id']) ?>" <?= (string) $selectedSubjectId === (string) $subject['id'] ? 'selected' : '' ?>><?= esc($subject['label']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="parent_id">Main System</label>
                <select id="parent_id" name="parent_id" required disabled>
                    <option value="">Select Subject first</option>
                </select>
                <div id="system-status" class="muted" role="status" aria-live="polite"></div>
            <?php endif; ?>

            <label for="name"><?= esc($cfg['nameLabel']) ?></label>
            <input id="name" name="name" type="text" maxlength="255" required>

            <?php if (($cfg['title'] ?? '') !== 'Topics' && $cfg['parent'] !== null): ?>
                <label for="parent_id"><?= esc($cfg['parent']['title']) ?></label>
                <select id="parent_id" name="parent_id" required>
                    <option value="">Select <?= esc($cfg['parent']['title']) ?></option>
                    <?php foreach ($parents as $parent): ?>
                        <option value="<?= esc((string) $parent['id']) ?>"><?= esc($parent['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <button type="submit" id="save-button">Save</button>
        </form>
    </section>

    <section class="panel">
        <h2>Existing records (<?= count($records) ?>)</h2>
        <div style="overflow-x:auto">
            <table>
                <thead><tr>
                    <?php foreach ($cfg['columns'] as $field => $label): ?><th><?= esc($label) ?></th><?php endforeach; ?>
                </tr></thead>
                <tbody>
                <?php if (! $records): ?>
                    <tr><td colspan="<?= count($cfg['columns']) ?>">No records found.</td></tr>
                <?php else: foreach ($records as $record): ?>
                    <tr>
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

<?php if (($cfg['title'] ?? '') === 'Topics'): ?>
<script>
(() => {
    const subjectSelect = document.getElementById('subject_id');
    const systemSelect = document.getElementById('parent_id');
    const status = document.getElementById('system-status');
    const saveButton = document.getElementById('save-button');
    const endpointBase = <?= json_encode(site_url('master/systems-by-subject')) ?>;

    async function loadSystems(subjectId) {
        systemSelect.innerHTML = '';
        systemSelect.disabled = true;
        saveButton.disabled = true;

        if (!subjectId) {
            systemSelect.add(new Option('Select Subject first', ''));
            status.textContent = '';
            return;
        }

        systemSelect.add(new Option('Loading systems...', ''));
        status.textContent = 'Loading systems...';

        try {
            const response = await fetch(endpointBase + '/' + encodeURIComponent(subjectId), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await response.json();
            systemSelect.innerHTML = '';

            if (!response.ok || data.status !== 'success') {
                systemSelect.add(new Option('Unable to load systems', ''));
                status.textContent = data.message || 'Unable to load systems. Please try again.';
                return;
            }

            if (!data.systems.length) {
                systemSelect.add(new Option('No systems found for this subject', ''));
                status.textContent = 'No systems are available for this subject. Add a system first.';
                return;
            }

            systemSelect.add(new Option('Select Main System', ''));
            data.systems.forEach(item => systemSelect.add(new Option(item.name, item.id)));
            systemSelect.disabled = false;
            saveButton.disabled = false;
            status.textContent = 'Select the main system, then enter the topic name.';
        } catch (error) {
            systemSelect.innerHTML = '';
            systemSelect.add(new Option('Unable to load systems', ''));
            status.textContent = 'A network error occurred while loading systems.';
        }
    }

    subjectSelect.addEventListener('change', () => loadSystems(subjectSelect.value));
    systemSelect.addEventListener('change', () => {
        saveButton.disabled = !subjectSelect.value || !systemSelect.value;
    });

    if (subjectSelect.value) {
        loadSystems(subjectSelect.value);
    } else {
        systemSelect.add(new Option('Select Subject first', ''));
    }
})();
</script>
<?php endif; ?>
</body>
</html>
