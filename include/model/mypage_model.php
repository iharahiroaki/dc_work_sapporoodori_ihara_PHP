<?php
// ログインユーザーの購入履歴を取得する関数
function getPurchaseHistory($dbh, $user_id) {
    try {
        $stmt = $dbh->prepare("SELECT * FROM purchase_history WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $purchase_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $purchase_history; // 戻り値を追加
    } catch (PDOException $e) {
        throw new Exception('購入履歴の取得に失敗しました。' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}

// データを取得する関数
function getAllData($dbh, $table) {
    try {
        // ①sql文の準備
        $sql = "SELECT * FROM " . $table;
        // ②sql文の実行
        $stmt = $dbh->query($sql);
        // ③sql文の結果取り出し
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo 'クエリの実行に失敗しました。' . $e->getMessage();
        exit();
    }
}