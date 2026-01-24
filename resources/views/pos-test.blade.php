<!DOCTYPE html>
<html>
<head>
    <title>POS Test</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Scanner Test</h1>
    <input type="text" id="barcode" autofocus placeholder="Scan karo yahan">
    <div id="result"></div>

    <script>
        document.getElementById('barcode').addEventListener('input', function(e) {
            let code = e.target.value.trim();
            if (code) {
                document.getElementById('result').innerHTML += '<p>Scanned: ' + code + '</p>';
                e.target.value = ''; // Clear for next
            }
        });
    </script>
</body>
</html>