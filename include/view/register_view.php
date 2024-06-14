<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./stylesheet/register_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>毛鉤専門ショップ_ユーザー登録ページ</title>
    
</head>

<body>
    <!-- navバーの呼び出し -->
    <?php require_once '../include/view/templates/header.php'; ?>

    <?php require_once '../include/utility/dbConnect.php'; ?>
    <div class="register-container">
    <h2>新規登録</h2>
        <form class="register-form" action="./register.php" method="post" onsubmit="return validateForm()">
            <label for="username">ユーザーID:</label>
            <input type="text" name="username" id="username" required value="<?php echo escapeHTML($_POST['username'] ?? ''); ?>"><br><br>
            <label for="password">パスワード:</label>
            <input type="password" name="password" id="password" required value="<?php echo escapeHTML($_POST['password'] ?? ''); ?>"><br><br>
            <input type="submit" value="登録">
        </form><br>
        <p>既にアカウントをお持ちの場合は<a href="./index.php">ログインページへ</a></p>
    </div>

    <?php
    // 登録成功時のメッセージを表示
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success" role="alert">' . escapeHTML($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
        // 3秒後にindex.phpにリダイレクト
        echo '<meta http-equiv="refresh" content="3; URL=./index.php">';
    }

    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger" role="alert">' . escapeHTML($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    ?>

    <!-- Bootstrap JavaScript（オプション）の呼び出し -->
    <?php include '../include/view/templates/footer.php'; ?>

</body>
</html>