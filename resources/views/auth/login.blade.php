<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --color-bright: #f2f1c6;
            --color-orange: #97a6b7ff;
            --body-background-color: #2c3135;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: var(--body-background-color);
            background-image: url('/images/ata-entrance.jpg');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            position: relative;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            image-rendering: pixelated;
        }
        body::before {
            content: '';
            position: fixed;
            background: rgba(34, 49, 63, 0.5);
            z-index: 1;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.1) 0%,
                rgba(255, 255, 255, 0.05) 50%,
                rgba(255, 255, 255, 0.1) 100%
            );
            z-index: 0;
            pointer-events: none;
        }
        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 370px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 7vh;
        }
        .login-form {
            width: 100%;
            background: rgba(255,255,255,0.13);
            border-radius: 1.2rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            padding: 2.2rem 1.5rem 1.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            backdrop-filter: blur(3px);
        }
        .login-form h2 {
            color: var(--color-bright);
            font-weight: 700;
            margin-bottom: 1.7rem;
            font-size: 2rem;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .input-group {
            width: 100%;
            margin-bottom: 1.2rem;
            position: relative;
        }
        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c7a89;
            font-size: 1.1rem;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.5rem;
            border: none;
            border-radius: 0.7rem;
            background: #eaf0f6;
            font-size: 1.08rem;
            color: #222;
            font-family: inherit;
            outline: none;
            transition: box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(34,49,63,0.04);
        }
        .login-form input[type="text"]:focus,
        .login-form input[type="password"]:focus {
            box-shadow: 0 0 0 2px var(--color-orange);
            background: #fff;
        }
        .login-form button {
            width: 100%;
            background: var(--color-orange);
            color: #fff;
            padding: 0.95rem 0;
            border-radius: 0.7rem;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            margin-top: 0.5rem;
            margin-bottom: 1.2rem;
            transition: background 0.2s, transform 0.2s;
            box-shadow: 0 2px 8px rgba(34,49,63,0.08);
        }
        .login-form button:hover {
            background: #000511ff;
            transform: translateY(-2px) scale(1.03);
        }
        .login-form .options {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .remember-section {
            display: flex;
            align-items: center;
            gap: 0.2em;
        }

        .checkbox-wrapper-31 {
            position: relative;
            display: inline-block;
            width: 28px;
            height: 28px;
            vertical-align: middle;
            margin-right: 0;
        }
        .checkbox-wrapper-31 .background {
            fill: #ccc;
            transition: ease all 0.6s;
        }
        .checkbox-wrapper-31 .stroke {
            fill: none;
            stroke: #fff;
            stroke-miterlimit: 10;
            stroke-width: 2px;
            stroke-dashoffset: 100;
            stroke-dasharray: 100;
            transition: ease all 0.6s;
        }
        .checkbox-wrapper-31 .check {
            fill: none;
            stroke: #fff;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2px;
            stroke-dashoffset: 22;
            stroke-dasharray: 22;
            transition: ease all 0.6s;
        }
        .checkbox-wrapper-31 input[type=checkbox] {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            margin: 0;
            opacity: 0;
            -appearance: none;
            -webkit-appearance: none;
        }
        .checkbox-wrapper-31 input[type=checkbox]:hover {
            cursor: pointer;
        }
        .checkbox-wrapper-31 input[type=checkbox]:checked + svg .background {
            fill: #91c679ff;
        }
        .checkbox-wrapper-31 input[type=checkbox]:checked + svg .stroke {
            stroke-dashoffset: 0;
        }
        .checkbox-wrapper-31 input[type=checkbox]:checked + svg .check {
            stroke-dashoffset: 0;
        }
        .remember-label {
            color: var(--color-bright);
            font-size: 0.98rem;
            font-weight: 500;
            vertical-align: middle;
        }
        .login-form .options a {
            color: var(--color-orange);
            font-size: 0.98rem;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .login-form .options a:hover {
            color: var(--color-bright);
        }

        #footer-contact-info {
            position: fixed;
            left: 18px;
            bottom: 18px;
            z-index: 200;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            pointer-events: none;
        }
        #footer-contact-info .links {
            display: flex;
            min-width: 0;
            gap: 12px;
            pointer-events: auto;
        }
        #footer-contact-info a {
            text-decoration: none;
            color: rgba(242, 241, 198, 0.7);
            font-size: 1.3rem;
        }
        #footer-contact-info .links a {
            display: block;
            position: relative;
        }
        #footer-contact-info .links a::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 1px solid transparent;
            transition: all 0.3s;
        }
        #footer-contact-info .links a:hover::after {
            --transformed-box-diagonal-length: 164%;
            width: var(--transformed-box-diagonal-length);
            left: calc((100% - var(--transformed-box-diagonal-length)) / 2);
        }
        #footer-contact-info .links a div {
            position: relative;
            display: block;
            width: 32px;
            height: 32px;
            transition: transform 0.3s;
        }
        #footer-contact-info .links a:hover div {
            transform: rotate(-35deg) skew(20deg);
        }
        #footer-contact-info .links a span {
            border: 1.5px solid rgba(242, 241, 198, 0.4);
            background: transparent;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transition: 0.5s;
            border-radius: 5px;
        }
        #footer-contact-info .links a span:nth-child(5) {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        #footer-contact-info .links a span i {
            font-size: 1.1em;
            opacity: 0.7;
        }
        #footer-contact-info .links a:hover,
        #footer-contact-info .links a:hover span i {
            color: var(--color-bright);
            opacity: 1;
        }
        #footer-contact-info .links a:hover span {
            border-color: var(--color-bright);
        }
        #footer-contact-info .links a:hover span:nth-child(5) { transform: translate(12px, -12px); opacity: 1; }
        #footer-contact-info .links a:hover span:nth-child(4) { transform: translate(9px, -9px); opacity: 0.8; }
        #footer-contact-info .links a:hover span:nth-child(3) { transform: translate(6px, -6px); opacity: 0.6; }
        #footer-contact-info .links a:hover span:nth-child(2) { transform: translate(3px, -3px); opacity: 0.4; }
        #footer-contact-info .links a:hover span:nth-child(1) { opacity: 0.2; }
        #footer-contact-info .links a.orange-background span { background: var(--color-orange); border-color: var(--color-bright); }
        #footer-contact-info .links a.orange-background span:nth-child(5) { background: var(--body-background-color); }
        #footer-contact-info .links a.orange-shadow:hover span { box-shadow: -1px 1px 3px var(--color-orange); }
        #footer-contact-info .links a.orange-border span { border-color: var(--color-orange); }
        #footer-contact-info .links a.orange-border span:nth-child(5) { border-color: var(--color-bright); }
        .footer-social-copyright {
            color: var(--color-bright);
            font-size: 0.75em;
            font-family: inherit;
            text-shadow: 0 1px 4px rgba(0,0,0,0.3);
            letter-spacing: 0.01em;
            user-select: none;
            opacity: 0.85;
            margin-top: 2px;
            pointer-events: auto;
        }
        @media (max-width: 600px) {
            #footer-contact-info { left: 6px; bottom: 6px; }
            #footer-contact-info .links a div { width: 22px; height: 22px; }
            .footer-social-copyright { font-size: 0.6em; }
        }
        .login-error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="login-container">

        <form class="login-form" method="POST" action="{{ route('login') }}">
            @csrf
            <h2>Giriş Yap</h2>
            @if($errors->any())
                <div class="login-error-message">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" id="username" name="username" value="{{ old('username', \Illuminate\Support\Facades\Cookie::get('remember_username')) }}" required autofocus autocomplete="username" placeholder="Kullanıcı Adı">
            </div>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" value="{{ \Illuminate\Support\Facades\Cookie::get('remember_password') }}" required autocomplete="current-password" placeholder="Şifre">
            </div>
            <div class="options">
                <div class="remember-section">
                    <label class="checkbox-wrapper-31">
                      <input type="checkbox" id="cbx" name="remember"/>
                      <svg viewBox="0 0 28 28">
                        <circle class="background" cx="14" cy="14" r="14"></circle>
                        <circle class="stroke" cx="14" cy="14" r="11.2"></circle>
                        <polyline class="check" points="9.2 14.2 12.2 17.5 20.2 10.1"></polyline>
                      </svg>
                    </label>
                    <span class="remember-label">Beni hatırla</span>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Şifremi unuttum?</a>
                @endif
            </div>
            <button type="submit">Giriş</button>
        </form>
    </div>
    <div id="footer-contact-info">
      <div class="links">
        <a href="https://www.facebook.com/atauni1957" target="_blank" aria-label="Facebook" class="orange-shadow">
          <div>
            <span></span><span></span><span></span><span></span>
            <span><i class="fa-brands fa-facebook-f"></i></span>
          </div>
        </a>
        <a href="https://x.com/atauniv1957" target="_blank" aria-label="X" class="orange-shadow">
          <div>
            <span></span><span></span><span></span><span></span>
            <span><i class="fa-brands fa-x-twitter"></i></span>
          </div>
        </a>
        <a href="https://www.instagram.com/atauni1957" target="_blank" aria-label="Instagram" class="orange-shadow">
          <div>
            <span></span><span></span><span></span><span></span>
            <span><i class="fa-brands fa-instagram"></i></span>
          </div>
        </a>
        <a href="https://www.youtube.com/c/ATABAUM/about" target="_blank" aria-label="YouTube" class="orange-shadow">
          <div>
            <span></span><span></span><span></span><span></span>
            <span><i class="fa-brands fa-youtube"></i></span>
          </div>
        </a>
        <a href="https://tr.linkedin.com/school/atauni1957" target="_blank" aria-label="LinkedIn" class="orange-shadow">
          <div>
            <span></span><span></span><span></span><span></span>
            <span><i class="fa-brands fa-linkedin-in"></i></span>
          </div>
        </a>
      </div>
      <div class="footer-social-copyright">
        © Atatürk Üniversitesi Bilgisayar Bilimleri Araştırma ve Uygulama Merkezi
      </div>
    </div>

    {{-- Beni Hatırla checkbox'ının durumunu çereze göre ayarlayan JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rememberCheckbox = document.getElementById('cbx');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');

            if (usernameInput.value !== '' || passwordInput.value !== '') {
                rememberCheckbox.checked = true;
            }
        });
    </script>
</body>
</html>
