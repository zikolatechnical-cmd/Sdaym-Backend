<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>بيانات الدخول إلى لوحة التحكم</title>
</head>

<body
    style="margin:0;padding:0;background-color:#f3f7fa;font-family:Tahoma,Arial,sans-serif;color:#18354a;direction:rtl;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="background-color:#f3f7fa;">
        <tr>
            <td align="center" style="padding:32px 12px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #dce9f1;">
                    <tr>
                        <td align="center" style="padding:28px 24px 22px;border-top:6px solid #087dc2;">
                            <img src="{{ $message->embed(public_path('images/sdaym-smart-logo.png')) }}" width="120"
                                alt="Sdaym Smart"
                                style="display:block;width:120px;max-width:100%;height:auto;border:0;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px 36px 34px;text-align:right;">
                            <h1 style="margin:0 0 12px;color:#087dc2;font-size:25px;line-height:1.5;text-align:center;">
                                مرحبًا بك في Sdaym Smart</h1>
                            <p style="margin:0 0 24px;color:#587080;font-size:16px;line-height:1.9;text-align:center;">
                                تم إنشاء حساب لوحة التحكم الخاص بمتجرك بنجاح.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="background:#f7fafc;border:1px solid #dce9f1;border-radius:12px;">
                                <tr>
                                    <td style="padding:18px 20px 8px;color:#718594;font-size:13px;">البريد الإلكتروني
                                    </td>
                                </tr>
                                <tr>
                                    <td dir="ltr"
                                        style="padding:0 20px 18px;color:#18354a;font-size:16px;font-weight:bold;text-align:right;word-break:break-all;">
                                        {{ $email }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:0 20px 8px;color:#718594;font-size:13px;border-top:1px solid #e4edf2;padding-top:18px;">
                                        كلمة المرور المؤقتة</td>
                                </tr>
                                <tr>
                                    <td dir="ltr"
                                        style="padding:0 20px 18px;color:#087dc2;font-family:Consolas,monospace;font-size:18px;font-weight:bold;letter-spacing:1px;text-align:right;word-break:break-all;">
                                        {{ $temporaryPassword }}</td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="margin-top:22px;background:#fffaf0;border-right:4px solid #d6b36a;border-radius:8px;">
                                <tr>
                                    <td style="padding:14px 16px;color:#735d2d;font-size:14px;line-height:1.8;">
                                        حفاظًا على أمان حسابك، يرجى تسجيل الدخول وتغيير كلمة المرور المؤقتة فورًا.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="padding:18px 24px;background:#087dc2;color:#ffffff;font-size:12px;line-height:1.7;">
                            فريق Sdaym Smart<br>
                            هذه رسالة آلية، يرجى عدم مشاركة بيانات الدخول مع أي شخص.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
