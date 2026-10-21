<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($pageTitle ?? 'MCQ Bank — Teaching Faculty') ?></title>
<style>
:root{--navy:#0d2b59;--blue:#0874d1;--cyan:#23c6e8;--violet:#5b4bd6;--ink:#1c2d49;--muted:#58708e;--line:#d8e7f8;--bg:#f3f7ff}
*{box-sizing:border-box}body{margin:0;color:var(--ink);font-family:"Segoe UI",Arial,sans-serif;background:radial-gradient(ellipse at 10% 0%,#e6f6ff 0,transparent 42%),radial-gradient(ellipse at 95% 15%,#f0edff 0,transparent 35%),var(--bg)}
.top{position:relative;isolation:isolate;overflow:hidden;min-height:158px;padding:22px clamp(18px,4vw,48px);display:flex;align-items:center;justify-content:space-between;gap:24px;background:linear-gradient(110deg,#fbfdff 0%,#e8f5ff 55%,#dceeff 100%);border-bottom:1px solid #c7e2fb;box-shadow:0 8px 22px #123c6610}
.top:before{content:"";position:absolute;z-index:-1;left:-8%;bottom:-118px;width:75%;height:170px;border:20px solid #a8e8f3;border-radius:50%;opacity:.52;transform:rotate(-4deg)}
.top:after{content:"";position:absolute;z-index:-1;right:-2%;top:-46px;width:42%;height:220px;border-radius:0 0 0 72px;background:linear-gradient(125deg,#1766ae,#103765);transform:skewX(-7deg)}
.brand-area{display:flex;align-items:center;gap:18px;min-width:0;position:relative;z-index:1}.logo{width:72px;height:72px;flex:0 0 72px;display:grid;place-items:center;background:linear-gradient(145deg,#fff,#edf7ff);border:1px solid #d4e8fb;border-radius:18px;box-shadow:0 7px 20px #13539b12}.logo svg{width:48px;height:48px}.brand-copy{min-width:0}.brand-name{font-family:Georgia,"Times New Roman",serif;font-size:clamp(20px,2.1vw,30px);font-weight:700;letter-spacing:-.5px;line-height:1.12;color:#102e62}.campus{font-family:Georgia,"Times New Roman",serif;color:#0872cb;font-size:clamp(16px,1.6vw,22px);font-weight:700;margin-top:3px}.subtitle{display:flex;align-items:center;gap:10px;margin-top:11px;color:#557495;font-size:13px}.accent{height:3px;width:32px;border-radius:4px;background:#12c7c9;display:inline-block}
.profile{position:relative;z-index:1;display:flex;align-items:center;gap:15px;color:#fff;padding:9px 0 9px 16px;min-width:330px;justify-content:flex-end}.avatar{height:58px;width:58px;flex:0 0 58px;border-radius:50%;background:linear-gradient(145deg,#fff,#dcefff);border:3px solid #2ca8e5;display:grid;place-items:center;box-shadow:0 0 0 4px #ffffff18}.avatar svg{width:34px;height:34px;fill:#2779ce}.identity{min-width:0;max-width:245px;padding-left:16px;border-left:1px solid #ffffff55}.profile-name{font-family:Georgia,"Times New Roman",serif;font-size:clamp(19px,1.7vw,25px);font-weight:700;line-height:1.15;color:#fff;white-space:nowrap}.profile-meta{display:flex;flex-direction:column;gap:3px;margin-top:5px;font-size:12px;color:#d9eaff;line-height:1.35}.profile-meta span:last-child{max-width:245px}.logout{white-space:nowrap;display:inline-flex;align-items:center;gap:7px;margin-left:2px;padding:10px 14px;border:1px solid #9fc8f0;border-radius:24px;text-decoration:none;color:#fff;font-weight:700;font-size:12px;transition:background .18s,border-color .18s}.logout:hover{background:#ffffff20;border-color:#fff}
.wrap{max-width:1450px;margin:32px auto;padding:0 clamp(18px,4vw,48px)}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px}.card{position:relative;isolation:isolate;overflow:hidden;display:grid;grid-template-columns:118px minmax(0,1fr);align-items:center;gap:18px;min-height:245px;padding:28px 30px;text-decoration:none;color:inherit;border:1.5px solid #56c8f3;border-radius:27px;background:linear-gradient(130deg,#fff 0%,#f2faff 58%,#dff8ff 100%);box-shadow:0 12px 30px #1479b51a;transition:transform .22s,box-shadow .22s,border-color .22s}.card:before{content:"";position:absolute;z-index:-1;width:290px;height:290px;right:-95px;bottom:-205px;border-radius:50%;background:#8de9ec;opacity:.5;transition:transform .25s}.card:hover{transform:translateY(-5px);border-color:#17b9ed;box-shadow:0 18px 38px #1479b52b}.card:hover:before{transform:scale(1.3)}.card:nth-child(2){border-color:#b7b4ff;background:linear-gradient(130deg,#fff 0%,#faf9ff 60%,#efedff 100%);box-shadow:0 12px 30px #6253d51a}.card:nth-child(2):before{background:#d4d0ff}.card:nth-child(2):hover{border-color:#8379f0;box-shadow:0 18px 38px #6253d52b}.icon{width:88px;height:88px;border-radius:23px;display:grid;place-items:center;color:#fff;background:linear-gradient(145deg,#38bde9,#0762cb);box-shadow:0 10px 22px #1687d64a,inset 0 0 0 1px #ffffff75}.icon svg{width:47px;height:47px;stroke:currentColor}.card:nth-child(2) .icon{background:linear-gradient(145deg,#9287ff,#4036c8);box-shadow:0 10px 22px #6655d94a,inset 0 0 0 1px #ffffff75}.card-content{min-width:0}.card h2{font-family:Georgia,"Times New Roman",serif;font-size:clamp(22px,2vw,31px);letter-spacing:-.4px;color:#102e62;margin:0 0 12px}.card p{font-size:15px;line-height:1.65;color:#536f91;margin:0 0 21px;max-width:540px}.go{display:inline-flex;align-items:center;justify-content:center;gap:12px;min-width:245px;padding:14px 22px;border-radius:28px;background:linear-gradient(100deg,#087fe0,#064fc2);color:white;font-size:14px;font-weight:800;box-shadow:0 7px 15px #0964c52a;transition:filter .18s,transform .18s}.card:hover .go{filter:brightness(1.08);transform:translateY(-1px)}.card:nth-child(2) .go{background:linear-gradient(100deg,#8275f0,#4035c5);box-shadow:0 7px 15px #5c4ed32a}.go svg{width:17px;height:17px;stroke:currentColor}.foot{text-align:center;color:#8b9db3;font-size:12px;padding:26px 0 12px}
@media(max-width:900px){.top{align-items:flex-start;flex-direction:column;padding-top:20px;padding-bottom:20px}.top:after{width:50%;height:100%;top:0;right:-8%;border-radius:0 0 0 60px}.profile{align-self:stretch;min-width:0;justify-content:flex-start;padding-left:0}.identity{max-width:none}.profile-meta span:last-child{max-width:none}.grid{gap:16px}.card{grid-template-columns:78px minmax(0,1fr);gap:15px;padding:22px;min-height:220px}.icon{width:68px;height:68px;border-radius:19px}.icon svg{width:38px;height:38px}.go{min-width:0}}
@media(max-width:600px){.brand-area{align-items:flex-start;gap:12px}.logo{width:54px;height:54px;flex-basis:54px;border-radius:14px}.logo svg{width:38px;height:38px}.brand-name{font-size:19px}.campus{font-size:16px}.subtitle{font-size:11px;margin-top:8px}.top:after{width:85%;opacity:.98}.profile{gap:10px;flex-wrap:wrap}.avatar{width:48px;height:48px;flex-basis:48px}.identity{padding-left:10px}.profile-name{font-size:19px;white-space:normal}.profile-meta{font-size:11px}.logout{margin-left:auto}.wrap{margin:22px auto}.grid{grid-template-columns:1fr}.card{min-height:190px;grid-template-columns:65px minmax(0,1fr);padding:20px;border-radius:22px}.icon{width:58px;height:58px;border-radius:16px}.icon svg{width:32px;height:32px}.card h2{font-size:23px}.card p{font-size:13px;margin-bottom:16px}.go{font-size:12px;padding:12px 15px}}
.logout{
    position: relative;
    top: 20px;
}
</style>
</head>
<body>
<header class="top">
 <div class="brand-area">
  <div class="logo" aria-hidden="true">
   <svg viewBox="0 0 64 64" fill="none" stroke="#123d79" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 22 32 9l25 13-25 13L7 22Z"/><path d="M17 28v12c8 7 22 7 30 0V28"/><path d="M56 23v18"/><path d="M24 19 42 28"/><path d="M18 47c8 7 22 7 30 0"/><path d="M48 22v8"/><circle cx="48" cy="32" r="2" fill="#123d79"/></svg>
  </div>
  <div class="brand-copy">
   <div class="brand-name">Riphah International University</div>
   <div class="campus">(Peshawar Campus)</div>
   <div class="subtitle"><span class="accent"></span><span>MCQ Bank · Teaching Faculty Workspace</span></div>
  </div>
 </div>
 <div class="profile">
  <div class="avatar" aria-hidden="true"><svg viewBox="0 0 48 48"><circle cx="24" cy="15" r="9"/><path d="M7 42c0-10 7-16 17-16s17 6 17 16Z"/></svg></div>
  <div class="identity">
   <div class="profile-name"><?= esc(($facultyProfile['fullName'] ?? '') !== '' ? $facultyProfile['fullName'] : 'Faculty profile not found') ?></div>
   <div class="profile-meta">
    <span><?= esc(($facultyProfile['designation'] ?? '') !== '' ? $facultyProfile['designation'] : 'Designation not assigned') ?></span>
    <span><?= esc(($facultyProfile['department'] ?? '') !== '' ? $facultyProfile['department'] : 'Department not assigned') ?></span>
   </div>
  </div>
  <a class="logout" href="<?= site_url('logout') ?>"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/></svg>Logout</a>
 </div>
</header>
<main class="wrap">
 <div class="grid">
  <a class="card" href="<?= site_url('master/faculty/mcqs') ?>">
   <div class="icon" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none" stroke-width="2.7" stroke-linecap="round" stroke-linejoin="round"><path d="M27 7H12a3 3 0 0 0-3 3v29a3 3 0 0 0 3 3h22a3 3 0 0 0 3-3V24"/><path d="M26 8v9h9"/><path d="m24 31 13-13 5 5-13 13-7 2Z"/><path d="m34 21 5 5"/></svg></div>
   <div class="card-content"><h2>MCQ Entry &amp; Drafts</h2><p>Create a new MCQ, save it as a draft, and edit your saved drafts from one place.</p><span class="go">Open MCQ workspace <svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h15M13 5l7 7-7 7"/></svg></span></div>
  </a>
  <a class="card" href="<?= site_url('master/change-password') ?>">
   <div class="icon" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none" stroke-width="2.7" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="21" width="32" height="23" rx="4"/><path d="M15 21v-8a9 9 0 0 1 18 0v8"/><circle cx="24" cy="32" r="2"/><path d="M24 34v4"/></svg></div>
   <div class="card-content"><h2>Change Password</h2><p>Update your account password securely.</p><span class="go">Change password <svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h15M13 5l7 7-7 7"/></svg></span></div>
  </a>
 </div>
 <div class="foot">MCQ Bank · Teaching Faculty Workspace</div>
</main>
</body>
</html>
