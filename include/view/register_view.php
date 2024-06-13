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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">フライフィッシングの毛鉤専門ショップ</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto d-flex align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="./index.php">ログインページ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="register-container">
    <h2>新規登録</h2>
        <form class="register-form" action="./register.php" method="post" onsubmit="return validateForm()">
            <label for="username">ユーザーID:</label>
            <input type="text" name="username" id="username" required><?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES); ?><br><br>
            <label for="password">パスワード:</label>
            <input type="password" name="password" id="password" required><?php echo htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES); ?><br><br>
            <input type="submit" value="登録">
        </form><br>
        <p>既にアカウントをお持ちの場合は<a href="./index.php">ログインページへ</a></p>
    </div>

    <?php
    // 登録成功時のメッセージを表示
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['success'], ENT_QUOTES) . '</div>';
        unset($_SESSION['success']);
        // 3秒後にindex.phpにリダイレクト
        echo '<meta http-equiv="refresh" content="3; URL=./index.php">';
    }

    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['error'], ENT_QUOTES) . '</div>';
        unset($_SESSION['error']);
    }
    ?>

    <!-- Bootstrap JavaScript（オプション） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>