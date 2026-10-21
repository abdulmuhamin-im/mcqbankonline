<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>MCQ Bank</title>
<style>
*{box-sizing:border-box}body{margin:0;padding:24px;font-family:Arial,sans-serif;background:#f4f7fb;color:#1f2937}main{max-width:1400px;margin:auto}.top,.panel{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:18px}.top{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}h1{margin:0 0 6px;font-size:25px}.muted{color:#64748b}.actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}input,button,.btn{padding:10px 13px;border:1px solid #cbd5e1;border-radius:7px;font-size:14px}button,.btn{background:#1769aa;color:white;border:0;cursor:pointer;text-decoration:none;display:inline-block}input{min-width:260px}table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:11px;border-bottom:1px solid #e2e8f0;vertical-align:top}th{background:#edf4fb}td.question{min-width:280px;max-width:460px;white-space:normal;overflow-wrap:anywhere}.msg{padding:11px;border-radius:7px;margin:10px 0}.ok{background:#e8f7ed;color:#176534}.err{background:#fff0f0;color:#9b1c1c}.small{font-size:12px;color:#64748b}.pager{margin-top:18px}.pager a,.pager strong{padding:7px 10px;border:1px solid #cbd5e1;border-radius:5px;margin-right:4px;text-decoration:none}
</style></head><body><main>
<div class="top"><div><h1>MCQ Bank</h1><div class="muted">All online questions are stored in the submission table. Approval takes place in the local system.</div></div>
<div style="display:flex; gap:10px; align-items:center;">
    <a href="<?= site_url('dashboard/admin') ?>" class="btn btn-secondary">
        ← Dashboard
    </a>

    <a href="<?= site_url('master/mcqs/new') ?>" class="btn btn-primary">
        + Download Submitted MCQ
    </a>
</div>
</div>
<?php if (session()->getFlashdata('success')): ?><div class="msg ok"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
<?php if (session()->getFlashdata('error')): ?><div class="msg err"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
<div class="panel"><form method="get" action="<?= site_url('master/mcqs') ?>" class="actions"><input type="search" name="q" value="<?= esc($search) ?>" placeholder="Search question, ID, subject, system, or topic"><button type="submit">Search</button><a href="<?= site_url('master/mcqs') ?>" class="btn btn-secondary">Clear </a><span class="muted">Showing <?= count($mcqs) ?> on this page</span></form></div>
<div class="panel" style="overflow-x:auto"><table><thead><tr><th>ID</th><th>Question</th><th>Subject</th><th>System</th><th>Topic</th><th>Key</th><th>Status</th></tr></thead><tbody>
<?php if (empty($mcqs)): ?><tr><td colspan="7" class="muted">No questions found in mcq_tbl_mcqstempsaved. Use Add MCQ to create the first record.</td></tr><?php else: foreach ($mcqs as $row): ?><tr><td><?= esc($row['mcq_id']) ?></td><td class="question"><?= esc($row['stem']) ?><div><?= esc($row['leadin']) ?></div><div class="small">A. <?= esc($row['option1']) ?> · B. <?= esc($row['option2']) ?> · C. <?= esc($row['option3']) ?> · D. <?= esc($row['option4']) ?> · E. <?= esc($row['option5']) ?></div></td><td><?= esc($row['subject_name'] ?? '—') ?></td><td><?= esc($row['system'] ?? '—') ?></td><td><?= esc($row['topic'] ?? '—') ?></td><td><strong><?= esc($row['mkey'] ?? '—') ?></strong></td><td><?= (int)$row['status'] === 1 ? 'Submitted' : 'Draft' ?></td></tr><?php endforeach; endif ?>
</tbody></table><div class="pager"><?php if ($pager !== null): ?><?= $pager->links() ?><?php endif; ?></div></div>
</main></body></html>
