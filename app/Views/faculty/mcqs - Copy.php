<?php
$isEditing = isset($editing) && is_array($editing);
$editing = $editing ?? [];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($pageTitle ?? 'MCQ Entry & Drafts') ?></title>
<style>
:root{--navy:#102b55;--blue:#1767bd;--cyan:#28b9d9;--purple:#635bdb;--ink:#1f2e45;--muted:#62738d;--line:#dbe6f5;--bg:#f3f7fd}
*{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at 10% 0,#e1f5ff 0,transparent 32%),linear-gradient(135deg,#f7fbff,#f3f2ff);font-family:Segoe UI,Arial,sans-serif;color:var(--ink)}
.header{padding:18px clamp(16px,4vw,48px);background:linear-gradient(110deg,#fafdff,#e4f2ff 62%,#d6e8ff);border-bottom:1px solid #cbdff6;display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap}
.brand{font-family:Georgia,serif;font-weight:700;font-size:clamp(19px,2.3vw,29px);color:#12336a}.campus{font-family:Georgia,serif;color:#1767bd;font-size:17px}.subtitle{color:#55739b;margin-top:6px;font-size:13px}
.profile{background:linear-gradient(120deg,#1c5592,#12315b);color:#fff;padding:12px 20px;border-radius:18px;min-width:245px;text-align:right;box-shadow:0 8px 22px #173e6b20}.name{font-size:22px;font-weight:800}.meta{font-size:13px;color:#d8eaff;line-height:1.6}.profile a{color:#fff;font-size:12px}
.wrap{max-width:1500px;margin:8px auto;padding:0 14px 16px}.page-head{display:flex;justify-content:space-between;align-items:end;gap:12px;flex-wrap:wrap;margin-bottom:16px}.page-head h1{margin:0;font-size:25px;color:#143867}.page-head p{margin:6px 0 0;color:var(--muted);font-size:14px}
.alert{border-radius:12px;padding:12px 15px;margin:0 0 14px;font-size:14px}.success{background:#e8fbf4;border:1px solid #b9ebd8;color:#12634d}.error{background:#fff0f0;border:1px solid #f3c5c5;color:#9b2222}
.toolbar{position:fixed;right:26px;bottom:34px;z-index:1000;margin:0}.toolbar .btn-dashboard{width:108px;height:108px;padding:10px;border-radius:50%;background:linear-gradient(145deg,#ffffff 0%,#e7f4ff 100%);color:#174b82;border:4px solid transparent;background-image:linear-gradient(145deg,#ffffff,#e7f4ff),linear-gradient(135deg,#36a8ff,#36a45b);background-origin:border-box;background-clip:padding-box,border-box;box-shadow:0 12px 26px #174c8b26;font-weight:900;font-size:16px;letter-spacing:.4px;text-transform:uppercase;display:flex;align-items:center;justify-content:center;transition:transform .2s ease,box-shadow .2s ease,background .2s ease}.toolbar .btn-dashboard:hover{background-image:linear-gradient(145deg,#f4fbff,#d8edff),linear-gradient(135deg,#2498f4,#278d49);box-shadow:0 15px 30px #174c8b35;transform:translateY(-3px) scale(1.03)}.toolbar .btn-dashboard:focus-visible{outline:3px solid #72b8f0;outline-offset:4px}@media(max-width:560px){.toolbar{right:16px;bottom:18px}.toolbar .btn-dashboard{width:84px;height:84px;font-size:13px;border-width:3px}}.layout{display:grid;grid-template-columns:minmax(0,2.1fr) minmax(260px,.9fr);gap:12px;align-items:start}.panel{background:#ffffffed;border:1px solid var(--line);border-radius:18px;box-shadow:0 10px 30px #153d6d0a;overflow:hidden}.panel-title{padding:8px 12px;background:linear-gradient(100deg,#e8f6ff,#f2f3ff);border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:12px}.panel-title h2{margin:0;font-size:14px;color:#173967}.panel-title small{color:var(--muted)}.panel-body{padding:10px}
.form-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:8px}.form-section{grid-column:1/-1;display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:8px;border:2px solid #171717;border-radius:10px;padding:8px;margin:0 0 1px;min-width:0}.field{display:flex;flex-direction:column;gap:3px;min-width:0}.field.full,.field.learning-objective{grid-column:1/-1}.field.scenario{grid-column:span 5}.field.scenario-side{grid-column:span 1}.field.language{grid-column:span 3}.field.full-width{grid-column:1/-1}.secondary-section{background:#fbfdff;border-style:dashed}.secondary-section[hidden]{display:none}.secondary-heading{grid-column:1/-1;font-size:12px;font-weight:800;color:#1767bd;margin:2px 0}.secondary-row{grid-column:1/-1;display:grid;grid-template-columns:105px minmax(0,1fr);align-items:start;gap:8px}.secondary-row label{padding-top:8px}.field.leadin-main{grid-column:span 5}.field.leadin-side{grid-column:span 1}.field.subject,.field.subdiscipline,.field.program{grid-column:span 2}.field.system{grid-column:span 2}.field.topic{grid-column:span 4}.field.key{grid-column:span 4}.field.competency,.field.difficulty{grid-column:span 1}.field.book-title,.field.book-author{grid-column:span 2}.field.edition,.field.page-no{grid-column:span 1}.field.competency input{width:100%;max-width:100%}.field.difficulty select{width:100%;min-width:0}.options-note{grid-column:1/-1;margin-top:-4px;color:#1767bd;font-size:12px}.option-row{grid-column:1/-1;display:grid;grid-template-columns:105px minmax(0,1fr) 42px;align-items:center;gap:8px}.option-row label{font-size:13px;color:#173967}.option-row input[type=text]{min-height:34px;padding:6px 9px}.key-choice{display:inline-flex;align-items:center;justify-content:center;width:38px;height:34px;border:1px solid #cbd8e9;border-radius:8px;background:#fff;color:#24405f;font-weight:800;cursor:pointer;user-select:none;position:relative}.key-choice input{position:absolute;opacity:0;width:1px;height:1px}.key-choice:has(input:checked){background:#dff5e4;color:#226b36;border-color:#79c88a;box-shadow:0 0 0 2px #79c88a25}.key-choice:focus-within{outline:2px solid #79c88a;outline-offset:2px}.program-hint{display:none}.image-upload{grid-column:span 2}.image-control{display:flex;align-items:center;gap:9px;min-width:0}.image-thumb-button{width:58px;height:54px;flex:0 0 58px;border:1px dashed #8eafd4;border-radius:9px;background:#f3f8ff;padding:3px;cursor:pointer;display:flex;align-items:center;justify-content:center;overflow:hidden}.image-thumb-button img{width:100%;height:100%;object-fit:cover;border-radius:6px}.image-thumb-button span{text-align:center;color:#1767bd;font-size:22px;line-height:1}.image-thumb-button small{display:block;font-size:9px}.image-actions{display:flex;align-items:center;gap:6px;flex-wrap:wrap}.image-actions .btn{padding:6px 9px}.image-modal{position:fixed;inset:0;background:#07152bd9;z-index:3000;display:flex;align-items:center;justify-content:center;padding:24px}.image-modal[hidden]{display:none}.image-modal img{max-width:min(94vw,1200px);max-height:90vh;object-fit:contain;background:white;border-radius:8px}.image-modal button{position:absolute;top:18px;right:22px;background:white;border:0;border-radius:8px;padding:9px 14px;cursor:pointer;font-weight:700}@media(max-width:760px){.image-upload{grid-column:1/-1}}label{font-weight:700;font-size:12px;color:#405673}input,select,textarea{width:100%;border:1px solid #cbd8e9;border-radius:10px;padding:7px 9px;background:#fff;color:#24364f;font:14px Segoe UI,Arial,sans-serif;outline:none;transition:.18s}textarea{min-height:54px;resize:vertical}input:focus,select:focus,textarea:focus{border-color:#57a9e7;box-shadow:0 0 0 3px #5eb7ed25}.hint{font-size:11px;color:#7a8aa1}.section-label{grid-column:1/-1;font-size:11px;color:#1c70ad;font-weight:800;border-bottom:1px solid #e5edf7;padding-bottom:3px;margin-top:1px;display:none}
.actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-top:10px}.btn{border:0;border-radius:10px;padding:8px 14px;font-weight:750;font-size:12px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:.18s}.btn:hover{transform:translateY(-2px);filter:brightness(1.03)}.btn-draft{background:#e9f3ff;color:#18549a;border:1px solid #bfdafa}.btn-submit{background:linear-gradient(110deg,#167acb,#2446b9);color:#fff;box-shadow:0 6px 15px #2362bd2a}.btn-muted{background:#f0f3f8;color:#42556f}
.table-wrap{overflow:auto}.draft-table{width:100%;border-collapse:collapse;font-size:12px}.draft-table th{text-align:left;background:#f7faff;color:#536984;padding:11px;border-bottom:1px solid var(--line)}.draft-table td{padding:11px;border-bottom:1px solid #edf1f7;vertical-align:top}.draft-title{max-width:240px;line-height:1.45}.edit-link{display:inline-block;background:#e9f3ff;color:#155da8;padding:6px 9px;border-radius:7px;text-decoration:none;font-weight:700}.empty{padding:24px;text-align:center;color:#77879d;font-size:13px}.badge{display:inline-block;padding:4px 7px;border-radius:20px;background:#fff4dd;color:#98610b;font-size:10px;font-weight:800}.required{color:#c03535}.small-note{margin-top:6px;color:#74839a;font-size:11px;line-height:1.5}
@media(max-width:1000px){.layout{grid-template-columns:1fr}}@media(max-width:760px){.form-grid,.form-section{grid-template-columns:repeat(2,minmax(0,1fr))}.field.subject,.field.subdiscipline,.field.program,.field.system,.field.topic,.field.key,.field.competency,.field.difficulty,.field.book-title,.field.book-author,.field.edition,.field.page-no{grid-column:span 1}.field.full,.field.learning-objective,.field.scenario,.field.scenario-side,.field.leadin-main,.field.leadin-side,.field.language,.field.full-width,.options-note,.option-row,.secondary-row,.secondary-heading{grid-column:1/-1}.field.difficulty select{width:100%;max-width:100%}.option-row{grid-template-columns:90px minmax(0,1fr) 38px}}@media(max-width:560px){.form-grid,.form-section{grid-template-columns:1fr}.field,.field.subject,.field.subdiscipline,.field.program,.field.system,.field.topic,.field.key,.field.competency,.field.difficulty,.field.book-title,.field.book-author,.field.edition,.field.page-no,.field.full,.field.learning-objective,.field.scenario,.field.scenario-side,.field.leadin-main,.field.leadin-side,.field.language,.field.full-width,.options-note,.option-row,.secondary-row,.secondary-heading{grid-column:1/-1}.field.difficulty select{width:100%;max-width:100%}.option-row{grid-template-columns:1fr 38px;gap:4px}.option-row label{grid-column:1/-1}}
</style>
</head>
<body>
<main class="wrap">
 <div class="toolbar"><a class="btn btn-dashboard" href="<?= site_url('dashboard/member') ?>">BACK</a></div>
 <?php if (! empty($success)): ?><div class="alert success"><?= esc($success) ?></div><?php endif; ?>
 <?php if (session()->getFlashdata('error')): ?><div class="alert error"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
 <?php if (! empty($errors)): ?><div class="alert error"><strong>Please correct the following:</strong><ul style="margin:7px 0 0;padding-left:20px"><?php foreach ($errors as $message): ?><li><?= esc($message) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
 <div class="layout">
  <section class="panel">
   <div class="panel-title"><h2><?= $isEditing ? 'Edit Draft' : 'MCQ Entry Form' ?></h2><small>Fields marked * are required</small></div>
   <div class="panel-body">
    <form method="post" enctype="multipart/form-data" action="<?= site_url('master/faculty/mcqs/save') ?>">
     <?= csrf_field() ?>
     <?php if ($isEditing): ?><input type="hidden" name="editing_id" value="<?= (int)($editing['mcq_id'] ?? 0) ?>"><?php endif; ?>
     <?php $val = static function($key, $default = '') use ($editing) { return old($key, ($key === 'program_id' ? ($editing['program_id'] ?? $editing['progId'] ?? $default) : ($editing[$key] ?? $default))); }; ?>
     <div class="form-grid">
      <div class="form-section academic-section">
      <div class="field subject"><label>Subject</label><input value="<?= esc($subject['subject_name'] ?? 'Subject not assigned') ?>" readonly><span class="hint">Your assigned subject is fixed by the administrator.</span></div>
      <div class="field subdiscipline"><label for="subdiscipline_id">Sub Discipline</label><select id="subdiscipline_id" name="subdiscipline_id"><option value="">Select if applicable</option></select><span class="hint">Optional only for subjects without sub-disciplines.</span></div>
      <div class="field program"><label for="program_id">Program <span class="required">*</span></label><select id="program_id" name="program_id" required><option value="">Select Program</option><?php foreach (($programs ?? []) as $p): ?><option value="<?= (int)($p['progId'] ?? 0) ?>" <?= (string)$val('program_id') === (string)($p['progId'] ?? '') ? 'selected' : '' ?>><?= esc($p['progName'] ?? ('Program '.($p['progId'] ?? ''))) ?></option><?php endforeach; ?></select><span class="hint program-hint"><?= empty($programs) ? 'No program assignments found. Please contact the administrator.' : 'Only programs assigned to your faculty account are listed.' ?></span></div>
      <div class="field system"><label for="system_id">Module/Block <span class="required">*</span></label><select id="system_id" name="system_id" required><option value="">Select Module/Block</option><?php foreach (($systems ?? []) as $item): ?><option value="<?= (int)$item['system_id'] ?>" <?= (string)$val('system_id', $editing['system_id'] ?? '') === (string)$item['system_id'] ? 'selected' : '' ?>><?= esc($item['system']) ?></option><?php endforeach; ?></select></div>
      <div class="field topic"><label for="topic_id">Topic <span class="required">*</span></label><select id="topic_id" name="topic_id" required><option value="">Select Topic</option></select></div>
      </div>
      <div class="form-section question-section">
       <div class="field language"><label for="primary_language">Primary Language <span class="required">*</span></label><select id="primary_language" name="primary_language" required><option value="English" <?= $val('primary_language','English') === 'English' ? 'selected' : '' ?>>English</option><option value="Urdu" <?= $val('primary_language','English') === 'Urdu' ? 'selected' : '' ?>>Urdu</option><option value="Arabic" <?= $val('primary_language','English') === 'Arabic' ? 'selected' : '' ?>>Arabic</option></select></div>
       <div class="field language"><label for="secondary_language">Secondary Language (Optional)</label><select id="secondary_language" name="secondary_language"><option value="">None</option><option value="English" <?= $val('secondary_language') === 'English' ? 'selected' : '' ?>>English</option><option value="Urdu" <?= $val('secondary_language') === 'Urdu' ? 'selected' : '' ?>>Urdu</option><option value="Arabic" <?= $val('secondary_language') === 'Arabic' ? 'selected' : '' ?>>Arabic</option></select></div>
       <div class="field full-width"><span class="hint">Choose one primary language. A second language is optional and applies to the whole MCQ.</span></div>
       <div class="field scenario"><label for="stem">Stem / Scenario (Optional)</label><textarea id="stem" name="stem" placeholder="Enter the clinical/contextual stem or scenario, if applicable..."><?= esc(old('stem', trim((string)($editing['stem'] ?? '') . "\n" . (string)($editing['scenario'] ?? '')))) ?></textarea></div>
       <div class="field scenario-side"><label for="difficulty">Difficulty <span class="required">*</span></label><select id="difficulty" name="difficulty" required><?php foreach (['Easy','Moderate','Difficult'] as $d): ?><option value="<?= $d ?>" <?= $val('difficulty','Moderate') === $d ? 'selected' : '' ?>><?= $d ?></option><?php endforeach; ?></select></div>
       <div class="field image-upload"><label for="mcq_image">MCQ Image (optional)</label><div class="image-control"><button type="button" class="image-thumb-button" id="imagePick" aria-label="Choose or preview MCQ image"><img id="imageThumb" alt="MCQ image thumbnail" src="<?= !empty($editing['image_path']) ? base_url($editing['image_path']) : '' ?>" <?= empty($editing['image_path']) ? 'hidden' : '' ?>><span id="imagePlaceholder" <?= !empty($editing['image_path']) ? 'hidden' : '' ?>>＋<small>Add image</small></span></button><div class="image-actions"><input type="file" id="mcq_image" name="mcq_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" hidden><button type="button" class="btn btn-draft" id="chooseImage">Choose image</button><button type="button" class="btn btn-muted" id="removeImage" <?= empty($editing['image_path']) ? 'hidden' : '' ?>>Remove</button><span class="hint">JPG, PNG, WebP · max 6 MB</span></div></div><input type="hidden" name="remove_mcq_image" id="remove_mcq_image" value="0"></div>
       <div class="field leadin-main"><label for="leadin">Lead-in / Question <span class="required">*</span></label><textarea id="leadin" name="leadin" required placeholder="Enter a clear, focused question..."><?= esc($val('leadin')) ?></textarea></div>
       <div class="field leadin-side"><label for="competency">Competency (1–4) <span class="required">*</span></label><input type="number" id="competency" name="competency" min="1" max="4" step="1" required value="<?= esc($val('competency', '1')) ?>"></div>
       <?php foreach (['option1'=>'A','option2'=>'B','option3'=>'C','option4'=>'D','option5'=>'E'] as $key=>$letter): ?>
       <div class="option-row"><label for="<?= esc($key) ?>">Option <?= $letter ?> *</label><input type="text" id="<?= esc($key) ?>" name="<?= esc($key) ?>" value="<?= esc($val($key)) ?>" required maxlength="255" placeholder="Enter option text"><label class="key-choice" title="Set Option <?= $letter ?> as the correct answer"><input type="radio" name="mkey" value="<?= $letter ?>" <?= $val('mkey') === $letter ? 'checked' : '' ?> required aria-label="Correct answer <?= $letter ?>"><span><?= $letter ?></span></label></div>
       <?php endforeach; ?>
       <div class="options-note">Options must be sorted alphabetically.</div>
      </div>

      <div class="form-section secondary-section" id="secondaryFields" <?= $val('secondary_language') === '' ? 'hidden' : '' ?>>
       <div class="secondary-heading">Secondary Language Content</div>
       <div class="secondary-row"><label for="stem_secondary">Stem / Scenario</label><textarea id="stem_secondary" name="stem_secondary" placeholder="Secondary-language stem or scenario..."><?= esc(old('stem_secondary', trim((string)($editing['stem_secondary'] ?? '') . "\n" . (string)($editing['scenario_secondary'] ?? '')))) ?></textarea></div>
       <div class="secondary-row"><label for="leadin_secondary">Lead-in / Question</label><textarea id="leadin_secondary" name="leadin_secondary" placeholder="Secondary-language lead-in..."><?= esc($val('leadin_secondary')) ?></textarea></div>
       <?php foreach (['option1'=>'A','option2'=>'B','option3'=>'C','option4'=>'D','option5'=>'E'] as $key=>$letter): ?>
       <div class="secondary-row"><label for="<?= esc($key) ?>_secondary">Option <?= $letter ?></label><input type="text" id="<?= esc($key) ?>_secondary" name="<?= esc($key) ?>_secondary" maxlength="255" value="<?= esc($val($key.'_secondary')) ?>" placeholder="Secondary-language option <?= $letter ?>"></div>
       <?php endforeach; ?>
      </div>

            <div class="form-section reference-section">
      <div class="field book-title"><label for="book_title">Book Title</label><input id="book_title" name="book_title" maxlength="255" value="<?= esc($val('book_title')) ?>"></div>
      <div class="field book-author"><label for="name_of_bookauthor">Book Author</label><input id="name_of_bookauthor" name="name_of_bookauthor" maxlength="255" value="<?= esc($val('name_of_bookauthor')) ?>"></div>
      <div class="field edition"><label for="edition">Edition</label><input id="edition" name="edition" maxlength="100" value="<?= esc($val('edition')) ?>"></div>
      <div class="field page-no"><label for="page_no">Page No.</label><input id="page_no" name="page_no" maxlength="10" value="<?= esc($val('page_no')) ?>"></div>
      <div class="field learning-objective"><label for="learning_objective">Learning Objective</label><textarea id="learning_objective" name="learning_objective"><?= esc($val('learning_objective')) ?></textarea></div>
      </div>
     </div>
     <div class="actions">
      <button class="btn btn-draft" type="submit" name="action" value="draft">▣ Save Draft</button>
      <button class="btn btn-submit" type="submit" name="action" value="submit" onclick="return confirm('Submit this MCQ for administrative review? You will no longer be able to edit it as a draft.');">➤ Submit for Review</button>
      <?php if ($isEditing): ?><a class="btn btn-muted" href="<?= site_url('master/faculty/mcqs') ?>">Cancel Edit</a><?php endif; ?>
     </div>
    </form>
   </div>
  </section>
  <aside class="panel">
   <div class="panel-title"><h2>My Drafts</h2><span class="badge"><?= count($drafts ?? []) ?> saved</span></div>
   <?php if (empty($drafts)): ?><div class="empty">No saved drafts yet.<br>Save your first MCQ draft using the form.</div>
   <?php else: ?>
   <div class="table-wrap"><table class="draft-table"><thead><tr><th>Question</th><th>Updated</th><th></th></tr></thead><tbody>
   <?php foreach ($drafts as $draft): ?><tr><td><div class="draft-title"><?= esc(mb_strimwidth(strip_tags((string)$draft['stem']),0,105,'…')) ?></div><div class="hint">Draft #<?= (int)$draft['mcq_id'] ?></div></td><td><?= esc($draft['mdate'] ?? '') ?></td><td><a class="edit-link" href="<?= site_url('master/faculty/mcqs/edit/'.(int)$draft['mcq_id']) ?>">Edit</a></td></tr><?php endforeach; ?>
   </tbody></table></div>
   <?php endif; ?>
   <div class="small-note" style="padding:0 16px 16px">Only saved drafts that have not been submitted are shown here.</div>
  </aside>
 </div>
</main>
<div class="image-modal" id="imageModal" hidden role="dialog" aria-modal="true" aria-label="MCQ image preview"><button type="button" id="closeImageModal">Close ✕</button><img id="imageLarge" alt="Enlarged MCQ image"></div>
<script>

const imageInput = document.getElementById('mcq_image');
const imageThumb = document.getElementById('imageThumb');
const imagePlaceholder = document.getElementById('imagePlaceholder');
const imageModal = document.getElementById('imageModal');
const imageLarge = document.getElementById('imageLarge');
const removeImageFlag = document.getElementById('remove_mcq_image');
const removeImageBtn = document.getElementById('removeImage');
function showImageModal(){ if(!imageThumb.hidden && imageThumb.src){imageLarge.src=imageThumb.src;imageModal.hidden=false;} }
document.getElementById('chooseImage').addEventListener('click',()=>imageInput.click());
document.getElementById('imagePick').addEventListener('click',()=>{ if(!imageThumb.hidden) showImageModal(); else imageInput.click(); });
imageThumb.addEventListener('click',showImageModal);
imageInput.addEventListener('change',()=>{const f=imageInput.files[0];if(!f)return;if(!['image/jpeg','image/png','image/webp'].includes(f.type)||f.size>6*1024*1024){alert('Choose a JPG, PNG, or WebP image up to 6 MB.');imageInput.value='';return;}imageThumb.src=URL.createObjectURL(f);imageThumb.hidden=false;imagePlaceholder.hidden=true;removeImageBtn.hidden=false;removeImageFlag.value='0';});
removeImageBtn.addEventListener('click',()=>{imageInput.value='';imageThumb.removeAttribute('src');imageThumb.hidden=true;imagePlaceholder.hidden=false;removeImageBtn.hidden=true;removeImageFlag.value='1';});
document.getElementById('closeImageModal').addEventListener('click',()=>imageModal.hidden=true);
imageModal.addEventListener('click',e=>{if(e.target===imageModal)imageModal.hidden=true;});
document.addEventListener('keydown',e=>{if(e.key==='Escape')imageModal.hidden=true;});

const subjectId = <?= (int)($subject['subject_id'] ?? 0) ?>;
const selectedSystem = <?= (int)($editing['system_id'] ?? 0) ?>;
const selectedTopic = <?= (int)($editing['topic_id'] ?? 0) ?>;
const selectedSubdiscipline = <?= (int)($editing['subdiscipline_id'] ?? 0) ?>;
const systemsUrl = <?= json_encode(site_url('master/faculty/mcqs/systems-by-subject')) ?>;
const topicsUrl = <?= json_encode(site_url('master/faculty/mcqs/topics-by-system')) ?>;
const disciplinesUrl = <?= json_encode(site_url('master/subdisciplines-by-subject/'.(int)($subject['subject_id'] ?? 0))) ?>;
const primaryLanguageSel = document.getElementById('primary_language');
const secondaryLanguageSel = document.getElementById('secondary_language');
const secondaryFields = document.getElementById('secondaryFields');
function updateSecondaryFields(){
  const selected = secondaryLanguageSel.value;
  secondaryFields.hidden = selected === '';
  Array.from(secondaryLanguageSel.options).forEach(option => {
    if (option.value !== '') option.disabled = option.value === primaryLanguageSel.value;
  });
  if (selected !== '' && selected === primaryLanguageSel.value) {
    secondaryLanguageSel.value = '';
    secondaryFields.hidden = true;
  }
}
primaryLanguageSel.addEventListener('change', updateSecondaryFields);
secondaryLanguageSel.addEventListener('change', updateSecondaryFields);
updateSecondaryFields();

const subSel = document.getElementById('subdiscipline_id');
const sysSel = document.getElementById('system_id');
const topicSel = document.getElementById('topic_id');
async function loadDisciplines(){
 try{
  const r=await fetch(disciplinesUrl,{headers:{'X-Requested-With':'XMLHttpRequest'}});
  const data=await r.json(); const list=data.subdisciplines||data.items||[];
  subSel.innerHTML='<option value="">Select if applicable</option>';
  list.forEach(x=>{const o=document.createElement('option');o.value=x.subdiscipline_id||x.id;o.textContent=x.subdiscipline||x.name;if(Number(o.value)===selectedSubdiscipline)o.selected=true;subSel.appendChild(o);});
  subSel.disabled=list.length===0;
  if(list.length===0) subSel.innerHTML='<option value="">Not applicable for this subject</option>';
 }catch(e){subSel.innerHTML='<option value="">Sub Discipline unavailable</option>';}
}
async function loadSystems(keepSelection=false){
 const old=keepSelection?(sysSel.value||selectedSystem):0;
 const url=new URL(systemsUrl,window.location.origin);url.searchParams.set('subdiscipline_id',subSel.value||'');
 const r=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest'}});
 const data=await r.json();const items=data.systems||[];
 sysSel.innerHTML='<option value="">Select Module/Block</option>';
 items.forEach(x=>{const o=document.createElement('option');o.value=x.id||x.system_id;o.textContent=x.name||x.system;if(Number(o.value)===Number(old))o.selected=true;sysSel.appendChild(o);});
 if(sysSel.value) await loadTopics(keepSelection);
 else topicSel.innerHTML='<option value="">Select Topic first</option>';
}
async function loadTopics(keepSelection=false){
 const old=keepSelection?(topicSel.value||selectedTopic):0;
 topicSel.innerHTML='<option value="">Loading topics…</option>';
 if(!sysSel.value){topicSel.innerHTML='<option value="">Select Topic first</option>';return;}
 const r=await fetch(topicsUrl+'/'+sysSel.value,{headers:{'X-Requested-With':'XMLHttpRequest'}});
 const data=await r.json();topicSel.innerHTML='<option value="">Select Topic</option>';
 (data.topics||[]).forEach(x=>{const o=document.createElement('option');o.value=x.id||x.topic_id;o.textContent=x.name||x.topic;if(Number(o.value)===Number(old))o.selected=true;topicSel.appendChild(o);});
}
subSel.addEventListener('change',()=>loadSystems(false));
sysSel.addEventListener('change',()=>loadTopics(false));
(async()=>{await loadDisciplines();await loadSystems(true);if(!sysSel.value&&selectedSystem){sysSel.value=String(selectedSystem);await loadTopics(true);}})();
</script>
</body>
</html>
