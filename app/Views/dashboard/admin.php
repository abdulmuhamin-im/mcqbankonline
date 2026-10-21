<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($pageTitle ?? 'MCQ Bank — Administration') ?></title>
<style>
:root{--navy:#10233f;--blue:#2457a7;--teal:#0d9488;--ink:#1e293b;--muted:#64748b;--line:#e2e8f0;--bg:#f3f6fb}
*{box-sizing:border-box}body{margin:0;background:var(--bg);font-family:Segoe UI,Arial,sans-serif;color:var(--ink)}
.top{background:linear-gradient(120deg,#10233f,#1d4d83);color:#fff;padding:26px clamp(18px,4vw,52px);display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap}
.brand{font-size:12px;letter-spacing:1.8px;text-transform:uppercase;color:#b9d7f7;font-weight:700}.top h1{margin:7px 0 5px;font-size:clamp(23px,3vw,32px)}.top p{margin:0;color:#d9e8f8}
.role{background:#ffffff1c;border:1px solid #ffffff45;border-radius:30px;padding:9px 14px;font-size:13px}
 .wrap{max-width:1500px;margin:18px auto;padding:0 22px}.intro{display:flex;justify-content:space-between;align-items:end;gap:12px;flex-wrap:wrap;margin-bottom:12px}.intro h2{margin:0 0 4px;font-size:22px}.intro p{margin:0;color:var(--muted);font-size:14px}
.section-panel{background:#ffffffb8;border:1px solid #d8e5f8;border-radius:14px;margin:14px 0;padding:0 12px 12px;box-shadow:0 5px 18px #10233f06}.section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:11px 4px 10px;border-bottom:1px solid #e4edf9;margin-bottom:10px}.section{font-size:18px;letter-spacing:0;color:#143b82;font-weight:800;text-transform:none;margin:0}.section-note{font-size:12px;color:#58709a}.grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:10px}.grid.operations{grid-template-columns:repeat(4,minmax(0,1fr))}.grid.systems{grid-template-columns:repeat(4,minmax(0,1fr))}.card{position:relative;z-index:5;isolation:isolate;cursor:pointer;pointer-events:auto;display:grid;grid-template-columns:48px minmax(0,1fr) 24px;align-items:center;gap:10px;text-decoration:none;color:inherit;background:#fff;border:1px solid var(--line);border-left:3px solid #5b9bff;border-radius:11px;padding:10px;min-height:88px;box-shadow:0 3px 10px #10233f05;transition:.16s}.card>*{pointer-events:none}.card:focus-visible{outline:3px solid #60a5fa;outline-offset:3px}.card:hover{transform:translateY(-2px);border-color:#9db9df;box-shadow:0 7px 16px #10233f10}.icon{height:46px;width:46px;display:grid;place-items:center;border-radius:12px;background:#e7f1ff;color:#1455a5;font-size:22px;margin:0}.icon svg{width:25px;height:25px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;display:block}.icon-programs svg{width:27px;height:27px}.icon-teachers svg{width:27px;height:27px}.card h3{font-size:14px;margin:0 0 5px;color:#123777}.card p{font-size:11.5px;line-height:1.35;color:var(--muted);margin:0}.go{height:24px;width:24px;display:grid;place-items:center;border-radius:50%;background:#edf4ff;font-size:17px;font-weight:700;color:var(--blue)}.card:nth-child(5n+2){border-left-color:#18a66a}.card:nth-child(5n+2) .icon{background:#e6f7ef;color:#087847}.card:nth-child(5n+3){border-left-color:#f59e0b}.card:nth-child(5n+3) .icon{background:#fff4dc;color:#a65b00}.card:nth-child(5n+4){border-left-color:#7048ff}.card:nth-child(5n+4) .icon{background:#f0ebff;color:#4b22d1}.card:nth-child(5n){border-left-color:#ef4444}.card:nth-child(5n) .icon{background:#ffebeb;color:#b91c1c}.card.backup .icon{background:#fff2df;color:#a65b00}.card.backup{border-left-color:#f97316}.card.profile{display:flex;align-items:center;gap:9px;padding:8px 13px;border:1px solid #ffffff45;border-radius:30px;background:#ffffff1c;font-size:13px}.avatar{height:30px;width:30px;border-radius:50%;display:grid;place-items:center;background:#e7f1ff;color:#174b91;font-size:17px}.foot{color:#94a3b8;text-align:center;font-size:11px;padding:12px 0 16px}
@media(max-width:1100px){.grid{grid-template-columns:repeat(3,minmax(0,1fr))}.grid.operations,.grid.systems{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:700px){.grid,.grid.operations,.grid.systems{grid-template-columns:repeat(2,minmax(0,1fr))}.card{grid-template-columns:40px minmax(0,1fr);}.icon{width:40px;height:40px}.go{display:none}.top{padding:18px}.wrap{padding:0 12px}}
@media(max-width:430px){.grid,.grid.operations,.grid.systems{grid-template-columns:1fr}}

/* Header-only redesign: preserve all dashboard cards and modules below */
.top{
  position:relative; overflow:hidden; isolation:isolate;
  min-height:170px; padding:24px clamp(18px,3.5vw,46px);
  display:flex; justify-content:space-between; align-items:center; gap:24px;
  flex-wrap:nowrap;
  background:
    radial-gradient(ellipse at 18% 0%, #1260a7 0%, transparent 46%),
    linear-gradient(112deg,#073d79 0%,#07366c 52%,#082b58 100%);
}
.top:before{
  content:""; position:absolute; inset:0; z-index:-1; pointer-events:none;
  opacity:.16;
  background-image:
    repeating-linear-gradient(135deg,transparent 0 44px,#63a9e9 45px,transparent 47px 90px),
    repeating-linear-gradient(45deg,transparent 0 60px,#63a9e9 61px,transparent 63px 120px);
  mask-image:linear-gradient(110deg,transparent 0%,#000 100%);
}
.top:after{
  content:"";position:absolute;z-index:-1;pointer-events:none;
  left:-5%;right:25%;bottom:-65px;height:100px;border-radius:50%;
  background:linear-gradient(90deg,#0872c9,#1c5fa4 65%,transparent);
  opacity:.72;transform:rotate(-3deg);
}
.brand-wrap{display:flex;align-items:center;gap:16px;min-width:0}
.university-mark{
  flex:0 0 66px;width:66px;height:88px;display:flex;align-items:center;justify-content:center;
  color:#fff;font-family:Georgia,serif;font-size:42px;font-weight:700;
  border:2px solid #e7f3ff;border-radius:48% 48% 8px 8px;
  text-shadow:0 2px 3px #001f48;position:relative;
}
.university-mark:after{content:"✦";position:absolute;top:5px;font-size:22px;color:#f2b84b}
.brand-copy{min-width:0}
.brand{font-family:Georgia,'Times New Roman',serif;font-size:clamp(15px,1.65vw,23px);letter-spacing:.15px;text-transform:uppercase;color:#fff;font-weight:700;line-height:1.2}
.brand .campus{display:block;margin-top:2px}
.top h1{margin:8px 0 0;font-size:clamp(15px,1.8vw,22px);font-weight:400;color:#e1efff;letter-spacing:.2px}
.top p{display:none}
.profile{
  display:flex;align-items:center;justify-content:flex-end;gap:18px;flex:0 0 auto;
  color:#fff;background:transparent;border:0;border-radius:0;padding:0;font-size:clamp(17px,2vw,26px);
}
.profile-name{font-size:clamp(17px,2vw,26px);font-weight:700;white-space:nowrap;line-height:1.3;color:#fff}
.profile-divider{height:48px;width:2px;background:#a7c9ee;opacity:.9}
.logout{
  display:inline-flex;align-items:center;justify-content:center;gap:8px;
  background:#f8fbff;color:#d7193f;text-decoration:none;
  border:1px solid #d5e6fb;border-radius:12px;padding:12px 17px;
  font-size:14px;font-weight:600;white-space:nowrap;
  box-shadow:0 2px 7px #001d4226;transition:background .15s,transform .15s;
}
.logout span{font-size:22px;line-height:1}
.logout:hover{background:#eaf3ff;transform:translateY(-1px)}
.header-toolbar{
  min-height:54px;margin:16px clamp(12px,2.6vw,38px) 20px;padding:10px 18px;
  display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap;
  border:1px solid #d4e5fb;border-radius:17px;
  background:linear-gradient(100deg,#eaf4ff,#f5f9ff);
  color:#103c7f;box-shadow:0 2px 8px #123d7810;
}
.breadcrumb{display:flex;align-items:center;gap:15px;font-size:14px;color:#1756a4}
.breadcrumb .home{font-size:22px;color:#103c7f}
.breadcrumb .slash{font-size:20px;color:#7a9ac2}
.header-meta{display:flex;align-items:center;gap:18px;margin-left:auto;font-size:13px;color:#103c7f}
.header-meta-item{display:flex;align-items:center;gap:10px;white-space:nowrap}
.header-meta-item .meta-icon{font-size:20px;line-height:1}
.header-meta-divider{height:26px;width:1px;background:#a8c7ec}
@media(max-width:760px){
 .top{padding:18px;min-height:unset;align-items:flex-start;flex-wrap:wrap}
 .brand-wrap{gap:11px}
 .university-mark{flex-basis:48px;width:48px;height:64px;font-size:31px}
 .university-mark:after{font-size:16px}
 .profile{width:100%;justify-content:flex-end;gap:12px}
 .profile-name{font-size:18px;white-space:normal}
 .profile-divider{height:32px}
 .logout{padding:9px 12px}
 .header-toolbar{margin:12px;padding:11px 13px}
 .header-meta{width:100%;margin-left:0;gap:12px;flex-wrap:wrap}
 .header-meta-divider{display:none}
}
@media(max-width:430px){
 .brand{font-size:13px}
 .top h1{font-size:14px}
 .breadcrumb{font-size:12px;gap:9px}
 .header-meta{font-size:11px}
 .header-meta-item{gap:6px}
}

</style>
</head>
<body>
<header class="top">
 <div class="brand-wrap">
  
  <div class="brand-copy">
   <div class="brand">Riphah International University <span class="campus">(Peshawar Campus)</span></div>
   <h1>MCQ Bank Administration</h1>
  </div>
 </div>
 <div class="profile">
  <div class="profile-name"><?= esc(($adminFullName ?? '') !== '' ? $adminFullName : 'Administrator') ?></div>
  <div class="profile-divider" aria-hidden="true"></div>
  <a class="logout" href="<?= site_url('logout') ?>"><span aria-hidden="true">⇥</span> Logout</a>
 </div>
</header>

<main class="wrap">
 <div class="intro"><div><h2>Administration workspace</h2><p>Select a module to continue. Access is controlled by your account role.</p></div></div>
 <section class="section-panel">
  <div class="section-head"><h2 class="section">Academic Master Data</h2><span class="section-note">Manage the academic structure, programs and faculty</span></div>
  <div class="grid">
   <a class="card" href="<?= site_url('master/departments') ?>"><div class="icon icon-departments"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 21h18M5 21V5l7-3 7 3v16M9 8h1m4 0h1M9 12h1m4 0h1M9 16h1m4 0h1M11 21v-3h2v3" /></svg></div><div><h3>Departments</h3><p>Create and maintain academic departments.</p></div><span class="go">›</span></a>
   <a class="card" href="<?= site_url('master/subjects') ?>"><div class="icon icon-subjects"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v17H6.5A2.5 2.5 0 0 0 4 21.5zM4 4.5v17M8 6h8M8 10h8M8 14h6" /></svg></div><div><h3>Subjects</h3><p>Manage subjects and department assignments.</p></div><span class="go">›</span></a>
   <a class="card" href="<?= site_url('master/subdisciplines') ?>"><div class="icon icon-subdisciplines"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="5" r="2.5"/><circle cx="5" cy="19" r="2.5"/><circle cx="19" cy="19" r="2.5"/><path d="M12 7.5v4M12 11.5 5 16.5M12 11.5l7 5" /></svg></div><div><h3>Sub Disciplines</h3><p>Maintain optional sub-discipline levels.</p></div><span class="go">›</span></a>
   <a class="card" href="<?= site_url('master/systems') ?>"><div class="icon icon-systems"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><path d="M10 6.5h4M6.5 10v4M17.5 10v4M10 17.5h4" /></svg></div><div><h3>Systems</h3><p>Manage systems and subject links.</p></div><span class="go">›</span></a>
   <a class="card" href="<?= site_url('master/topics') ?>"><div class="icon icon-topics"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14M5 9h14M5 14h9M5 19h9"/><circle cx="3" cy="4" r=".5"/><circle cx="3" cy="9" r=".5"/><circle cx="3" cy="14" r=".5"/><circle cx="3" cy="19" r=".5"/></svg></div><div><h3>Topics</h3><p>Maintain topics under relevant systems.</p></div><span class="go">›</span></a>
   <a class="card" href="<?= site_url('master/program-departments') ?>"><div class="icon icon-programs"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="7" height="6" rx="1"/><rect x="14" y="14" width="7" height="6" rx="1"/><path d="M10 7h4a2 2 0 0 1 2 2v5M13 12l3 3 3-3"/></svg></div><div><h3>Program–Department Mapping</h3><p>Assign departments to one or more academic programs.</p></div><span class="go">›</span></a>
   <a class="card" href="<?= site_url('master/programs') ?>"><div class="icon icon-programs"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 9 12 4l9 5-9 5zM6 11v5c3 3 9 3 12 0v-5M21 9v6" /><path d="M9 20h6" /></svg></div><div><h3>Program Management</h3><p>Manage academic programs and assignments.</p></div><span class="go">›</span></a>
   <a class="card" href="<?= site_url('master/program-subjects') ?>"><div class="icon icon-programs"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4zM13 4h7v4h-7zM13 10h7v10h-7zM4 13h7v7H4z"/><path d="M6 7h3M6 16h3M15 6h3M15 13h3M15 16h3"/></svg></div><div><h3>Program–Subject Mapping</h3><p>Assign shared subjects to the selected academic programs.</p></div><span class="go">›</span></a>
   <a class="card" href="<?= site_url('master/teachers') ?>"><div class="icon icon-teachers"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="7" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2zM17 4.5a3 3 0 0 1 0 5.8M18 14a4.5 4.5 0 0 1 3 4.2V20h-3" /></svg></div><div><h3>Teacher Management</h3><p>Maintain faculty and teacher records.</p></div><span class="go">›</span></a>
  </div>
 </section>
 <section class="section-panel">
  <div class="section-head"><h2 class="section">Question Bank Operations</h2><span class="section-note">Manage MCQs, review submissions and access reports</span></div>
  <div class="grid operations">
   <a class="card" href="<?= site_url('master/mcqs') ?>"><div class="icon icon-mcq"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="m7 8 1.5 1.5L11 7M13 8h4M7 14l1.5 1.5L11 13M13 14h4" /></svg></div><div><h3>MCQ Bank</h3><p>Browse and manage MCQ records.</p></div><span class="go">›</span></a>
   
   <a class="card" href="<?= site_url('master/admin/users') ?>"><div class="icon icon-users"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2z"/><circle cx="18" cy="9" r="2.5"/><path d="M17 15a4 4 0 0 1 4 4v1h-4" /></svg></div><div><h3>Users & Faculty</h3><p>Manage accounts, roles and access.</p></div><span class="go">›</span></a>
   <!-- <a class="card" href="<?= site_url('admin/reports') ?>"><div class="icon icon-reports"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20V4M4 20h17M8 16v-5M12 16V7M16 16v-8M20 16V3" /></svg></div><div><h3>Reports</h3><p>View question-bank and submission reports.</p></div><span class="go">›</span></a> -->
  </div>
 </section>
 <section class="section-panel">
  <div class="section-head"><h2 class="section">System Administration</h2><span class="section-note">Backup and maintenance tools</span></div>
  <div class="grid systems">
   <a class="card backup" href="<?= site_url('admin/backup') ?>"><div class="icon icon-backup"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12M7 10l5 5 5-5M4 18v3h16v-3" /></svg></div><div><h3>Database Backup</h3><p>Create and download a database backup.</p></div><span class="go">›</span></a>
   <a class="card" href="<?= site_url('admin/settings') ?>"><div class="icon icon-settings"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/><path d="m19.4 15 .1.1 1.4 1.1-1.4 2.4-1.7-.7a8 8 0 0 1-1.6.9L16 20.5h-3l-.3-1.8a8 8 0 0 1-1.6-.9l-1.7.7L8 16.1 9.4 15a7 7 0 0 1 0-1.9L8 12l1.4-2.4 1.7.7a8 8 0 0 1 1.6-.9L13 7.5h3l.3 1.9a8 8 0 0 1 1.6.9l1.7-.7L21 12l-1.5 1.1a7 7 0 0 1-.1 1.9z" transform="translate(-1 -2) scale(1.05)"/></svg></div><div><h3>System Settings</h3><p>Manage application settings and preferences.</p></div><span class="go">›</span></a>
  </div>
 </section>
 <div class="foot">MCQ Bank · Administration workspace</div>
</main>
</body>
</html>
