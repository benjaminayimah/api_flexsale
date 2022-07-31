<!doctype html>
<html lang="eng">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<table style="width: 100%; background-color: #F4F4F7">
    <tbody>
        <tr>
            <td style="padding: 80px 0;">
                <table style="width: 40%; max-width: 550px; min-width: 220px; margin: 0 auto">
                    <tbody>
                        <tr>
                            <td style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 15px; font-weight: 400; line-height: 1.5">
                                <div class="body-card" style="background-color: #fff; padding: 30px; border-radius: 16px; border: 1px solid #F0F0F0">
                                    <p style="text-align: center">
                                        <a href="https://www.flexsale.store" target="_blank">
                                            <img aria-hidden="true" src="https://api.flexsale.store/storage/flexsale-email-logo.png" height="40" alt="flexsale">
                                        </a>
                                    </p>
                                    <div style="text-align: center; padding: 12px 8px">
                                        <span style="border-bottom: 1px solid #F2F2F2; color: rgb(34, 34, 34); padding-bottom: 6px; font-weight: 500; font-size: 25px; display:inline-block">
                                        {{ $title }}
                                        </span>
                                    </div>
                                    <div style="color: rgb(34, 34, 34);">
                                        <p>
                                            <strong>Hello!</strong>
                                        </p>
                                        <p>
                                            You have requested a password reset. Please proceed by clicking on the link below. Or copy and paste the link into your browser if you're having trouble clicking.
                                        </p>
                                        <p>
                                            <a href="{{ $url }}" target="_blank">{{ $url }}</a>
                                        </p>
                                        <p>
                                            Please ignore this message if you did not request a password reset.
                                        </p>
                                    </div>
                                </div>
                                <div style="padding: 20px 40px 0 40px;text-align: center">
                                    <div style="font-size: 12px; color: #7A7D84">
                                        <span>You received this email because you have created an account with Flexsale.</span>
                                        <span> For more enquiries, contact us at <a style="color: #212121;" href="mailTo:info@flexsale.store">info@flexsale.store</a></span>
                                        <span> or visit our website <a style="color: #212121;" href="https://www.flexsale.store" target="_blank">www.flexsale.store</a> for more information.</span>
                                        <br>
                                        <div>© 2022 Flexsale. All Rights Reserved.</div>
                                        <span style="opacity: 0"> {{ $hideme }} </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>
