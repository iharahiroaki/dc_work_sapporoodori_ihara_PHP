<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログインページ</title>
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
    <header>
        <h1>ログイン</h1>
    </header>
    <div>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo escapeHTML($error_message); ?></p> <!-- HTMLエスケープ関数の使用 -->
        <?php endif; ?>
        <form action="./index.php" method="post">
            <label for="username">ユーザーID:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">ログイン</button>
        </form>
    </div>
    <footer>
    </footer>
</body>
</html>
