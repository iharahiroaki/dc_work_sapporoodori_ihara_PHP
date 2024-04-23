<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./stylesheet/register_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="path/to/bootstrap.min.css">

    <title>毛鉤専門ショップ_ユーザー登録ページ</title>

    <script>
        function validateForm() {
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            var usernameRegex = /^[a-zA-Z0-9]{5,}$/; // ユーザーIDの正規表現
            var passwordRegex = /^[a-zA-Z0-9]{8,}$/; // パスワードの正規表現

            if (!usernameRegex.test(username)) {
                alert("ユーザーIDは半角英数字で5文字以上で入力してください。");
                return false;
            }

            if (!passwordRegex.test(password)) {
                alert("パスワードは半角英数字で8文字以上で入力してください。");
                return false;
            }

            return true;
        }
    </script>

</head>

<body>
    <!-- Bootstrap Navbar -->
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
        <form class="register-form" action="./register_process.php" method="post" onsubmit="return validateForm()">
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
    if (isset($_GET['success']) && $_GET['success'] == 'true') {
        echo "<p class=\"message\">ユーザー登録が完了しました！<br>3秒後にログインページに自動で移ります。</p>";
        // リダイレクト
        header('Refresh: 3; URL=./index.php'); // 3秒後にindex.phpにリダイレクト
        exit;
    }
    ?>

    <!-- Bootstrap JavaScript（オプション） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>