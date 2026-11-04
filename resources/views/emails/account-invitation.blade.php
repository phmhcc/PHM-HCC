<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to PHM HCC Church Website – Activate Your Account</title>
    <style>
        @media only screen and (max-width: 600px) {
            .email-outer { padding: 8px !important; }
            .email-shell { width: 100% !important; }
            .email-gutter { padding-right: 20px !important; padding-left: 20px !important; }
            .email-column { display: block !important; width: 100% !important; }
            .email-header-title { font-size: 24px !important; }
            .email-column-content { padding: 18px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:16px;background:#f2f5fa;font-family:Arial,Helvetica,sans-serif;color:#202936;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f2f5fa;">
        <tr>
            <td class="email-outer" align="center" style="padding:16px;">
                <table class="email-shell" role="presentation" width="640" cellpadding="0" cellspacing="0" border="0"
                    style="width:100%;max-width:640px;background:#fff;">
                    <tr>
                        <td class="email-gutter" style="padding:18px 28px;background:#082f63;border-bottom:5px solid #f5ad18;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="76" valign="middle">
                                        <img src="{{ $logoUrl }}" width="66" height="66" alt="PHM HCC Church logo"
                                            style="display:block;width:66px;height:66px;border:0;border-radius:50%;background:#fff;">
                                    </td>
                                    <td valign="middle" style="padding-left:12px;color:#fff;">
                                        <p style="margin:0;color:#fff;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:bold;letter-spacing:1px;">
                                            PHM HCC
                                        </p>
                                        <p style="margin:3px 0 0;color:#f5ad18;font-size:12px;letter-spacing:1px;">
                                            HOUSE OF CHRIST CHURCH
                                        </p>
                                    </td>
                                    <td align="right" valign="middle" style="padding-left:8px;color:#f5ad18;font-family:Georgia,'Times New Roman',serif;font-size:21px;font-style:italic;">
                                        You're Invited!
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-gutter" style="padding:24px 28px 18px;">
                            <h1 class="email-header-title" style="margin:0 0 8px;color:#082f63;font-size:30px;line-height:1.2;">
                                Welcome to PHM HCC!
                            </h1>
                            <p style="margin:0;color:#202936;font-size:15px;line-height:1.6;">
                               Hello {{ $userName }}, An administrator has created an account for you on the PHM HCC Church Website.
                                You have been added as a valued leader in our church community.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-gutter" style="padding:0 28px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td class="email-column" width="54%" valign="top" style="width:54%;padding-right:10px;">
                                        <table class="email-column-content" role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                            style="background:#f1f5fb;border-radius:12px;">
                                            <tr>
                                                <td style="padding:18px 20px;color:#202936;font-size:14px;line-height:1.5;">
                                                    <p style="margin:0 0 14px;"><strong style="color:#082f63;">Full Name:</strong><br>{{ $userName }}</p>
                                                    <p style="margin:0 0 14px;overflow-wrap:anywhere;"><strong style="color:#082f63;">Email Address:</strong><br>{{ $userEmail }}</p>
                                                    <p style="margin:0 0 14px;"><strong style="color:#082f63;">Department:</strong><br>{{ $userDepartment }}</p>
                                                    <p style="margin:0;"><strong style="color:#082f63;">Assigned Role:</strong><br>{{ $userRole }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="email-column" width="46%" valign="middle" style="width:46%;padding-left:10px;">
                                        <table class="email-column-content" role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding:10px;color:#202936;font-size:14px;line-height:1.6;">
                                                    <h2 style="margin:0 0 10px;color:#082f63;font-size:18px;">Activate Your Account</h2>
                                                    <p style="margin:0 0 16px;">
                                                        Create your password to activate your account, then sign in using your email address.
                                                    </p>
                                                    <p style="margin:0 0 8px;">
                                                        <a href="{{ $resetUrl }}"
                                                            style="display:inline-block;padding:13px 18px;border-radius:6px;background:#082f63;color:#fff;text-decoration:none;font-weight:bold;">
                                                            Set Your Password
                                                        </a>
                                                    </p>
                                                    <p style="margin:0;color:#5a6573;font-size:12px;font-style:italic;">
                                                        This secure link is valid for a limited time.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-gutter" style="padding:0 28px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background:#f1f5fb;border-radius:12px;">
                                <tr>
                                    <td class="email-column email-column-content" width="50%" valign="top"
                                        style="width:50%;padding:20px 22px;color:#202936;font-size:13px;line-height:1.6;">
                                        <h2 style="margin:0 0 8px;color:#082f63;font-size:16px;">Why Did I Receive This Email?</h2>
                                        <p style="margin:0;">
                                            An administrator registered you on the PHM HCC Church Website. Set your password to access your account securely.
                                        </p>
                                    </td>
                                    <td class="email-column email-column-content" width="50%" valign="top"
                                        style="width:50%;padding:20px 22px;border-left:1px solid #c9d5e5;color:#202936;font-size:13px;line-height:1.6;">
                                        <h2 style="margin:0 0 8px;color:#082f63;font-size:16px;">We're Glad to Have You With Us!</h2>
                                        <p style="margin:0 0 10px;">
                                            May God bless you as you serve in His vineyard. If you did not expect this invitation, contact the church administrator.
                                        </p>
                                        <p style="margin:0;padding-top:8px;border-top:1px solid #f5ad18;color:#082f63;font-weight:bold;">
                                            PHM HCC Church Administration Team
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:14px 20px;background:#082f63;color:#fff;font-size:12px;line-height:1.5;">
                            PHM HCC Church Website · Tanzania
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
