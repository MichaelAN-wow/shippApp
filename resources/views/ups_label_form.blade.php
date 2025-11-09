<!DOCTYPE html>
<html>
<head>
    <title>UPS 라벨 생성</title>
</head>
<body>
<h1>UPS 라벨 생성 테스트</h1>

@if(session('error'))
    <p style="color:red;">{{ session('error') }}</p>
@endif

<form action="/ups-label" method="POST">
    @csrf
    <label>받는 사람 이름:</label><br>
    <input type="text" name="name" value="John Doe"><br><br>

    <label>주소:</label><br>
    <input type="text" name="address" value="456 Receiver Rd"><br><br>

    <label>도시:</label><br>
    <input type="text" name="city" value="Los Angeles"><br><br>

    <label>주(State):</label><br>
    <input type="text" name="state" value="CA"><br><br>

    <label>우편번호:</label><br>
    <input type="text" name="zip" value="90001"><br><br>

    <label>무게(lb):</label><br>
    <input type="text" name="weight" value="1"><br><br>

    <button type="submit">라벨 생성</button>
</form>
</body>
</html>
