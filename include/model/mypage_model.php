<?php
// ログインユーザーの購入履歴を取得する関数
function getPurchaseHistory($dbh, $user_id) {
    try {
        $stmt = $dbh->prepare("SELECT * FROM purchase_history WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $purchase_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $purchase_history;
    } catch (PDOException $e) {
        throw new Exception('購入履歴の取得に失敗しました。' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}

// データを取得する関数
function getAllData($dbh, $table) {
    try {
        $stmt = $dbh->prepare("SELECT * FROM " . $table);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception('データの取得に失敗しました。' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}

// 商品情報を取得する関数
function getProductInfo($dbh, $product_id) {
    $stmt = $dbh->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->execute([$product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}