<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blocked Feedback</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f0ea;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff; 
            border: 1px solid #ccbbaa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 120px;
            margin-bottom: 10px;
        }
        .header h2 {
            color: #5a4234; 
            font-size: 24px;
        }
        p {
            color: #5d4037;
            line-height: 1.6;
            font-size: 16px;
            text-align: center; 
            margin: 5 0 20px; 
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .button {
            background-color: #8c6f5b;
            color: #ffffff !important;
            padding: 12px 24px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            display: inline-block;
            text-align: center;
        }
        .button:hover {
            background-color: #755a44; 
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #8c7c6e; 
        }
        .blockquote{
            text-align: center;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-body">
    <img src="{{ asset('logo.png') }}" alt="Your Logo">
        <p>Dear User,</p>
        <p>Your feedback has been blocked due to a violation of our guidelines.</p>
        <blockquote class="blockquote">
            "{{ $feedback }}"
        </blockquote>
        <p>If you believe this is a mistake, please contact us for further review.</p>
        <div class="button-container">
            <a href="{{ url('https://chocolate-eland-808338.hostingersite.com/home') }}" class="button">Visit Website</a>
        </div>
    </div>
    <div class="footer">
        Thank you for your understanding, <br>
    </div>
</div>

</body>
</html>
