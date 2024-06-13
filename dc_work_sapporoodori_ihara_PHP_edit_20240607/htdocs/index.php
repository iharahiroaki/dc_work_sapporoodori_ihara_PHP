<?php
require_once '../include/utility/common_utilities.php';
require_once '../include/utility/dbConnect.php';
require_once '../include/model/index_model.php';

safeSessionStart(); // セッションを安全に開始
$dbh = dbConnect();
$error_message = '';

if (isset($_SESSION['username'])) {
    redirect('../ec_site/shopping.php'); // リダイレクト関数の使用
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $username = escapeHTML($username); // HTMLエスケープ関数の使用
    $password = escapeHTML($password); // HTMLエスケープ関数の使用

    if (empty($username) || empty($password)) {
        $error_message = "ユーザーIDおよびパスワードを入力してください。";
    } else {
        $user = getUser($dbh, $username);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            session_regenerate_id(true);
            saveSessionToDB($dbh, $username, session_id());
            redirect('../ec_site/shopping.php'); // リダイレクト関数の使用
        } else {
            $error_message = "ユーザーIDまたはパスワードが間違っています。";
        }
    }
}

require_once '../include/view/index_view.php';
