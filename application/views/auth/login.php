<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - BannerPosko</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #FBF6F6;
            color: #f0f0f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #2D336B;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo-icon {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .logo-text {
            font-family: 'Space Mono', monospace;
            font-size: 18px;
            font-weight: 700;
        }
        .logo-sub {
            font-size: 13px;
            color: #F8EDED;
            margin-top: 4px;
        }
        .error-msg {
            background: rgba(248,113,113,0.1);
            border: 1px solid rgba(248,113,113,0.3);
            color: #f87171;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: white;
            margin-bottom: 8px;
        }
        input {
            width: 100%;
            padding: 11px 14px;
            background: #0d0d0f;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            color: #f0f0f5;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.18s;
            outline: none;
        }
        input:focus {
            border-color: #4f8ef7;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #4f8ef7;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: opacity 0.18s;
        }
        .btn-login:hover { opacity: 0.85; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <div class="logo-icon"></div>
            <div class="logo-text">BannerPosko</div>
            <div class="logo-sub">Masuk ke panel admin</div>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="error-msg"><?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('Auth/login') ?>" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</body>
</html>