<?php
// セッションを開始
session_start();

// ブラウザにエラーを表示
ini_set('display_errors', "On");

// データベースに接続
require_once('../../include/model/dbConnect.php');

// もしログイン済みであれば、shopping.phpにリダイレクト
if (isset($_SESSION['username'])) {
    header("Location: ../ec_site/shopping.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars(trim($_POST['password']), ENT_QUOTES, 'UTF-8');

    // ユーザーIDとパスワードのバリデーション
    $usernameRegex = '/^[a-zA-Z0-9]{5,}$/'; // ユーザーIDの正規表現
    $passwordRegex = '/^[a-zA-Z0-9]{8,}$/'; // パスワードの正規表現

    if (!preg_match($usernameRegex, $username)) {
        // ユーザーIDが正しくない場合はエラーメッセージを表示してリダイレクト
        $_SESSION['error'] = 'ユーザーIDは半角英数字で5文字以上で入力してください。';
        header('Location: ./register.php');
        exit;
    }

    if (!preg_match($passwordRegex, $password)) {
        // パスワードが正しくない場合はエラーメッセージを表示してリダイレクト
        $_SESSION['error'] = 'パスワードは半角英数字で8文字以上で入力してください。';
        header('Location: ./register.php');
        exit;
    }

    try {
        $dbh = dbConnect();

        // トランザクションの開始
        $dbh->beginTransaction();

        // ユーザーIDの重複をチェック
        $stmt = $dbh->prepare("SELECT * FROM user WHERE user_name = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            // エラーメッセージを表示
            $_SESSION['error'] = 'このユーザーIDは既に使用されています。';
            // トランザクションのロールバックをして終了
            $dbh->rollBack();
            header('Location: ./register.php');
            exit;
        }

        // パスワードのハッシュ化
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $dbh->prepare("INSERT INTO user (user_name, password) VALUES (:username, :password)");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->execute();

        // トランザクションのコミット
        $dbh->commit();

        // セッションハイジャック対策
        session_regenerate_id(true);

        // 登録成功時は登録完了のメッセージを表示してリダイレクト
        $_SESSION['success'] = 'ユーザー登録が完了しました！　※自動でログイン画面に移動します。';
        header('Location: ./register.php');
        exit;
    } catch(PDOException $e) {
        // データベースエラーの場合はエラーメッセージを表示してリダイレクト
        $_SESSION['error'] = 'データベースエラーが発生しました。';
        header('Location: ./register.php');
        exit;
    }
}

try {
    require_once('../../include/view/register_view.php');
} catch (Exception $e) {
    echo 'viewファイルの読み込みに失敗しました。' . $e->getMessage();
}