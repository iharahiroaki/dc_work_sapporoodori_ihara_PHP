<?php
$title = '毛鉤専門ショップ_ログインページ';
$stylesheet = './stylesheet/index_styles.css';
require_once '../include/view/templates/header.php';
?>
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
<!-- Bootstrap JavaScript（オプション）の呼び出し -->
<?php include '../include/view/templates/footer.php'; ?>