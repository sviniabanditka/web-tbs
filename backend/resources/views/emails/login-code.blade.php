<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Login Code: {{ $code  }}</title>
  <style>
    body, h1, p, div {
      margin: 0;
      padding: 0;
    }
    body {
      font-family: Arial, sans-serif;
      background-color: #ffffff;
      color: #000000;
      line-height: 1.5;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 24px;
    }
    .header {
      text-align: center;
      padding-bottom: 24px;
    }
    .logo {
      font-size: 24px;
      font-weight: bold;
      letter-spacing: 1px;
    }
    .title {
      font-size: 20px;
      margin-top: 16px;
    }
    .body {
      background-color: #f8f8f8;
      padding: 24px;
      margin-bottom: 24px;
      border-radius: 8px;
      text-align: center;
    }
    .code-box {
      display: inline-block;
      font-size: 32px;
      letter-spacing: 4px;
      font-weight: bold;
      padding: 16px 32px;
      background-color: #ffffff;
      border: 1px solid #dddddd;
      border-radius: 4px;
      margin: 16px 0;
    }
    .text {
      font-size: 14px;
      color: #555555;
      margin-top: 8px;
    }
    .footer {
      font-size: 12px;
      text-align: center;
      color: #888888;
      border-top: 1px solid #eeeeee;
      padding-top: 16px;
    }
    .social {
      margin: 12px 0;
    }
    .social a {
      margin: 0 8px;
      text-decoration: none;
      color: #000000;
      font-size: 16px;
    }
    @media(max-width: 400px) {
      .code-box { font-size: 24px; padding: 12px 20px; letter-spacing: 3px; }
      .container { padding: 16px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="logo">Tactorium</div>
      <div class="title">Login code</div>
    </div>

    <div class="body">
      <p>We have received a login request. Enter the code below in the application window:</p>
      <div class="code-box">{{ $code }}</div>
      <p class="text">If you did not request a code, simply ignore this email. The code will be valid for 15 minutes.</p>
    </div>

    <div class="footer">
      <p>Tactorium - Turn-based Strategy Game</p>
      <div class="social">
        <a href="https://discord.com/">Discord</a>
        <a href="https://github.com/sviniabanditka/web-tbs">GitHub</a>
        <a href="https://threads.com/cringeneer_dev">Threads</a>
        <a href="mailto:support@tactorium.app">Email</a>
        <a href="https://tactorium.app">App</a>
      </div>
      <p>© 2025 Tactorium.</p>
    </div>
  </div>
</body>
</html>
