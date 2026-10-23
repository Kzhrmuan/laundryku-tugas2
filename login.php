
<?php
session_start();
require_once "config.php";

$error = "";

if (!empty($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT id, username, password
             FROM users
             WHERE username = :username
             LIMIT 1"
        );

        $stmt->execute(["username" => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["bahasa"] = $_SESSION["bahasa"] ?? "id";

            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login LaundryKu</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', Arial, sans-serif;
            color: #153f79;
            background:
                radial-gradient(circle at 10% 58%, #e6f3ff 0 30px, transparent 31px),
                radial-gradient(circle at 92% 58%, #e9f4ff 0 180px, transparent 181px),
                linear-gradient(125deg, #f8fbff, #edf6ff 55%, #f9fcff);
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            top: -100px;
            left: -100px;
            width: 330px;
            height: 260px;
            background: #bfddff;
            border-radius: 40% 0 65% 0;
            transform: rotate(-10deg);
            z-index: -1;
        }

        body::after {
            content: "";
            position: fixed;
            bottom: -150px;
            right: -60px;
            width: 300px;
            height: 260px;
            background: #b7d9ff;
            border-radius: 70% 0 0 0;
            z-index: -1;
        }

        .container {
            width: min(1120px, 92%);
            min-height: 100vh;
            margin: auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 70px;
            padding: 45px 0;
        }

        /* BAGIAN BRANDING */
        .branding {
            padding: 15px 0;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-bottom: 34px;
        }

        .logo-icon {
            width: 105px;
            height: 105px;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #1269b9;
        }

        .logo-icon svg {
            width: 100%;
            height: 100%;
        }

        .brand-name {
            font-size: clamp(34px, 4vw, 49px);
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -2px;
            color: #143f7b;
        }

        .brand-name span {
            color: #19a6d0;
        }

        .tagline {
            margin-top: 8px;
            font-size: 18px;
            color: #89a4c6;
            letter-spacing: 1px;
        }

        .brand-description {
            max-width: 420px;
            font-size: 21px;
            line-height: 1.6;
            font-weight: 500;
            margin-bottom: 30px;
        }

        .laundry-illustration {
            width: min(100%, 420px);
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .laundry-illustration img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .illustration-placeholder {
            width: 290px;
            height: 190px;
            border-radius: 50%;
            background: linear-gradient(145deg, #e2f0ff, #c8e3ff);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: #1979c7;
        }

        .illustration-placeholder svg {
            width: 115px;
            height: 115px;
        }

        .illustration-placeholder p {
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* KARTU LOGIN */
        .login-card {
            width: 100%;
            max-width: 510px;
            justify-self: end;
            padding: 40px 38px 32px;
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 22px 55px rgba(67, 133, 205, 0.13);
            backdrop-filter: blur(12px);
        }

        .login-card h1 {
            font-size: 36px;
            line-height: 1.2;
            color: #153f79;
            margin-bottom: 14px;
            font-weight: 800;
        }

        .login-subtitle {
            color: #617fa6;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .error-message {
            background: #fff0f0;
            color: #b42318;
            border: 1px solid #ffd1d1;
            border-radius: 10px;
            padding: 13px 15px;
            margin-bottom: 22px;
            font-size: 14px;
            line-height: 1.5;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
            min-height: 66px;
            border: 1.5px solid #dbe6f2;
            border-radius: 14px;
            margin-bottom: 22px;
            padding: 0 16px;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-group:focus-within {
            border-color: #159ca5;
            box-shadow: 0 0 0 4px rgba(21, 156, 165, 0.10);
        }

        .input-icon {
            width: 25px;
            height: 25px;
            flex-shrink: 0;
            margin-right: 17px;
            color: #17477f;
        }

        .input-content {
            flex: 1;
            min-width: 0;
        }

        .input-content label {
            display: block;
            color: #58779f;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .input-content input {
            display: block;
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            color: #183f73;
            font-family: inherit;
            font-size: 15px;
        }

        .input-content input::placeholder {
            color: #a9b9cd;
        }

        .toggle-password {
            width: 35px;
            height: 35px;
            flex-shrink: 0;
            border: none;
            background: transparent;
            color: #8da5c0;
            cursor: pointer;
            font-size: 19px;
            margin-left: 8px;
        }

        .toggle-password:hover {
            color: #159ca5;
        }

        .login-button {
            width: 100%;
            min-height: 64px;
            border: none;
            border-radius: 13px;
            margin-top: 10px;
            background: linear-gradient(110deg, #07989d, #078e93);
            color: #fff;
            font-family: inherit;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 7px 16px rgba(7, 152, 157, 0.16);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(7, 152, 157, 0.25);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .button-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .button-content svg {
            width: 21px;
            height: 21px;
        }

        .login-footer {
            margin-top: 31px;
            padding: 17px 12px;
            border-radius: 14px;
            background: #f0f7ff;
            color: #58769c;
            text-align: center;
            font-size: 12px;
            line-height: 1.9;
        }

        .footer-brand {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
        }

        .footer-brand svg {
            width: 23px;
            height: 23px;
            color: #17477f;
        }

        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
                gap: 30px;
                width: min(510px, 92%);
                padding: 45px 0;
            }

            .branding {
                text-align: center;
            }

            .brand-logo {
                justify-content: center;
                margin-bottom: 20px;
            }

            .logo-icon {
                width: 75px;
                height: 75px;
            }

            .brand-name {
                font-size: 35px;
            }

            .tagline {
                font-size: 13px;
            }

            .brand-description {
                margin: 0 auto 15px;
                font-size: 16px;
            }

            .laundry-illustration {
                height: 150px;
                margin: auto;
            }

            .illustration-placeholder {
                height: 140px;
                width: 220px;
            }

            .illustration-placeholder svg {
                width: 75px;
                height: 75px;
            }

            .login-card {
                justify-self: center;
                padding: 32px 25px 25px;
            }

            .login-card h1 {
                font-size: 30px;
            }
        }

        @media (max-width: 420px) {
            .brand-logo {
                gap: 12px;
            }

            .logo-icon {
                width: 60px;
                height: 60px;
            }

            .brand-name {
                font-size: 29px;
            }

            .login-card {
                padding: 27px 19px 22px;
            }

            .login-card h1 {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>
    <main class="container">

        <section class="branding">
            <div class="brand-logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 100 100" fill="none"
                         xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="12" y="22" width="58" height="68"
                              rx="8" stroke="currentColor" stroke-width="5"/>
                        <path d="M13 37H69" stroke="currentColor" stroke-width="4"/>
                        <circle cx="23" cy="29" r="2.5" fill="currentColor"/>
                        <circle cx="32" cy="29" r="2.5" fill="currentColor"/>
                        <circle cx="42" cy="60" r="18"
                                stroke="currentColor" stroke-width="5"/>
                        <path d="M26 60C31 53 36 53 42 60C48 67 53 67 58 60"
                              stroke="#20A8D2" stroke-width="5"
                              stroke-linecap="round"/>
                        <circle cx="79" cy="13" r="6"
                                stroke="#20A8D2" stroke-width="4"/>
                        <circle cx="88" cy="28" r="5"
                                stroke="#20A8D2" stroke-width="4"/>
                        <circle cx="76" cy="35" r="3" fill="#20A8D2"/>
                    </svg>
                </div>

                <div>
                    <div class="brand-name">Laundry<span>Ku</span></div>
                    <p class="tagline">Bersih · Rapi · Terpercaya</p>
                </div>
            </div>

            <p class="brand-description">
                Kelola pesanan laundry dengan mudah, cepat, dan efisien.
            </p>

            <div class="laundry-illustration">
                <img
                    src="assets/laundry.png"
                    alt="Ilustrasi keranjang dan perlengkapan laundry"
                    onerror="this.style.display='none'; document.getElementById('illustrationFallback').style.display='flex';"
                >

                <div class="illustration-placeholder"
                     id="illustrationFallback" style="display:none;">
                    <svg viewBox="0 0 120 120" fill="none"
                         xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M30 40H90L84 105H36L30 40Z"
                              fill="#80BFFF" stroke="#1979C7" stroke-width="4"/>
                        <path d="M27 40H93V30C93 26 90 23 86 23H34C30 23 27 26 27 30V40Z"
                              fill="#1979C7"/>
                        <path d="M42 55V85M60 55V85M78 55V85"
                              stroke="#EAF5FF" stroke-width="5" stroke-linecap="round"/>
                        <path d="M42 15C35 5 49 2 43 0M62 15C55 5 69 2 63 0"
                              stroke="#20A8D2" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                    <p>LAUNDRYKU</p>
                </div>
            </div>
        </section>

        <section class="login-card">
            <h1>Login Admin</h1>
            <p class="login-subtitle">
                Masuk ke akun Anda untuk mengelola pesanan laundry.
            </p>

            <?php if ($error !== ""): ?>
                <div class="error-message" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="input-group">
                    <svg class="input-icon" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.2"
                         stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21V19C4 15.7 7.6 14 12 14C16.4 14 20 15.7 20 19V21Z"/>
                    </svg>

                    <div class="input-content">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Masukkan username"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="input-group">
                    <svg class="input-icon" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.2"
                         stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <rect x="4" y="10" width="16" height="11" rx="2"/>
                        <path d="M8 10V7A4 4 0 0 1 16 7V10"/>
                        <circle cx="12" cy="15" r="1.3"/>
                    </svg>

                    <div class="input-content">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <button
                        class="toggle-password"
                        type="button"
                        id="togglePassword"
                        aria-label="Tampilkan password"
                        title="Tampilkan atau sembunyikan password"
                    >◉</button>
                </div>

                <button class="login-button" type="submit">
                    <span class="button-content">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.3"
                             stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M10 17L15 12L10 7"/>
                            <path d="M15 12H3"/>
                            <path d="M12 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H12"/>
                        </svg>
                        Login
                    </span>
                </button>
            </form>

            <footer class="login-footer">
                <div class="footer-brand">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M12 22S20 18 20 11V5L12 2L4 5V11C4 18 12 22 12 22Z"/>
                        <path d="M9 12L11 14L15 10"/>
                    </svg>
                    <span>LaundryKu - Sistem Informasi Laundry</span>
                </div>
                <p>© 2026. Semua hak dilindungi.</p>
            </footer>
        </section>

    </main>

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", function () {
            const isPassword = passwordInput.type === "password";

            passwordInput.type = isPassword ? "text" : "password";
            togglePassword.textContent = isPassword ? "◎" : "◉";
            togglePassword.setAttribute(
                "aria-label",
                isPassword ? "Sembunyikan password" : "Tampilkan password"
            );
        });
    </script>
</body>
</html>
