<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{URL::asset('assets/css/select2.min.css') }}" rel="stylesheet">
    <style>
        #loader {
            transition: all .3s ease-in-out;
            opacity: 1;
            visibility: visible;
            position: fixed;
            height: 100vh;
            width: 100%;
            background: #fff;
            z-index: 90000
        }

        #loader.fadeOut {
            opacity: 0;
            visibility: hidden
        }

        .spinner {
            width: 40px;
            height: 40px;
            position: absolute;
            top: calc(50% - 20px);
            left: calc(50% - 20px);
            background-color: #333;
            border-radius: 100%;
            -webkit-animation: sk-scaleout 1s infinite ease-in-out;
            animation: sk-scaleout 1s infinite ease-in-out
        }

        @-webkit-keyframes sk-scaleout {
            0% {
                -webkit-transform: scale(0)
            }
            100% {
                -webkit-transform: scale(1);
                opacity: 0
            }
        }

        @keyframes sk-scaleout {
            0% {
                -webkit-transform: scale(0);
                transform: scale(0)
            }
            100% {
                -webkit-transform: scale(1);
                transform: scale(1);
                opacity: 0
            }
        }
    </style>
    <link href="{{URL::asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{URL::asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{URL::asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{URL::asset('assets/css/tabulator-boot.min.css') }}" rel="stylesheet">
    <link href="{{URL::asset('assets/css/animate.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
          integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    @yield('custom-css')
    <link href="{{URL::asset('assets/css/override.css') }}" rel="stylesheet">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-129314973-5"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-129314973-5');
    </script>
</head>
<body class="app loading">
<div id="loader">
    <div class="spinner"></div>
</div>
<script>
    window.addEventListener('load', () => {
        const loader = document.getElementById('loader');
        setTimeout(() => {
            loader.classList.add('fadeOut');
        }, 300);
    });
</script>
<div>
    <div class="sidebar">
        <div class="sidebar-inner">
            @include('includes.sidebar')
        </div>
    </div>
    <div class="page-container">
        <div class="header navbar">
            @include('includes.header')
        </div>
        <main class="main-content bgc-grey-100">
            <div id="mainContent">
                @yield('content')
            </div>
        </main>
        @include('includes.footer')
    </div>
</div>
<script type="text/javascript" src="{{URL::asset('assets/js/jquery.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/vendor.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/bundle.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/moment.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/inputmask.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/bootstrap-notify.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/tabulator.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/app.config.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/orders-table.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/custom-table.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/select2.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/vi.js')}}"></script>
<link href="{{URL::asset('assets/css/custom.css') }}" rel="stylesheet">
@yield('script_inline')
@yield('script')
@if (session('message'))
    <?php
    $title = session("message")['title'];
    $content = session("message")['content'];
    $type = session("message")['type'];
    ?>
    <script>
        makeAlert('<?= $title ?>', '<?= $content ?>', '<?= $type ?>');
    </script>
@endif

</body>
</html>