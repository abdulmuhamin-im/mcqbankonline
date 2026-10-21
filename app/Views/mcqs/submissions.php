<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= esc($title ?? 'Faculty Submissions') ?></title>
<style>
:root{--navy:#173b57;--blue:#287ca7;--teal:#2f7f8e;--ink:#20364a;--muted:#657d90;--line:#dce7ee;--paper:#fff;--wash:#f1f6f9;--pale:#eaf4f8;--green:#e5f4ed}
*{box-sizing:border-box}body{margin:0;padding:24px;font-family:Inter,"Segoe UI",Arial,sans-serif;color:var(--ink);background:linear-gradient(145deg,#eef5f8 0%,#f8fbfc 58%,#edf4f7 100%);min-height:100vh}main{max-width:1440px;margin:0 auto}.hero{position:relative;overflow:hidden;padding:28px 30px;border-radius:22px;background:linear-gradient(115deg,#173b57,#205d79 68%,#2d7d8c);color:#fff;box-shadow:0 15px 36px #183b561a;margin-bottom:20px}.hero:after{content:"";position:absolute;width:280px;height:280px;border:1px solid #ffffff1c;border-radius:50%;right:-65px;top:-150px;box-shadow:0 0 0 35px #ffffff08,0 0 0 70px #ffffff06}.hero-top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;position:relative;z-index:1}h1{font-size:clamp(25px,3vw,34px);margin:0 0 7px;letter-spacing:-.5px}.subtitle{margin:0;color:#e0edf4;line-height:1.55;max-width:850px;font-size:14px}.hero-actions{display:flex;gap:9px;flex-wrap:wrap;position:relative;z-index:1;margin-top:20px}.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:10px;padding:11px 15px;font-weight:750;font-size:13px;text-decoration:none;cursor:pointer;transition:transform .15s,box-shadow .15s;background:#fff;color:var(--navy);box-shadow:0 3px 8px #071e2b1a}.btn:hover{transform:translateY(-1px);box-shadow:0 6px 15px #071e2b24}.btn.primary{background:#e8f5f8;color:#164e69}.btn.ghost{background:#ffffff16;border:1px solid #ffffff55;color:#fff;box-shadow:none}.btn.download{background:linear-gradient(135deg,#267ca8,#2f8a91);color:#fff}.panel{background:#ffffffed;border:1px solid #fff;border-radius:20px;padding:22px 24px;box-shadow:0 12px 32px #183b560d;margin-bottom:20px}.section-title{font-size:17px;font-weight:800;color:var(--navy);margin:0}.section-note{font-size:12px;color:var(--muted);margin-top:4px}.filters{display:grid;grid-template-columns:minmax(180px,1fr) minmax(200px,1.2fr) minmax(220px,1.4fr) auto;gap:13px;align-items:end;margin-top:18px}.field label{display:block;font-size:12px;font-weight:750;color:#35536a;margin:0 0 7px}.field select,.field input{width:100%;height:43px;border:1px solid #cbdce7;border-radius:10px;background:#fff;color:#233d51;padding:0 12px;font:inherit;font-size:13px;outline:none}.field select:focus,.field input:focus{border-color:#5aa2c5;box-shadow:0 0 0 3px #58a2c51c}.filter-actions{display:flex;gap:8px}.btn.filter{height:43px;background:var(--navy);color:#fff;white-space:nowrap}.btn.reset{height:43px;background:#edf3f6;color:#426176;box-shadow:none}.summary{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:15px}.count{display:inline-flex;align-items:center;gap:8px;background:var(--pale);color:#24627d;padding:8px 12px;border-radius:999px;font-size:12px;font-weight:800}.count-dot{width:7px;height:7px;border-radius:50%;background:#2990a0}.table-wrap{overflow:auto;border:1px solid var(--line);border-radius:13px}table{width:100%;border-collapse:separate;border-spacing:0;min-width:1000px;background:#fff}thead th{background:#f3f8fa;color:#597387;text-transform:uppercase;font-size:10px;letter-spacing:.08em;font-weight:800;padding:14px 13px;text-align:left;border-bottom:1px solid var(--line);white-space:nowrap}tbody td{padding:15px 13px;border-bottom:1px solid #e9f0f4;vertical-align:top;font-size:13px}tbody tr:last-child td{border-bottom:0}tbody tr:hover{background:#f8fbfc}.id{font-weight:800;color:#53748a}.question{font-weight:750;color:#213d52;line-height:1.45;max-width:290px}.lead{font-size:12px;line-height:1.45;color:var(--muted);margin-top:5px;max-width:310px}.faculty-name{font-weight:750;color:#234a64}.subtext{font-size:12px;color:var(--muted);line-height:1.5;margin-top:5px}.status{display:inline-flex;align-items:center;gap:6px;background:#e5f3f5;color:#246a77;padding:6px 10px;border-radius:99px;font-size:11px;font-weight:800;white-space:nowrap}.status:before{content:"";width:6px;height:6px;border-radius:50%;background:#2d8c98}.empty{text-align:center;padding:50px 20px!important;color:var(--muted)}.empty-icon{display:inline-grid;place-items:center;width:44px;height:44px;border-radius:14px;background:#edf5f8;color:#41758d;font-size:20px;margin-bottom:10px}.flash{padding:13px 15px;border-radius:12px;margin:0 0 16px;font-size:13px;font-weight:650}.success{background:#e7f5ed;color:#28664a;border:1px solid #cce8d8}.error{background:#fff0ef;color:#903d38;border:1px solid #f1d3d0}.footer-note{font-size:12px;color:var(--muted);line-height:1.55;margin:13px 2px 0}.download-form{display:inline}.pill-light{display:inline-block;border:1px solid #d8e5eb;border-radius:8px;padding:5px 8px;font-size:11px;color:#547083;background:#f8fbfc}
@media(max-width:950px){body{padding:14px}.filters{grid-template-columns:1fr 1fr}.filter-actions{grid-column:1/-1}.hero-top{display:block}.hero-actions{margin-top:16px}.panel{padding:18px}}
@media(max-width:560px){.filters{grid-template-columns:1fr}.filter-actions{grid-column:auto}.filter-actions .btn{flex:1}.hero,.panel{border-radius:16px}.hero{padding:22px}.hero-actions .btn{flex:1}.summary{align-items:flex-start;flex-direction:column}}
</style>
</head>
<body><main>
<header class="hero">
  <div class="hero-top"><div><h1>Faculty Submissions</h1><p class="subtitle">Browse submitted MCQs by faculty member or department, then download the matching questions for offline review and final import. Downloads do not delete records or change submission status.</p></div></div>
  <nav class="hero-actions" aria-label="Page actions">
    <a class="btn download" href="<?= site_url('master/mcqs/submissions/download') . (http_build_query(array_filter(['department_id' => (int)($filters['department_id'] ?? 0) ?: null, 'author_id' => (int)($filters['author_id'] ?? 0) ?: null, 'q' => ($filters['q'] ?? '') !== '' ? $filters['q'] : null])) ? '?' . http_build_query(array_filter(['department_id' => (int)($filters['department_id'] ?? 0) ?: null, 'author_id' => (int)($filters['author_id'] ?? 0) ?: null, 'q' => ($filters['q'] ?? '') !== '' ? $filters['q'] : null])) : '') ?>">↓&nbsp; Download Filtered Questions (CSV)</a>
    <a class="btn ghost" href="<?= site_url('master/mcqs') ?>">← Back</a>
    <a class="btn ghost" href="<?= site_url('dashboard/admin') ?>">Dashboard ↗</a>
  </nav>
</header>
<?php foreach(['success','error'] as $type): if($m=session()->getFlashdata($type)): ?><div class="flash <?= $type ?>"><?= esc($m) ?></div><?php endif; endforeach ?>
<section class="panel">
  <h2 class="section-title">Find submissions</h2><div class="section-note">Choose a department first to narrow the faculty list, or select a faculty member directly.</div>
  <form method="get" action="<?= site_url('master/mcqs/submissions') ?>" class="filters" id="submissionFilters">
    <div class="field"><label for="department_id">Department</label><select name="department_id" id="department_id"><option value="">All departments</option><?php foreach(($departments ?? []) as $d): ?><option value="<?= (int)$d['department_id'] ?>" <?= (int)($filters['department_id'] ?? 0)===(int)$d['department_id']?'selected':'' ?>><?= esc($d['department_name']) ?></option><?php endforeach ?></select></div>
    <div class="field"><label for="author_id">Faculty with submitted MCQs</label><select name="author_id" id="author_id"><option value="">All faculty</option><?php foreach(($faculty ?? []) as $f): ?><option value="<?= (int)$f['author_id'] ?>" <?= (int)($filters['author_id'] ?? 0)===(int)$f['author_id']?'selected':'' ?>><?= esc($f['author_name']) ?></option><?php endforeach ?></select></div>
    <div class="field"><label for="q">Search questions</label><input id="q" name="q" type="search" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Question text, lead-in, or MCQ ID…"></div>
    <div class="filter-actions"><button class="btn filter" type="submit">Apply filters</button><a class="btn reset" href="<?= site_url('master/mcqs/submissions') ?>">Reset</a></div>
  </form>
</section>
<section class="panel">
  <div class="summary"><div><h2 class="section-title">Submitted questions</h2><div class="section-note">Only submitted records are shown. Approval and final import remain in your local system.</div></div><span class="count"><span class="count-dot"></span><?= count($questions ?? []) ?> matching MCQ<?= count($questions ?? [])===1?'':'s' ?></span></div>
  <div class="table-wrap"><table><thead><tr><th>ID</th><th>Question preview</th><th>Faculty</th><th>Department / Subject</th><th>Module / Topic</th><th>Program</th><th>Submitted details</th><th>Status</th></tr></thead><tbody>
  <?php if(empty($questions)): ?><tr><td colspan="8" class="empty"><div class="empty-icon">⌕</div><div><strong>No matching submissions</strong></div><div class="subtext">Try another department or faculty member, or reset the filters.</div></td></tr><?php endif ?>
  <?php foreach(($questions ?? []) as $q): ?><tr>
    <td><span class="id">#<?= esc($q['mcq_id']) ?></span></td>
    <td><div class="question"><?= esc(mb_strimwidth(trim((string)($q['stem'] ?? '')) !== '' ? (string)$q['stem'] : (string)($q['leadin'] ?? ''),0,145,'…')) ?></div><?php if(trim((string)($q['stem'] ?? '')) !== '' && trim((string)($q['leadin'] ?? '')) !== ''): ?><div class="lead"><?= esc(mb_strimwidth((string)$q['leadin'],0,120,'…')) ?></div><?php endif ?></td>
    <td><div class="faculty-name"><?= esc($q['author_name'] ?? 'Unknown faculty') ?></div><div class="subtext">Faculty ID <?= esc($q['author_id'] ?? '') ?></div></td>
    <td><div><?= esc($q['department_name'] ?? '—') ?></div><div class="subtext"><?= esc($q['subject_name'] ?? '—') ?></div></td>
    <td><div><?= esc($q['system'] ?? '—') ?></div><div class="subtext"><?= esc($q['topic'] ?? '—') ?></div></td>
    <td><?= esc($q['progName'] ?? '—') ?></td>
    <td><span class="pill-light"><?= esc($q['difficulty'] ?? '—') ?></span><div class="subtext">Competency <?= esc($q['competency'] ?? '—') ?><br><?= esc($q['mdate'] ?? '—') ?></div></td>
    <td><span class="status">Submitted</span></td>
  </tr><?php endforeach ?>
  </tbody></table></div>
  <p class="footer-note">CSV export respects the selected department, faculty member, and search text. The export includes only submitted MCQs and never changes their status.</p>
</section>
</main>
<script>
// Changing department refreshes the available faculty list; selected department is retained.
document.getElementById('department_id')?.addEventListener('change', function(){
  const faculty = document.getElementById('author_id'); if (faculty) faculty.value = '';
  document.getElementById('submissionFilters')?.requestSubmit();
});
</script>
</body></html>
