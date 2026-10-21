<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($pageTitle ?? 'Change Password — MCQ Bank') ?></title>
<style>
:root{--navy:#103765;--blue:#1766ae;--ink:#183454;--muted:#607995;--line:#c9def4;--bg:#f1f7ff}
*{box-sizing:border-box}body{margin:0;min-height:100vh;font-family:"Segoe UI",Arial,sans-serif;color:var(--ink);background:radial-gradient(ellipse at 12% 0%,#e0f5ff 0,transparent 42%),radial-gradient(ellipse at 100% 15%,#e9eaff 0,transparent 38%),var(--bg);display:flex;flex-direction:column}
header{padding:18px clamp(18px,4vw,44px);background:linear-gradient(110deg,#fff,#e2f1ff);border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:16px}.brand{font-family:Georgia,"Times New Roman",serif;font-size:clamp(20px,2.4vw,30px);font-weight:700;color:#102e62}.campus{font-family:Georgia,"Times New Roman",serif;color:#0872cb;font-size:16px}.workspace{font-size:12px;color:#58708e;margin-top:7px}.back{display:inline-flex;align-items:center;justify-content:center;text-decoration:none;color:#164b82;background:#fff;border:2px solid #8fc9f4;border-radius:24px;padding:12px 22px;font-weight:800;box-shadow:0 6px 16px #165a9418;transition:.18s}.back:hover{background:#e8f5ff;transform:translateY(-2px);box-shadow:0 9px 20px #165a9426}
main{width:min(560px,calc(100% - 28px));margin:clamp(22px,6vh,56px) auto;flex:1}.panel{background:#ffffffed;border:1px solid #d5e5f7;border-radius:22px;overflow:hidden;box-shadow:0 14px 38px #214b7510}.panel-head{padding:21px 25px;background:linear-gradient(110deg,#fafdff,#eaf4ff);border-bottom:1px solid #dce9f8}.panel-head h1{margin:0;color:#123c70;font-size:24px}.panel-head p{margin:7px 0 0;color:var(--muted);font-size:13px}.form{padding:24px 25px 26px}.field{margin-bottom:17px}.field label{display:block;font-size:13px;font-weight:700;margin-bottom:7px;color:#234b76}.field input{width:100%;height:44px;border:1px solid #c4d8f1;border-radius:11px;padding:10px 12px;font:inherit;color:#183454;background:#fff;outline:none}.field input:focus{border-color:#388fe0;box-shadow:0 0 0 3px #d9edff}.hint{font-size:12px;color:#6a819b;margin-top:6px}.actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:23px}.submit{border:0;border-radius:12px;padding:13px 20px;background:linear-gradient(100deg,#1766ae,#183f9a);color:#fff;font-size:14px;font-weight:800;cursor:pointer;box-shadow:0 7px 16px #145db52b}.submit:hover{filter:brightness(1.07);transform:translateY(-1px)}.cancel{display:inline-flex;align-items:center;text-decoration:none;padding:12px 18px;border-radius:12px;background:#edf3fa;color:#284968;font-weight:700;font-size:13px}.alert{margin:18px 25px 0;padding:12px 14px;border-radius:11px;font-size:13px;line-height:1.5}.error{background:#fff0f0;border:1px solid #ffc9c9;color:#a12222}.success{background:#e9fbf2;border:1px solid #a9e8c8;color:#14643d}.foot{text-align:center;color:#8497ad;font-size:12px;padding:0 16px 22px}
@media(max-width:560px){header{align-items:flex-start;flex-direction:column}.back{align-self:flex-start}.panel-head,.form{padding-left:18px;padding-right:18px}.panel-head h1{font-size:21px}}
</style>
</head>
<body>
<header>
 <div><div class="brand">Riphah International University</div><div class="campus">(Peshawar Campus)</div><div class="workspace">MCQ Bank · Teaching Faculty Workspace</div></div>
 <a class="back" href="<?= site_url('dashboard/member') ?>">Back to Dashboard</a>
</header>
<main>
 <section class="panel">
  <div class="panel-head"><h1>Change Password</h1><p>Choose a new password for your faculty account.</p></div>
  <?php if (! empty($errorMessage)): ?><div class="alert error"><?= esc($errorMessage) ?></div><?php endif; ?>
  <?php if (! empty($successMessage)): ?><div class="alert success"><?= esc($successMessage) ?></div><?php endif; ?>
  <form class="form" method="post" action="<?= site_url('master/change-password') ?>" autocomplete="off">
   <?= csrf_field() ?>
   <div class="field"><label for="current_password">Current Password *</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required maxlength="255"></div>
   <div class="field"><label for="new_password">New Password *</label><input id="new_password" name="new_password" type="password" autocomplete="new-password" required minlength="8" maxlength="255"><div class="hint">Use at least 8 characters.</div></div>
   <div class="field"><label for="confirm_password">Confirm New Password *</label><input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required minlength="8" maxlength="255"></div>
   <div class="actions"><button class="submit" type="submit">Save New Password</button><a class="cancel" href="<?= site_url('dashboard/member') ?>">Cancel</a></div>
  </form>
 </section>
</main>
<div class="foot">MCQ Bank · Teaching Faculty Workspace</div>
</body>
</html>
