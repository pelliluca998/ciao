<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Segresta 2.0</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Pompiere" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- Styles -->
  <style>
  html, body {
    background-color: #fff;
    color: #636b6f;
    font-family: 'Pompiere';
    font-weight: 100;
    height: 100vh;
    margin: 0;
  }

  .full-height {
    height: 100vh;
  }

  .flex-center {
    align-items: center;
    display: flex;
    justify-content: center;
    background-image: url("{{ url('assets/bg_dark.jpg') }}");
    background-size: cover
  }

  .position-ref {
    position: relative;
  }

  .top-right {
    position: absolute;
    right: 10px;
    top: 18px;
    background-color: rgba(255, 255, 255, 0.80);
  }

  .content {
    text-align: center;
    /* background-color: rgba(255, 255, 255, 0.80); */
    padding: 5px;

  }

  .title {
    font-size: 84px;
  }

  .links > a {
    color: #636b6f;
    padding: 25px 25px;
    font-size: 20px;
    font-weight: 600;
    letter-spacing: .1rem;
    text-decoration: none;
    text-transform: uppercase;
  }

  .m-b-md {
    margin-bottom: 30px;
  }
  </style>
</head>
<body>
  <div class="flex-center position-ref full-height">
    @if (Route::has('login'))
    <div class="top-right links" style="padding: 10px;">
      @if (Auth::check())
      <a href="{{ url('/home') }}">Entra!</a>
      @else
      <a href="{{ url('/login') }}">Login</a>
      <a href="{{ url('/register') }}">Registrati</a>
      @endif
    </div>
    @endif



    <div class="content">
      <img src="{{ asset('/assets/logo_new_bianco.png') }}" height="400px"/>
      <p style="color: white; font-size: 25px; margin-top: 3px;">Un nuovo modo di gestire il tuo oratorio</p>

      <div class="links" style="background-color: rgba(255, 255, 255, 0.80); padding: 10px;">
        @if (Auth::check())
        <a href="{{ url('/home') }}">Entra!</a>
        @else
        <a href="{{ url('/login') }}">Login</a>
        <a href="{{ url('/register') }}">Registrati</a>
        @endif
      </div>

    </div>
  </div>
</body>
</html>
