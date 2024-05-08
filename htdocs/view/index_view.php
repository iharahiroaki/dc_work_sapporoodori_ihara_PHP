<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>毛鉤専門ショップ_ログインページ</title>

    <link rel="stylesheet" href="../view/stylesheet/index_styles.css">

</head>
<body>
    <header>
        <h1 class="site-title">フライフィッシングの毛鉤専門ショップ</h1>
    </header>
    
    <div class="login-container">
        <h2 class="login-title">ログイン</h2>
        <?php if (isset($error_message)): ?>
            <script>
                alert("<?php echo htmlspecialchars($error_message, ENT_QUOTES); ?>");
            </script>
        <?php endif; ?>
        <form action="../ecsite/index.php" method="post" class="login-form">
            <div class="form-group">
                <label for="username" class="login-label">ユーザーID:</label>
                <input type="text" name="username" id="username" autocomplete="off" required><br><br> <!-- ※required属性でnull禁止 -->
            </div>
            <div class="form-group">
                <label for="password" class="login-label">パスワード:</label>
                <input type="password" name="password" id="password" autocomplete="off" required><br><br> <!-- ※required属性でnull禁止 -->
            </div>
            <input type="submit" value="ログイン">
        </form>
        <br>
        <a href="../ecsite/register.php" class="signup">新規登録はここをクリック！</a>
    </div>
</body>
</html>