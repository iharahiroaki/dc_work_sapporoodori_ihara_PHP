<?php
// ブラウザにエラーを表示
ini_set('display_errors', "On");

require_once '../include/utility/common_utilites.php';
require_once '../include/utility/dbConnect.php';
require_once '../include/model/index_model.php';

// セッションを開始
safeSessionStart(); // セッションを安全に開始
$dbh = dbConnect();
$error_message = ''; // エラーメッセージを格納する変数を初期化

// もしログイン済みであれば、shopping.phpにリダイレクト
if (isset($_SESSION['username'])) {
    redirect('./shopping.php'); // リダイレクト関数の使用
}

// フォームが送信されたかどうかを確認する
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ユーザーIDとパスワードが指定の値であるかチェックする
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    // XSS対策
    $username = escapeHTML($username); // HTMLエスケープ関数の使用
    $password = escapeHTML($password); // HTMLエスケープ関数の使用

    // 入力の検証
    if (empty($username) || empty($password)) {
      $error_message = "ユーザーIDおよびパスワードを入力してください。";
    } else {
        if ($username === 'ec_admin' && $password === 'ec_admin') {
            $_SESSION['admin'] = true;
            $_SESSION['username'] = $username;
            session_regenerate_id(true);
            redirect('./product.php');
        } else {
            $user = getUser($dbh, $username);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $username;
                $_SESSION['admin'] = false;
                session_regenerate_id(true);
                saveSessionToDB($dbh, $username, session_id());
                redirect('./shopping.php');
            } else {
                $error_message = "ユーザーIDまたはパスワードが間違っています。";
            }
        }
    }
}

require_once('../include/view/index_view.php');