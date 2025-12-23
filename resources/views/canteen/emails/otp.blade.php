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
        
        /* OTP Box Styles */
        .otp-box {
            background-color: #ffff;
            border: 2px dashed #f97316;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin: 30px 0;
            background-color: #fff7ed;
        }
        
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #ea580c;
            display: block;
            font-family: 'Courier New', monospace;
        }
        
        .otp-label {
            display: block;
            text-transform: uppercase;
            font-size: 11px;
            color: #9a3412;
            font-weight: 700;
            margin-top: 8px;
            letter-spacing: 1px;
        }
        
        /* Footer */
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
                            <img src="{{ config('logo.logo_base64') }}" alt="KantinCanteen Logo" style="width: 94px; height: 64px; margin-bottom: 12px;">
                            <h1>KantinCanteen</h1>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="content">
                            <p>Hi there,</p>
                            
                            <p>Thank you for signing up with KantinCanteen! To complete your registration, please verify your email address using the code below:</p>

                            <div class="otp-box">
                                <span class="otp-code">{{ $otp }}</span>
                                <span class="otp-label">Verification Code</span>
                            </div>

                            <p style="margin-bottom: 10px;">For security, this code will <strong>expire in 60 seconds</strong>.</p>
                            <p>If you didn't request this code, you can safely ignore this email.</p>
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