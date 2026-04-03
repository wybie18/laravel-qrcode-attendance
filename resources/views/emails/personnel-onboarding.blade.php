<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #111827;">
    <p>Dear {{ $personnel->first_name }},</p>

    <p>
        Thank you for registering with the <strong>SDO Ormoc Automated Check-In/Out System</strong>.
        Your registration has been successfully recorded for check-in and check-out during visits
        to the SDO Ormoc office.
    </p>

    <p>
        Please download or print your QR code from the attached file and present it at the gate
        entrance when checking in or checking out.
    </p>

    <p>
        If you have any questions or need assistance, please contact us at
        <a href="mailto:{{ config('mail.personnel_onboarding_cc.address') }}">{{ config('mail.personnel_onboarding_cc.address') }}</a>.
    </p>

    <p>We look forward to your visit.</p>

    <p>
        Best regards,<br>
        {{ config('mail.personnel_onboarding_cc.name') }}
    </p>
</body>
</html>
