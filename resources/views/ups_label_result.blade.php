<!DOCTYPE html>
<html>
<head>
    <title>UPS 라벨 생성 결과</title>
</head>
<body>
<h1>UPS 라벨 생성 완료</h1>

<p><strong>트래킹 번호:</strong> {{ $trackingNumber }}</p>

<p><a href="{{ $downloadUrl }}" target="_blank">라벨 다운로드</a></p>

<h2>트래킹 데이터</h2>
<pre>{{ json_encode($trackingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

</body>
</html>
