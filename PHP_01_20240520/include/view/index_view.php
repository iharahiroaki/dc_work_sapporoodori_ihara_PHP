<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログインページ</title>
</head>
<body>
    <?php if (!empty($GLOBALS['errorMessage'])): ?>
        <p><?php echo $GLOBALS['errorMessage']; ?></p>
    <?php endif; ?>
    <form action="index.php" method="post">
        ユーザー名: <input type="text" name="login_id"><br>
        パスワード: <input type="password" name="password"><br>
        <input type="submit" value="ログイン">
    </form>
    <a href="signup.php">新規登録ページへ</a>
</body>
</html>
