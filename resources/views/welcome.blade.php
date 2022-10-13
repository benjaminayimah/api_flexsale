<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <link rel="icon" href="favicon.svg">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Flexsale - API</title>
        <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Urbanist', Helvetica, sans-serif;
                font-weight: 200;
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
            }
            .position-ref {
                position: relative;
            }
            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }
            .content {
                text-align: center;
            }
            .title {
                font-size: 84px;
            }
            .links > a {
                color: #636b6f;
                padding: 0 20px;
                font-size: 15px;
                font-weight: 500;
                text-decoration: none;
            }
            .links a:hover {
                color: #566ff4
            }
            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
            <div class="content">
                <div class="title m-b-md">
                    Flexsale
                </div>
                <div class="links">
                    <a href="https://www.flexsale.store">Home</a>
                    <a href="https://www.flexsale.store/about">About</a>
                    <a href="https://app.flexsale.store/signin">Sign in</a>
                    <a href="https://app.flexsale.store/signup">Sign up</a>
                    <a href="https://www.flexsale.store/privacy-policy">Privacy policy</a>
                    <a href="https://www.flexsale.store/terms-and-conditions">Terms & Conditions</a>
                </div>
            </div>
        </div>
    </body>
</html>
