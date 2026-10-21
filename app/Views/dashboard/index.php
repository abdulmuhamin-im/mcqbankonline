<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle ?? 'MCQ Bank') ?></title>
    <style>
        body { margin:0; font-family:Arial,sans-serif; background:#f4f7fb; color:#1f2937; }
        header { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:18px 24px; background:#fff; border-bottom:1px solid #e2e8f0; }
        main { max-width:1000px; margin:28px auto; padding:0 18px; }
        .card { padding:24px; border:1px solid #e2e8f0; border-radius:14px; background:#fff; box-shadow:0 8px 24px rgba(15,23,42,.04); }
        .muted { color:#64748b; }
        a.logout { padding:9px 13px; color:#fff; background:#1769aa; border-radius:8px; text-decoration:none; }
    </style>
</head>
<body>
<header>
    <div><strong>MCQ Data Bank</strong><div class="muted"><?= esc($roleLabel ?? '') ?></div></div>
    <div>Welcome, <?= esc((string) session()->get('username')) ?> &nbsp; <a class="logout" href="<?= site_url('logout') ?>">Logout</a></div>
</header>
<main>
    <section class="card">
        <h1><?= esc($pageTitle ?? 'Dashboard') ?></h1>
        <p>مرحلہ 1 کامیاب ہونے کے بعد موجودہ MCQ Bank کے modules کو مرحلہ وار یہاں منتقل کیا جائے گا۔</p>
        <p class="muted">ابھی یہ foundation dashboard ہے؛ پرانے CRUD اور MCQ workflows اگلی conversion batch میں شامل ہوں گے۔</p>
    </section>
</main>
</body>
</html>
