<!DOCTYPE html>
<html>
<head>
    <title>Store Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; padding: 40px 20px; background-color: #f4f4f4;">
    <div style="background-color: #fff; padding: 30px; border-radius: 5px; max-width: 500px; margin: auto; text-align: center;">
        <h2 style="color: {{ $ok ? '#198754' : '#dc3545' }};">{{ $title }}</h2>
        <p>{{ $message }}</p>
    </div>
</body>
</html>
