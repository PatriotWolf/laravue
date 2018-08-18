<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' }</script>
        <title>Chatbot</title>
        <style type="text/css">
            .background-image {
                position: fixed;
                background-image: url(https://i.ytimg.com/vi/NDbLo_BmfP8/maxresdefault.jpg);
                background-size: cover;
                display: block;
                filter: blur(5px);
                height: 100%;
                left: 0px;
                position: fixed;
                right: 0px;
                z-index: 1;
            }
        </style>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->

    </head>
    <body>
        <div class="background-image"></div>
        <div id="app">
            <chatbox></chatbox>
        </div>
        <script src="{{asset('js/app.js')}}"></script>

    </body>

  <script src='https://code.responsivevoice.org/responsivevoice.js'></script>
</html>
