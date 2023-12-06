<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>

    <div style="background-color: #870e5c; border-radius: 10px; padding: 40px; width: 500px; margin: 0 auto;">
        <p style="color: white; text-align: left; margin-bottom: 5px">
            Dear {{ $reset->user->full_name }}
        </p>
        <p style="color: white; text-align: left; margin-bottom: 5px">
            Your code is " {{ $reset->otp }} "
        </p>
        <p style="color: white; text-align: left; margin-bottom: 5px">
            Keep the OTP code confidential and enjoy enhanced account security<br>
            Best regards
        </p>
        <p style="color: white; text-align: left;">
            TripBuddy Team
        </p>

    </div>

</body>

</html>
