<?php
require_once '../../include/utility/database_functions.php';

// 指定されたユーザー名がデータベースに存在するかをチェックします。
function checkUserExists($loginId) {
    $pdo = get_connection();
    $stmt = $pdo->prepare("SELECT user_name FROM user WHERE user_name = ?");
    $stmt->execute([$loginId]);
    return $stmt->fetchColumn() !== false;
}

// 新しいユーザーをデータベースに登録します。
function insertUser($loginId, $password) {
    $pdo = get_connection();
    // パスワードを安全に保管するために、ハッシュ化します。
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO user (user_name, password) VALUES (?, ?)");
    $stmt->execute([$loginId, $passwordHash]);
    // 登録したユーザーのIDを返します。
    return $pdo->lastInsertId();
}

// 入力されたユーザー名とパスワードのバリデーションを行います。
function validateInput($loginId, $password) {
    // 必須入力チェック
    if (empty($loginId) || empty($password)) {
        return "All fields are required.";
    }
    // ユーザー名のフォーマットチェック
    if (!preg_match('/^[a-zA-Z0-9]{5,}$/', $loginId)) {
        return "Username must be at least 5 characters long and contain only letters and numbers.";
    }
    // パスワードのフォーマットチェック
    if (!preg_match('/^[a-zA-Z0-9]{8,}$/', $password)) {
        return "Password must be at least 8 characters long and contain only letters and numbers.";
    }
    // ユーザー名の重複チェック
    if (checkUserExists($loginId)) {
        return "Username already exists.";
    }
    // すべてのチェックを通過した場合は空文字を返します。
    return "";
}
?>
