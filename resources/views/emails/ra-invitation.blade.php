<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.7; }
        .container { max-width: 600px; margin: 30px auto; padding: 30px; border: 1px solid #e0e0e0; border-radius: 8px; }
        a { color: #0d6efd; }
        .footer { margin-top: 30px; font-size: 13px; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <p>Dear Sir/Madam,</p>

        <p>Please find below the login link for the Resolution Applicant (RA) Portal for the Challenge Mechanism process:</p>

        <p>
            <strong>Resolution Applicant (RA) Portal Login:</strong><br>
            <a href="https://npv.indiaeauction.com/ra/login">https://npv.indiaeauction.com/ra/login</a>
        </p>

        <p>
            <strong>Date of Challenge Process:</strong>
            {{ \Carbon\Carbon::parse($auction->meeting_date)->format('d M Y') }}
        </p>

        <p>Kindly use your registered email ID to receive the OTP and access the portal.</p>

        <div class="footer">
            <p>Regards,<br>
            <strong>{{ $auction->corporate_debtor_name }}</strong></p>
        </div>
    </div>
</body>
</html>
