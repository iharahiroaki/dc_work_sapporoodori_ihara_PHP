<?php
// ブラウザにエラーを表示
ini_set('display_errors', "On");

require_once '../include/utility/common_utilites.php';
require_once '../include/utility/dbConnect.php';
require_once '../include/model/register_model.php';

// セッションを開始
safeSessionStart(); // セッションを安全に開始
$dbh = dbConnect();
$error_message = ''; // エラーメッセージを格納する変数を初期化

// もしログイン済みであれば、shopping.phpにリダイレクト
if (isset($_SESSION['username'])) {
    redirect('./shopping.php'); // リダイレクト関数の使用
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // POSTデータからユーザーIDとパスワードを取得
    $username = $_POST['username'];
    $password = $_POST['password'];
    // XSS対策
    $username = escapeHTML($username); // HTMLエスケープ関数の使用
    $password = escapeHTML($password); // HTMLエスケープ関数の使用
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
        // トランザクションの開始
        $dbh->beginTransaction();

        // ユーザーIDの重複をチェックする関数の呼び出し
        if (isUsernameExists($dbh, $username)) {
            // エラーメッセージを表示
            $_SESSION['error'] = 'このユーザーIDは既に使用されています。';
            // トランザクションのロールバックをして終了
            $dbh->rollBack();
            header('Location: ./register.php');
            exit;
        }

        // 新しいユーザーをデータベースに挿入する関数の呼び出し
        insertNewUser($dbh, $username, $password);

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

require_once('../include/view/register_view.php');