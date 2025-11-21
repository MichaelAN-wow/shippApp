<!DOCTYPE html>
<html>
<head>
    <title>Loading...</title>
</head>
<body>
    <h2>Preparing file...</h2>

    <script>
        setTimeout(function(){
            window.location.href = "{{ asset('storage/' . $file) }}";
        }, 300);
    </script>
</body>
</html>
