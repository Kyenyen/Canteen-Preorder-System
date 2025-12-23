<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        /* Base Styles */
        body { 
            background-color: #f4f7f9; 
            margin: 0; 
            padding: 0; 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f7f9; padding: 40px 0; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 500px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
        
        /* Typography */
        h1 { color: #111827; font-size: 24px; font-weight: 700; margin: 0 0 16px 0; text-align: center; }
        p { color: #4b5563; font-size: 16px; line-height: 24px; margin: 0 0 20px 0; }
        
        /* Components */
        .logo-container { padding: 32px 0 20px 0; text-align: center; }
        .logo-icon { background-color: #fff7ed; color: #f97316; width: 64px; height: 64px; line-height: 64px; border-radius: 50%; display: inline-block; font-size: 28px; margin-bottom: 12px; }
        .content { padding: 0 40px 40px 40px; }
        
        /* The Button (Bulletproof) */
        .button-table { margin: 30px auto; }
        .button-link { 
            background-color: #f97316; 
            border: none;
            border-radius: 8px;
            color: #ffffff !important; 
            display: inline-block; 
            font-size: 16px; 
            font-weight: 600; 
            padding: 14px 32px; 
            text-decoration: none; 
            transition: background-color 0.2s;
        }
        
        /* Small Text */
        .security-note { font-size: 13px; color: #9ca3af; text-align: center; border-top: 1px solid #f3f4f6; padding-top: 20px; }
        .raw-link { font-size: 12px; color: #9ca3af; word-break: break-all; margin-top: 15px; }
        .footer { text-align: center; padding: 24px; font-size: 13px; color: #9ca3af; }
    </style>
</head>
<body>
    <table class="wrapper" role="presentation" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table class="main" role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="logo-container">
                            <img src="{{ $message->embed(public_path('photos/logo.png')) }}" alt="KantinCanteen Logo" style="width: 94px; height: 64px; margin-bottom: 12px;">
                            <h1>KantinCanteen</h1>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="content">
                            <p>Hi <strong>{{ $username }}</strong>,</p>
                            
                            <p>Forgot your password? It happens to the best of us. Click the button below to set a new one for your account.</p>

                            <table class="button-table" role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="border-radius: 8px;" bgcolor="#f97316">
                                        <a href="{{ $url }}" class="button-link">Reset Password</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin-bottom: 10px;">For security, this link will <strong>expire in 10 minutes</strong>. If you didn't request this, you can safely ignore this email.</p>
                            
                            <div class="security-note">
                                <p style="margin: 0;">Trouble with the button? Copy and paste this link:</p>
                                <div class="raw-link">
                                    <a href="{{ $url }}" style="color: #f97316; text-decoration: none;">{{ $url }}</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="footer">
                    <p>
                        &copy; {{ date('Y') }} KantinCanteen &bull; Skip the queue, eat better.<br>
                        Tunku Abdul Rahman University of Management and Technology
                    </p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>