
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Riphah International University</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --navy: #10243b;
            --navy-light: #1d3a56;
            --teal: #1ca99a;
            --teal-light: #65ded0;
            --text: #183149;
            --muted: #718096;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(ellipse at 12% 15%,
                    rgba(51, 193, 177, .24), transparent 38%),
                radial-gradient(ellipse at 90% 85%,
                    rgba(80, 129, 180, .24), transparent 40%),
                linear-gradient(135deg, #eaf2f7, #dce9f0 50%, #edf5f7);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 18px;
            position: relative;
            overflow: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(2px);
        }

        body::before {
            width: 340px;
            height: 340px;
            top: -150px;
            right: -90px;
            background: rgba(28, 169, 154, .18);
        }

        body::after {
            width: 420px;
            height: 420px;
            bottom: -230px;
            left: -140px;
            background: rgba(37, 78, 121, .13);
        }

        .page {
            width: 100%;
            max-width: 1000px;
            min-height: 560px;
            display: grid;
            grid-template-columns: .95fr 1.05fr;
            border: 1px solid rgba(255, 255, 255, .8);
            border-radius: 30px;
            overflow: hidden;
            background: rgba(255, 255, 255, .38);
            box-shadow:
                0 32px 90px rgba(20, 46, 70, .17),
                0 5px 18px rgba(20, 46, 70, .06);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            position: relative;
            z-index: 1;
        }

        .brand-panel {
            background:
                radial-gradient(circle at 15% 15%,
                    rgba(67, 208, 190, .18), transparent 35%),
                linear-gradient(150deg, #10243b, #173b55 65%, #145a61);
            color: white;
            padding: 46px 42px 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            width: 360px;
            height: 360px;
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 50%;
            right: -210px;
            top: 90px;
            box-shadow:
                0 0 0 35px rgba(255,255,255,.025),
                0 0 0 75px rgba(255,255,255,.02);
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border: 1px solid rgba(101, 222, 208, .20);
            border-radius: 50%;
            left: -135px;
            bottom: 30px;
        }

        .brand-content,
        .brand-footer {
            position: relative;
            z-index: 1;
        }

        .university-mark {
            width: 76px;
            height: 76px;
            border-radius: 23px;
            border: 1px solid rgba(255,255,255,.42);
            background: rgba(255,255,255,.11);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.22);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 26px;
        }

        .university-mark svg {
            width: 43px;
            height: 43px;
        }

        .eyebrow {
            color: #8de6da;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .university-name {
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(30px, 3.1vw, 40px);
            line-height: 1.18;
            letter-spacing: -.8px;
            font-weight: 600;
            margin: 0;
        }

        .campus-name {
            font-size: 17px;
            font-weight: 500;
            letter-spacing: 2px;
            color: #8de6da;
            margin: 15px 0 0;
        }

        .brand-rule {
            height: 3px;
            width: 64px;
            border-radius: 4px;
            background: linear-gradient(90deg, #65ded0, transparent);
            margin: 22px 0;
        }

        .brand-description {
            color: rgba(255,255,255,.76);
            line-height: 1.75;
            font-size: 14px;
            max-width: 300px;
            margin: 0;
        }

        .brand-footer {
            color: rgba(255,255,255,.60);
            font-size: 11px;
            letter-spacing: .7px;
            line-height: 1.8;
            border-top: 1px solid rgba(255,255,255,.15);
            padding-top: 14px;
            margin-top: 28px;
        }

        .login-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 42px 54px 36px;
            background: rgba(255,255,255,.56);
        }

        .login-box {
            width: 100%;
            max-width: 370px;
        }

        .login-label {
            color: var(--teal);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .login-title {
            font-size: 32px;
            font-weight: 750;
            letter-spacing: -1px;
            margin: 0 0 10px;
            color: var(--navy);
        }

        .login-subtitle {
            font-size: 14px;
            line-height: 1.7;
            color: var(--muted);
            margin: 0 0 26px;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 9px;
            color: #344b60;
            letter-spacing: .3px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap svg {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 19px;
            height: 19px;
            stroke: #8093a5;
            pointer-events: none;
        }

        .field input {
            width: 100%;
            height: 54px;
            border: 1px solid rgba(140, 162, 181, .42);
            border-radius: 13px;
            background: rgba(255,255,255,.78);
            padding: 0 44px;
            font: inherit;
            font-size: 14px;
            color: var(--navy);
            outline: none;
            box-shadow: inset 0 1px 2px rgba(20,46,70,.025);
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        .field input::placeholder {
            color: #9baab8;
        }

        .field input:focus {
            background: white;
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(28,169,154,.12);
        }

        .password-toggle {
            position: absolute;
            right: 9px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            border-radius: 8px;
            padding: 7px;
            background: transparent;
            color: #71869a;
            cursor: pointer;
            font-size: 11px;
            font-weight: 700;
        }

        .password-toggle:hover {
            background: #edf5f5;
            color: #147e76;
        }

        .sign-in {
            position: relative;
            width: 100%;
            min-height: 55px;
            border: 1px solid rgba(255,255,255,.26);
            border-radius: 13px;
            background: linear-gradient(115deg, #102b46, #174d61 60%, #138c83);
            color: #fff;
            font-size: 14px;
            font-weight: 750;
            letter-spacing: .7px;
            cursor: pointer;
            overflow: hidden;
            box-shadow:
                0 12px 25px rgba(20, 67, 87, .22),
                inset 0 1px 0 rgba(255,255,255,.20);
            transition: transform .2s, box-shadow .2s, filter .2s;
        }

        .sign-in::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            height: 100%;
            width: 65%;
            background: linear-gradient(90deg,
                transparent, rgba(255,255,255,.20), transparent);
            transform: skewX(-22deg);
            transition: left .65s;
        }

        .sign-in:hover {
            transform: translateY(-2px);
            filter: brightness(1.07);
            box-shadow: 0 16px 30px rgba(20, 67, 87, .28);
        }

        .sign-in:hover::before {
            left: 140%;
        }

        .sign-in:active {
            transform: translateY(0);
        }

        .error-message {
            border: 1px solid rgba(190, 58, 70, .20);
            border-radius: 12px;
            background: rgba(255, 235, 238, .84);
            color: #a12d3b;
            padding: 12px 14px;
            font-size: 12px;
            line-height: 1.7;
            margin-bottom: 22px;
        }

        .validation-errors {
            color: #a12d3b;
            font-size: 12px;
            margin-top: 7px;
        }

        .secure-note {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            color: #8090a0;
            font-size: 11px;
        }

        .secure-note svg {
            width: 15px;
            height: 15px;
            stroke: #1a9d8f;
        }

        .bottom-note {
            text-align: center;
            margin-top: 23px;
            padding-top: 14px;
            border-top: 1px solid rgba(135, 157, 176, .22);
            color: #8a98a6;
            font-size: 11px;
            line-height: 1.8;
        }

        /* Refined, quieter academic presentation */
        .page {
            min-height: 560px;
            border-radius: 26px;
            box-shadow: 0 24px 70px rgba(20, 46, 70, .14), 0 3px 12px rgba(20, 46, 70, .04);
        }

        .brand-panel {
            background: radial-gradient(circle at 10% 12%, rgba(67,208,190,.13), transparent 34%),
                        linear-gradient(155deg, #112b43 0%, #173e58 68%, #15545a 100%);
        }

        .university-mark {
            width: 68px;
            height: 68px;
            border-radius: 20px;
            margin-bottom: 27px;
        }

        .eyebrow { letter-spacing: 2.4px; margin-bottom: 13px; }
        .campus-name { font-size: 15px; letter-spacing: 1.7px; }
        .brand-description { font-size: 14px; color: rgba(255,255,255,.78); }
        .login-panel { background: rgba(250,252,253,.88); }
        .login-label { letter-spacing: 2px; margin-bottom: 10px; }
        .login-title { font-size: 31px; letter-spacing: -.7px; }
        .login-subtitle { margin-bottom: 25px; }
        .field input { height: 52px; border-radius: 11px; }
        .sign-in { min-height: 53px; border-radius: 11px; letter-spacing: .3px; }
        .bottom-note { font-size: 10px; }

        @media (max-height: 700px) and (min-width: 781px) {
            body { padding-top: 16px; padding-bottom: 16px; }
            .page { min-height: 520px; }
            .brand-panel { padding-top: 34px; padding-bottom: 25px; }
            .brand-footer { margin-top: 20px; padding-top: 13px; }
            .login-panel { padding-top: 30px; padding-bottom: 26px; }
            .login-subtitle { margin-bottom: 20px; }
            .field { margin-bottom: 15px; }
        }

        @media (max-width: 780px) {
            body {
                padding: 18px 12px;
            }

            .page {
                max-width: 490px;
                grid-template-columns: 1fr;
                border-radius: 23px;
            }

            .brand-panel {
                padding: 30px 29px 25px;
            }

            .university-mark {
                width: 58px;
                height: 58px;
                border-radius: 17px;
                margin-bottom: 23px;
            }

            .university-mark svg {
                width: 34px;
                height: 34px;
            }

            .university-name {
                font-size: 31px;
            }

            .brand-description,
            .brand-rule {
                display: none;
            }

            .brand-footer {
                margin-top: 18px;
                padding-top: 13px;
            }

            .login-panel {
                padding: 34px 27px 29px;
            }

            .login-title {
                font-size: 28px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>

<main class="page">
    <section class="brand-panel">
        <div class="brand-content">
            <div class="university-mark" aria-hidden="true">
                <svg viewBox="0 0 48 48" fill="none"
                     stroke="#8de6da" stroke-width="1.7"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M24 5 42 14 24 23 6 14 24 5Z"/>
                    <path d="M12 18v10c7 7 17 7 24 0V18"/>
                    <path d="M42 14v14"/>
                    <path d="M19 32v8h10v-8"/>
                    <path d="M16 43h16"/>
                </svg>
            </div>

            <div class="eyebrow">Academic Examination Portal</div>

            <h1 class="university-name">
                Riphah International University
            </h1>

            <p class="campus-name">(Peshawar Campus)</p>

            <div class="brand-rule"></div>

            <p class="brand-description">
                Academic assessment, made simple.
            </p>
        </div>

        <div class="brand-footer">
            ONLINE MCQ EXAMINATION &amp; QUESTION BANK
        </div>
    </section>

    <section class="login-panel">
        <div class="login-box">
            <div class="login-label">ACADEMIC PORTAL</div>
            <h2 class="login-title">Welcome back</h2>
            <p class="login-subtitle">Sign in to continue.</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="error-message" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($loginError) && $loginError): ?>
                <div class="error-message" role="alert">
                    <?= esc($loginError) ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="field">
                    <label for="UserName">Username</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21c0-4.1 3.6-7 8-7s8 2.9 8 7"/>
                        </svg>
                        <input
                            type="text"
                            id="UserName"
                            name="UserName"
                            placeholder="Enter your username"
                            value="<?= esc(old('UserName') ?? '') ?>"
                            autocomplete="username"
                            required
                            autofocus>
                    </div>
                    <?php if (isset($validation)): ?>
                        <div class="validation-errors">
                            <?= esc($validation->getError('UserName')) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="Password">Password</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8">
                            <rect x="4" y="10" width="16" height="11" rx="2"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                            <path d="M12 14v3"/>
                        </svg>
                        <input
                            type="password"
                            id="Password"
                            name="Password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required>
                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword"
                            aria-label="Show password"
                            aria-pressed="false">
                            SHOW
                        </button>
                    </div>
                    <?php if (isset($validation)): ?>
                        <div class="validation-errors">
                            <?= esc($validation->getError('Password')) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="sign-in">
                    Sign in &nbsp; &#8594;
                </button>
            </form>

            <div class="secure-note">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8">
                    <path d="M12 3 20 6v5c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6l8-3Z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                Secure access for authorized users
            </div>

            <div class="bottom-note">
                Riphah International University · Peshawar Campus
            </div>
        </div>
    </section>
</main>

<script>
    const passwordInput = document.getElementById('Password');
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', function () {
        const showPassword = passwordInput.type === 'password';

        passwordInput.type = showPassword ? 'text' : 'password';
        togglePassword.textContent = showPassword ? 'HIDE' : 'SHOW';
        togglePassword.setAttribute('aria-pressed', String(showPassword));
        togglePassword.setAttribute(
            'aria-label',
            showPassword ? 'Hide password' : 'Show password'
        );
    });
</script>

</body>
</html>
