<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $mailSubject }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #019934;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 30px 20px;
            font-size: 16px;
        }
        .footer {
            background-color: #f1f5f9;
            color: #64748b;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
        }
        .footer a {
            color: #019934;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0 !important;
                border-radius: 0 !important;
                border: none !important;
            }
            .content {
                padding: 20px 15px !important;
            }
            .header {
                padding: 20px 15px !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Southwest Farmers Store</h1>
        </div>
        <div class="content">
            {!! $body !!}
        </div>
        <div class="footer">
            <p>You received this email because you subscribed to the Southwest Farmers Store newsletter.</p>
            <p>&copy; {{ date('Y') }} Southwest Farmers. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
