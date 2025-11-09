<!DOCTYPE html>
<html>
<head>
    <title>UPS Label Generator</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>UPS Label Generator</h1>
    <form id="labelForm">
        <input type="text" name="name" placeholder="Recipient Name" required><br>
        <input type="text" name="address" placeholder="Street Address" required><br>
        <input type="text" name="city" placeholder="City" required><br>
        <input type="text" name="state" placeholder="State (e.g. CA)" required><br>
        <input type="text" name="zip" placeholder="ZIP Code" required><br>
        <input type="text" name="weight" placeholder="Weight (lbs)" required><br>
        <button type="submit">Generate Label</button>
    </form>
    <div id="result" style="margin-top:20px;"></div>

    <script>
        $('#labelForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/ups/generate-label',
                type: 'POST',
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(data) {
                    $('#result').html('<a href="' + data.downloadUrl + '" target="_blank">Download UPS Label</a>');
                },
                error: function() {
                    $('#result').html('<p style="color:red;">Label generation failed.</p>');
                }
            });
        });
    </script>
</body>
</html>
