<?php
require_once('./dbConnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ユーザーIDとパスワードのバリデーション
    $usernameRegex = '/^[a-zA-Z0-9]{5,}$/'; // ユーザーIDの正規表現
    $passwordRegex = '/^[a-zA-Z0-9]{8,}$/'; // パスワードの正規表現

    if (!preg_match($usernameRegex, $username)) {
        // ユーザーIDが正しくない場合はエラーメッセージを表示してリダイレクト
        header('Location: ./register.php?error=invalid_username');
        exit;
    }

    if (!preg_match($passwordRegex, $password)) {
        // パスワードが正しくない場合はエラーメッセージを表示してリダイレクト
        header('Location: ./register.php?error=invalid_password');
        exit;
    }

    try {
        $dbh = dbConnect();

        // ユーザーIDの重複をチェック
        $stmt = $dbh->prepare("SELECT * FROM user WHERE user_name = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
             // JavaScriptを使用してエラーメッセージを表示
             echo "<script>alert('このユーザーIDは既に使用されています。');</script>";
             // ページに留まる
             echo "<script>window.location = './register.php';</script>";
             exit;
        }

        // パスワードのハッシュ化
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $dbh->prepare("INSERT INTO user (user_name, password) VALUES (:username, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        // 登録成功時は登録完了のメッセージを表示してリダイレクト
        header('Location: ./register.php?success=true');
        exit;
    } catch(PDOException $e) {
        // データベースエラーの場合はエラーメッセージを表示してリダイレクト
        header('Location: ./register.php?error=database_error');
        exit;
    }
}

include_once('./register.php');