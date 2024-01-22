<html>

<head>
    <title>Email Using a Custom Template</title>
</head>

<body>
    <h3>サンクスメール</h3>
    <p>
        仮登録ありがとうございます。
        以下のURLから本登録を実施して下さい。
        <br />
            http://localhost:8788/signup/user?token=<?= $token ?>&step=1
    </p>
</body>

</html>
