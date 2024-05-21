<?php
require_once '../../include/utility/database_functions.php';

// ユーザー認証を行う関数
function authenticate($loginId, $password) {
    // データベース接続を取得
    $pdo = get_connection();
    // ユーザー名に基づいてユーザー情報を取得する準備
    $stmt = $pdo->prepare("SELECT user_name, password FROM user WHERE user_name = ?");
    // SQLを実行
    $stmt->execute([$loginId]);
    // 結果を連想配列として取得
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ユーザーが存在し、パスワードが一致する場合にtrueを返す
    if ($user && password_verify($password, $user['password'])) {
        return true;
    }
    return false;
}
?>
