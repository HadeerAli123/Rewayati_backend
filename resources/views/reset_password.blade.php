<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f0ea; /* لون كريمي فاتح */
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff; /* لون أبيض */
            border: 1px solid #ccbbaa; /* لون بني فاتح */
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
            margin-bottom: 8px;
            float:left;
        }
        .header h2 {
    color: #5a4234; /* لون بني داكن */
    font-size: 24px; /* حجم النص */
    text-align: center; /* محاذاة النص في المنتصف */
    margin: 0 auto; /* جعل العنصر مركزي */
    display: inline-block; /* يجعله محاطاً بمساحة فقط حول النص */
    width: 100%; /* لضمان أن العنصر يأخذ العرض بالكامل */
}
        p {
            color: #5d4037; /* لون بني مناسب */
            line-height: 1.6;
            font-size: 16px;
            text-align: center; /* محاذاة النص في المنتصف */
            margin: 5 0 20px; /* ضبط المسافة بين الفقرات */
        }
        .button-container {
            text-align: center; /* الزرار في النص */
            margin-top: 20px;
        }
        .button {
            background-color: #8c6f5b; /* لون بني متوسط */
            color: #ffffff !important; /* لون النص أبيض */
            padding: 12px 24px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            display: inline-block;
            text-align: center; /* يضمن النص في منتصف الزر */
        }
        .button:hover {
            background-color: #755a44; /* لون بني أغمق عند التمرير */
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #8c7c6e; /* لون بني فاتح */
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Your Logo">
            <h2>Reset Your Password</h2>
            <p>Click the button below to reset your password:</p>
        </div>
        <div class="button-container">
            <a href="{{ $url }}" class="button">Reset Password</a> <!-- تأكد من النص أبيض -->
        </div>
        <p>If you did not request a password reset, no further action is required.</p>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Application. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
