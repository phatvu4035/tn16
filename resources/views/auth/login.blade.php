<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title> Đăng nhập
    </title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="c1rW4NnXqxFMJo9RC5SvMx9ze1y92xHJGeVQyA3O">
    <style>
        #loader{transition:all .3s ease-in-out;opacity:1;visibility:visible;position:fixed;height:100vh;width:100%;background:#fff;z-index:90000}#loader.fadeOut{opacity:0;visibility:hidden}.spinner{width:40px;height:40px;position:absolute;top:calc(50% - 20px);left:calc(50% - 20px);background-color:#333;border-radius:100%;-webkit-animation:sk-scaleout 1s infinite ease-in-out;animation:sk-scaleout 1s infinite ease-in-out}@-webkit-keyframes sk-scaleout{0%{-webkit-transform:scale(0)}100%{-webkit-transform:scale(1);opacity:0}}@keyframes sk-scaleout{0%{-webkit-transform:scale(0);transform:scale(0)}100%{-webkit-transform:scale(1);transform:scale(1);opacity:0}}
    </style>
    <link href="{{URL::asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{URL::asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{URL::asset('assets/css/animate.min.css') }}" rel="stylesheet">
    <link href="{{URL::asset('assets/css/custom.css') }}" rel="stylesheet">

    <style>
        .login-page, .register-page {
            background: #d2d6de;
        }

        .login-box-msg, .register-box-msg {
            margin: 0;
            text-align: center;
            padding: 0 20px 20px 20px;
        }

        .login-box, .register-box {
            width: 360px;
            margin: 7% auto;
        }

        .login-logo, .register-logo {
            font-size: 35px;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 300;
        }

        .login-logo a, .register-logo a {
            color: #444;
        }

        .login-box-body, .register-box-body {
            background: #fff;
            padding: 20px;
            border-top: 0;
            color: #666;
        }

        .btn.btn-flat {
            border-radius: 0;
            box-shadow: none;
            border-width: 1px;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .btn-social > :first-child {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 32px;
            line-height: 34px;
            font-size: 1.6em;
            text-align: center;
            border-right: 1px solid rgba(0, 0, 0, 0.2);
        }

        .btn-google {
            color: #fff;
            background-color: #dd4b39;
            border-color: rgba(0, 0, 0, 0.2);
        }

        .btn-social {
            position: relative;
            padding-left: 44px;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn.btn-flat {
            border-radius: 0;
            box-shadow: none;
            border-width: 1px;
        }

        .social-auth-links {
            margin: 10px 0;
            overflow: hidden;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .btn {
            display: inline-block;
            margin-bottom: 0;
            font-weight: 300;
            text-align: center;
            vertical-align: middle;
            touch-action: manipulation;
            cursor: pointer;
            background-image: none;
            border: 1px solid transparent;
            white-space: nowrap;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            border-radius: 4px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .btn-google {
            color: #fff;
            background-color: #dd4b39;
            border-color: rgba(0, 0, 0, 0.2);
        }

        .btn-social {
            position: relative;
            padding-left: 44px;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>


    <!--<script src="http://hrview.topica.vn/js/snowstorm.js"></script>-->
    <script>
        window.Laravel = {"csrfToken": "c1rW4NnXqxFMJo9RC5SvMx9ze1y92xHJGeVQyA3O"}    </script>
</head>

<body class="hold-transition login-page">
<div id="app">
    <div class="login-box">
        <div class="login-logo">
            <a href=""><b>TN</b>2018</a>
        </div><!-- /.login-logo -->

        <div class="login-box-body">
            <p class="login-box-msg"> Vui lòng sử dụng email Topica </p>


            <div class="social-auth-links text-center">
                <a href="/auth/google" class="btn btn-block btn-social btn-google btn-flat">
                    <i class="fa fa-google-plus" style="margin-left: 2px;"></i>
                    Đăng nhập sử dụng Google+</a>
            </div>
        </div><!-- /.login-box-body -->

    </div><!-- /.login-box -->
</div>
<!-- Compiled app javascript -->
<script type="text/javascript" src="{{URL::asset('assets/js/jquery.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/vendor.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/bundle.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/bootstrap-notify.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/app.config.js')}}"></script>
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