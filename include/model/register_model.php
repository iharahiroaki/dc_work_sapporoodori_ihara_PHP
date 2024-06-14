<?php
// ユーザーIDの重複をチェックする関数
function isUsernameExists($dbh, $username) {
    $stmt = $dbh->prepare("SELECT * FROM user WHERE user_name = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    return $existingUser !== false;
}

// 新しいユーザーをデータベースに挿入する関数
function insertNewUser($dbh, $username, $password) {
    // パスワードのハッシュ化
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // ユーザー情報の挿入
    $stmt = $dbh->prepare("INSERT INTO user (user_name, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $stmt->execute();
}