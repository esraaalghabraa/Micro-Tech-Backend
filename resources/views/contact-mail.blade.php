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
        .contact-message{
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
        <p style="direction: rtl">اسم المرسل: {{$message_contact['full_name']}}،</p>
        <p style="direction: rtl">البريد الالكتروني: {{$message_contact['email']}}،</p>
        <h1 style="direction: rtl">الرسالة: </h1>
        <p style="direction: rtl">{{$message_contact['message']}}</p>
        <p style="direction: rtl">{{$message_contact['inquiry_type']?$message_contact['inquiry_type']:'others'}}</p>
        <p style="direction: rtl">{{$message_contact['reaching_way']?$message_contact['reaching_way']:'others'}}</p>
    </div>
</div>
</body>
</html>
