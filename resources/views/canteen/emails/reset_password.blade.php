<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        body { background-color: #f3f4f6; color: #374151; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding-bottom: 60px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 600px; max-width: 600px; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background-color: #f97316; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.025em; }
        .content { padding: 40px; }
        .button { display: inline-block; background-color: #f97316; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; margin-bottom: 20px; }
        .footer { text-align: center; font-size: 12px; color: #9ca3af; margin-top: 20px; }
    </style>
</head>
<body>
    <table class="wrapper" role="presentation">
        <tr>
            <td align="center">
                <table class="main" role="presentation">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                           <h1>UniCanteen</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td class="content">
                            <p style="font-size: 18px; margin-bottom: 20px;">Hello {{ $username }},</p>
                            
                            <p style="line-height: 1.6; margin-bottom: 20px;">
                                You are receiving this email because we received a password reset request for your account. If you did not request a password reset, no further action is required.
                            </p>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}" class="button">Reset Password</a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="font-size: 14px; color: #6b7280; margin-top: 20px;">
                                Or copy and paste this link into your browser:<br>
                                <a href="{{ $url }}" style="color: #f97316; word-break: break-all;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Footer -->
                <div class="footer">
                    <p>&copy; {{ date('Y') }} UniCanteen. All rights reserved.</p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>