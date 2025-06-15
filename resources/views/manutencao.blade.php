<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manutenção em Andamento</title>
  <style>
    *, *:before, *:after {
      box-sizing: border-box;
      margin: 0;
    }
    html {
      font-size: 62.5%;
      font-family: sans-serif;
    }
    .splash {
      max-height: 80vh;
      position: fixed;
      left: 0;
      bottom: 0;
      z-index: -1;
      width: 100%;
      object-fit: cover;
    }
    .tt-error-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      color: #50535c;
      padding: 2rem;
      text-align: center;
      flex-direction: column;
    }
    .tt-error-image {
      margin-bottom: 2rem;
      max-width: 200px;
    }
    .tt-error-message {
      max-width: 500px;
    }
    .tt-error-message h1 {
      font-size: 3.2rem;
      margin-bottom: 2rem;
      font-weight: 600;
    }
    .tt-error-message p {
      font-size: 2.2rem;
      margin-bottom: 1.6rem;
    }
    .tt-error-message p.small {
      font-size: 1.6rem;
      line-height: 1.2;
      color: #a6a9b2;
    }
    @media (max-width: 768px) {
      .tt-error-image { display: none; }
      .tt-error-message h1 { font-size: 2.6rem; }
      .tt-error-message p { font-size: 2rem; }
    }
  </style>
</head>
<body>
  <img src="https://rafaeladeconto.com/archive/background-splash.svg" alt="Fundo abstrato" class="splash">

  <section class="tt-error-container">
    <img src="https://rafaeladeconto.com/archive/icon-maintenance.svg" alt="Ícone de manutenção" class="tt-error-image">
    <div class="tt-error-message">
      <img src="https://rafaeladeconto.com/archive/logo-tt.svg" alt="Logotipo TT" class="tt-error-logo">
      <h1>O Filehub está passando por melhorias agendadas.</h1>
      <p>Voltaremos em breve.</p>
      <p class="small">Agradecemos a sua paciência.</p>
    </div>
  </section>
</body>
</html>
