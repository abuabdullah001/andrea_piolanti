<!DOCTYPE html>
<html>
<head>
    <title>Booking Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
        }

        .body {
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }

        .body p {
            font-size: 16px;
        }

        .footer {
            background-color: #f8f9fa;
            color: #777;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .reminder-date {
            font-weight: bold;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>Booking Reminder</h2>
        </div>
        <div class="body">
            <p>Hi {{ $booking->customer->name }},</p>
            <p>This is a friendly reminder that you have a booking scheduled on <span class="reminder-date">{{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</span>.</p>

            <p>Thank you for choosing us!</p>
        </div>
        <div class="footer">
            <p>If you have any questions, feel free to <a href="mailto:support@example.com">contact us</a>.</p>
        </div>
    </div>
</body>
</html>
