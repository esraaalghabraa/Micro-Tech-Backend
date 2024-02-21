<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{asset('favicon.png')}}">
    <title>Micro Tech</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #fafafa;
            font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';
            line-height: 1.6;
            padding: 20px;
            direction: rtl;
        }
        .container{
            margin: 0 auto;
            overflow: hidden;
            background-color: #fafafa;
            border-radius: 10px;
            padding: 20px;
            color: #262626;
            line-height: 1.8;
            word-break:break-word;
            text-align: center;
        }

        .logo{
            width: 100%;
            border-bottom: 2px solid #E0E2E5;
            padding: 20px 0;
        }

        .message-content{
            padding: 50px 0;
            text-align: right;
            border-bottom: 2px solid #E0E2E5;
            font-size: 16px;
        }
        p{
            font-size: 16px;
            padding-bottom: 8px;
        }
        .verification-code{
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">
            <img src="https://ssl.gstatic.com/ui/v1/icons/mail/rfr/logo_gmail_lockup_default_1x_rtl_r5.png" alt="logo">
    </div>
    <div class="message-content">
        <p style="direction: rtl">مرحباً {{$message_verify['user_name']}}،</p>
        <p style="direction: rtl"> استخدم الكود التالي لتسجيل الدخول في <span style=" font-family: 'SF Pro Text'; ">Micro Tect</span></p>
        <div class="verification-code">123456</div>
        <p class="note">الرجاء استخدام هذا الرمز لتسجيل الدخول إلى حسابك.</p>
    </div>
</div>
</body>
</html>
{{--    <!DOCTYPE html>--}}
{{--<html lang="ar">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
{{--    <title>Email Verification Code</title>--}}
{{--    <style>--}}
{{--        @import url('https://fonts.googleapis.com/css2?family=Google+Sans:wght@300;400;500;700&display=swap');--}}

{{--        body {--}}
{{--            font-family: "Google Sans",Roboto,RobotoDraft,Helvetica,Arial,sans-serif;--}}
{{--            margin: 0;--}}
{{--            padding: 20px;--}}
{{--            background-color: #fafafa;--}}
{{--            direction: rtl; /* Set text direction to right-to-left for Arabic */--}}
{{--        }--}}

{{--        .container {--}}
{{--            max-width: 600px;--}}
{{--            margin: 0 auto;--}}
{{--            background-color: #fafafa;--}}
{{--            padding: 20px;--}}
{{--            border-radius: 8px;--}}
{{--            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);--}}
{{--            text-align: right; /* Align text to the right for Arabic */--}}
{{--        }--}}

{{--        h2 {--}}
{{--            color: #007bff;--}}
{{--            text-align: center;--}}
{{--            direction: rtl !important;--}}
{{--            font-size: 24px;--}}
{{--            font-weight: 700;--}}
{{--            line-height: 1.2;--}}
{{--            word-wrap: normal--}}
{{--        }--}}

{{--        p {--}}
{{--            font-size: 22px;--}}
{{--            color: #333;--}}
{{--            line-height: 1.6;--}}
{{--            direction: rtl;--}}
{{--        }--}}

{{--        .verification-code {--}}
{{--            font-size: 24px;--}}
{{--            font-weight: bold;--}}
{{--            color: #007bff;--}}
{{--            text-align: center;--}}
{{--            margin: 20px 0;--}}
{{--        }--}}

{{--        .note {--}}
{{--            color: #555;--}}
{{--            margin-bottom: 20px;--}}
{{--            font-size: 22px;--}}
{{--        }--}}

{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}

{{--<div class="container">--}}
{{--    <img alt="logo" src="https://ci3.googleusercontent.com/meips/ADKq_NZXJW4dO14U_fyZ8iiaGF_BhetjoRrxj_X5Z8AczCtAlIAds6P5bgI_vU5Pvx04wSLT-Ah4ojbyCukPz0GXNvcwfgG4yy1DDcFHfVK7Z7rLRTkKTI3TPbUv9WuynUEk3IURtVDU5q-Ci0kDCxnjILTWYY6a0EJzdolFp7Q5CT9UtkecKV07I_rWrEv8NA-R021CwpIdPisZzBguIer6NixH5tCNEP9EaFXLKhXFnOTyC43TISIRWPBYkei_NPviYjeU0w33iCSFhWPQvHjhTm_5f2ZbE2rIUa4_G3_Gdlt0ft-lWR2SuHUVdmbtPHg19iM=s0-d-e1-ft#https://static.licdn.com/sc/p/com.linkedin.email-assets-frontend%3Aemail-assets-frontend-static-content%2B__latest__/f/%2Femail-assets-frontend%2Fimages%2Femail%2Fphoenix%2Flogos%2Flogo_phoenix_header_blue_78x66_v1.png">--}}
{{--    <h2>رمز التحقق</h2>--}}
{{--    <p>مرحبًا <span>{{$message_verify['user_name']}}</span></p>--}}
{{--    <p>رمز التحقق الخاص بك هو</p>--}}
{{--    <div class="verification-code">{{$message_verify['verify_code']}}</div>--}}
{{--    <p class="note">الرجاء استخدام هذا الرمز لتسجيل الدخول إلى حسابك.</p>--}}
{{--</div>--}}

{{--</body>--}}
{{--</html>--}}
