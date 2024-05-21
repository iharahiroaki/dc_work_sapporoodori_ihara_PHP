<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
</head>
<body>
    <h3>ユーザー登録</h3>
    <div>
        <div>
            <p style="color:red;" id="areaErr_js">
                <?php echo $errorMessage; ?>
            </p>
        </div>
        <form action="signup.php" method="post" name="signupForm" >
            ユーザー名: <input type="text" name="login_id" style="margin-bottom:5px;"><br>
            パスワード: <input type="password" name="password" style="margin-bottom:15px;"><br>
            <input type="submit" value="登録" >
        </form>
        <div>
            <a href="index.php">ログインページへ</a>
        </div>
    </div>
</body>
</html>
