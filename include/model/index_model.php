<?php
// データベースの情報の出し入れ

function getUser($dbh, $username) {
    $stmt = $dbh->prepare("SELECT * FROM user WHERE user_name = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function saveSessionToDB($dbh, $username, $session_id) {
    $stmt = $dbh->prepare("UPDATE user SET session_id = :session_id WHERE user_name = :username");
    $stmt->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
}