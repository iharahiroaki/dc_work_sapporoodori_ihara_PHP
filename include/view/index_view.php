<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>毛鉤専門ショップ_ログインページ</title>

    <link rel="stylesheet" href="./stylesheet/index_styles.css">

</head>
<body>
    <header>
        <h1 class="site-title">ログイン</h1>
    </header>   
    <div class="login-container">
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo escapeHTML($error_message); ?></p> <!-- HTMLエスケープ関数の使用 -->
        <?php endif; ?>
        <form action="./index.php" method="post" class="login-form">
            <label for="username" class="login-label">ユーザーID:</label>
            <input type="text" name="username" id="username" required>
            <label for="password" class="login-label">パスワード:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">ログイン</button>
        </form>
        <br>
        <a href="./register.php" class="signup">新規登録はここをクリック！</a>
    </div>
</body>
</html>