<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($pageTitle ?? 'Program–Department Mapping') ?></title>
<style>
:root{--navy:#10233f;--blue:#2457a7;--ink:#1e293b;--muted:#64748b;--line:#dbe5f2;--bg:#f3f6fb;--green:#087847;--red:#b42318}
*{box-sizing:border-box}body{margin:0;background:var(--bg);font-family:Segoe UI,Arial,sans-serif;color:var(--ink)}
.top{background:linear-gradient(115deg,#10233f,#1d4d83);color:#fff;padding:22px clamp(16px,4vw,48px);display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}.brand{font-size:12px;letter-spacing:1px;color:#c5def8;font-weight:700}.top h1{font-size:clamp(22px,3vw,30px);margin:7px 0 0}.top a{color:#123c78;text-decoration:none;background:#fff;border-radius:9px;padding:10px 14px;font-weight:700;font-size:13px}
.wrap{max-width:1180px;margin:24px auto;padding:0 18px}.intro h2{margin:0 0 5px;color:#143b82;font-size:23px}.intro p{margin:0 0 18px;color:var(--muted);font-size:14px}.panel{background:#fff;border:1px solid var(--line);border-radius:15px;box-shadow:0 5px 18px #10233f08;padding:20px;margin-bottom:18px}.field-label{display:block;font-size:13px;font-weight:800;margin-bottom:8px;color:#29466f}.program-select{width:100%;max-width:540px;padding:12px;border:1px solid #bfd0e5;border-radius:9px;background:#fff;font-size:15px;color:#1e293b}.toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px}.toolbar h3{margin:0;color:#143b82;font-size:17px}.toolbar input{width:min(100%,330px);padding:10px 12px;border:1px solid #cbd8e8;border-radius:8px;font-size:14px}.department-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.department{display:flex;align-items:flex-start;gap:10px;padding:12px;border:1px solid #dbe5f2;border-radius:10px;background:#fbfdff;min-height:58px}.department:hover{border-color:#94b6e5;background:#f4f8ff}.department input{margin-top:3px;width:17px;height:17px;accent-color:#2457a7;flex:0 0 auto}.department span{font-size:14px;line-height:1.4}.btn{border:0;border-radius:9px;padding:11px 16px;font-size:14px;font-weight:800;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}.btn-primary{background:#2457a7;color:#fff}.btn-light{background:#edf4ff;color:#194c91;border:1px solid #d1e1f6}.actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:18px}.notice{padding:12px 14px;border-radius:9px;margin:0 0 15px;font-size:14px}.success{background:#eaf8f0;border:1px solid #b7e4c9;color:#17643b}.error{background:#fff0ef;border:1px solid #f3c4c0;color:#a3231a}.hint{font-size:12px;color:var(--muted);margin-top:10px}.count{font-size:12px;color:#315887;background:#edf4ff;padding:6px 9px;border-radius:20px;font-weight:700}.empty{color:var(--muted);padding:18px;border:1px dashed #cbd8e8;border-radius:10px;background:#fafcff}.foot{text-align:center;color:#94a3b8;font-size:11px;padding:18px}
@media(max-width:850px){.department-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:520px){.department-grid{grid-template-columns:1fr}.panel{padding:14px}.program-select{max-width:100%}}
</style>
</head>
<body>
<header class="top"><div><div class="brand">Riphah International University (Peshawar Campus)</div><h1>Program–Department Mapping</h1></div><a href="<?= site_url('dashboard/admin') ?>">← Admin Dashboard</a></header>
<main class="wrap">
 <div class="intro"><h2>Assign Departments to Programs</h2><p>A department can belong to multiple programs. Unchecking a department deactivates its mapping without deleting the record or changing MCQs.</p></div>
 <?php if (!empty($success)): ?><div class="notice success"><?= esc($success) ?></div><?php endif; ?>
 <?php if (!empty($error)): ?><div class="notice error"><?= esc($error) ?></div><?php endif; ?>
 <section class="panel">
  <form method="get" action="<?= site_url('master/program-departments') ?>">
   <label class="field-label" for="program_id">1. Select Program</label>
   <select class="program-select" id="program_id" name="program_id" required onchange="this.form.submit()">
    <option value="">— Choose a Program —</option>
    <?php foreach ($programs as $program): ?><option value="<?= (int) $program['progId'] ?>" <?= (int) $selectedProgramId === (int) $program['progId'] ? 'selected' : '' ?>><?= esc($program['progName']) ?></option><?php endforeach; ?>
   </select>
   <noscript><button class="btn btn-light" type="submit">Load Program</button></noscript>
  </form>
 </section>
 <?php if ($selectedProgramId > 0): ?>
 <form method="post" action="<?= site_url('master/program-departments') ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="progId" value="<?= (int) $selectedProgramId ?>">
  <section class="panel">
   <div class="toolbar"><h3>2. Select Departments</h3><span class="count" id="selectedCount">0 selected</span><input type="search" id="departmentSearch" placeholder="Search departments…" aria-label="Search departments"></div>
   <?php if (empty($departments)): ?><div class="empty">No departments were found. Add departments first, then return to this page.</div>
   <?php else: ?><div class="department-grid" id="departmentGrid">
    <?php foreach ($departments as $department): $checked = in_array((int) $department['department_id'], $activeDepartmentIds, true); ?>
     <label class="department" data-name="<?= esc(mb_strtolower($department['department_name']), 'attr') ?>"><input type="checkbox" name="departments[]" value="<?= (int) $department['department_id'] ?>" <?= $checked ? 'checked' : '' ?>><span><?= esc($department['department_name']) ?></span></label>
    <?php endforeach; ?>
   </div><?php endif; ?>
   <div class="actions"><button class="btn btn-primary" type="submit">Save Department Mapping</button><button class="btn btn-light" type="button" id="selectVisible">Select Visible</button><button class="btn btn-light" type="button" id="clearVisible">Clear Visible</button><a class="btn btn-light" href="<?= site_url('dashboard/admin') ?>">Cancel</a></div>
   <div class="hint">Only this Program’s mappings are updated. Other Programs and existing Department, Subject, and MCQ records remain unchanged.</div>
  </section>
 </form>
 <?php else: ?><section class="panel"><div class="empty">Choose a Program above to view and edit its Department assignments.</div></section><?php endif; ?>
 <div class="foot">MCQ Bank · Academic Master Data</div>
</main>
<script>
(function(){
 const search=document.getElementById('departmentSearch');
 const grid=document.getElementById('departmentGrid');
 const count=document.getElementById('selectedCount');
 function visibleLabels(){return Array.from(document.querySelectorAll('.department')).filter(el=>el.style.display!=='none');}
 function updateCount(){if(!count)return;count.textContent=document.querySelectorAll('.department input:checked').length+' selected';}
 if(search&&grid){search.addEventListener('input',function(){const q=this.value.trim().toLocaleLowerCase();document.querySelectorAll('.department').forEach(el=>{el.style.display=el.dataset.name.includes(q)?'flex':'none';});});}
 document.querySelectorAll('.department input').forEach(el=>el.addEventListener('change',updateCount));
 const select=document.getElementById('selectVisible');if(select)select.addEventListener('click',()=>{visibleLabels().forEach(el=>{const cb=el.querySelector('input');cb.checked=true;});updateCount();});
 const clear=document.getElementById('clearVisible');if(clear)clear.addEventListener('click',()=>{visibleLabels().forEach(el=>{const cb=el.querySelector('input');cb.checked=false;});updateCount();});
 updateCount();
})();
</script>
</body></html>
