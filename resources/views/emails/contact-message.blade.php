
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message | PHM HCC</title>
</head>
<body style="margin:0; padding:0; background-color:#f2f4f7; font-family:Arial, Helvetica, sans-serif; color:#333333;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background-color:#f2f4f7; padding:30px 10px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                       style="max-width:650px; background-color:#ffffff; border-radius:12px; overflow:hidden;">

                    {{-- Church header --}}
                    <tr>
                        <td align="center"
                            style="background-color:#003366; padding:30px 20px; border-bottom:5px solid #FFD700;">

                            <h1 style="margin:0; color:#FFD700; font-size:25px;">
                                PHM HCC
                            </h1>

                            <p style="margin:10px 0 0; color:#ffffff; font-size:14px; line-height:1.6;">
                                Pentecostal Holiness Mission Hope Christian Church
                            </p>

                            <p style="margin:8px 0 0; color:#ffffff; font-size:13px;">
                                A New Contact Message
                            </p>
                        </td>
                    </tr>

                    {{-- Email content --}}
                    <tr>
                        <td style="padding:30px 25px;">

                            <h2 style="margin:0 0 15px; color:#003366; font-size:22px;">
                                Contact Form Submission
                            </h2>

                            <p style="font-size:15px; line-height:1.7; color:#555555;">
                                Hello PHM HCC Team,
                            </p>

                            <p style="font-size:15px; line-height:1.7; color:#555555;">
                                Someone has submitted a message through the church website.
                                Here are the details:
                            </p>

                            {{-- Sender details --}}
                            <table role="presentation" width="100%" cellpadding="10" cellspacing="0"
                                   style="border-collapse:collapse; margin:20px 0; font-size:14px;">

                                <tr>
                                    <td width="30%" style="background-color:#f2f5f9; border:1px solid #e0e5eb; color:#003366; font-weight:bold;">
                                        Full Name
                                    </td>
                                    <td style="border:1px solid #e0e5eb;">
                                        {{ $data['name'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="background-color:#f2f5f9; border:1px solid #e0e5eb; color:#003366; font-weight:bold;">
                                        Email
                                    </td>
                                    <td style="border:1px solid #e0e5eb; overflow-wrap:anywhere;">
                                        {{ $data['email'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="background-color:#f2f5f9; border:1px solid #e0e5eb; color:#003366; font-weight:bold;">
                                        Phone
                                    </td>
                                    <td style="border:1px solid #e0e5eb;">
                                        {{ $data['phone'] ?: 'Not provided' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="background-color:#f2f5f9; border:1px solid #e0e5eb; color:#003366; font-weight:bold;">
                                        Subject
                                    </td>
                                    <td style="border:1px solid #e0e5eb;">
                                        {{ $data['subject'] ?: 'General Inquiry' }}
                                    </td>
                                </tr>
                            </table>

                            {{-- Message --}}
                            <h3 style="margin:25px 0 10px; color:#003366; font-size:18px;">
                                Message
                            </h3>

                            <div style="background-color:#fffbea; border-left:5px solid #FFD700; padding:18px; border-radius:5px; font-size:15px; line-height:1.8; color:#333333; overflow-wrap:anywhere;">
                                {!! nl2br(e($data['message'])) !!}
                            </div>

                            {{-- Reply button --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:25px 0 10px;">
                                <tr>
                                    <td align="center" style="background-color:#003366; border-radius:6px;">
                                        <a href="mailto:{{ $data['email'] }}"
                                           style="display:inline-block; padding:14px 24px; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold;">
                                            Reply to Sender
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin-top:25px; font-size:14px; line-height:1.7; color:#666666;">
                                Please review this message and respond when appropriate.
                            </p>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center"
                            style="background-color:#003366; padding:22px 20px; border-top:4px solid #FFD700;">

                            <p style="margin:0; color:#FFD700; font-size:14px; font-weight:bold;">
                                PHM HCC
                            </p>

                            <p style="margin:8px 0; color:#ffffff; font-size:12px; line-height:1.7;">
                                Mbezi-Magari Saba, Dar es Salaam, Tanzania
                            </p>

                            <p style="margin:0; color:#ffffff; font-size:12px;">
                                Email: info@phmhcc.org
                            </p>

                            <p style="margin:15px 0 0; color:#cccccc; font-size:11px;">
                                This email was generated by the PHM HCC website contact form.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
