<?php
// 購入履歴をデータベースに保存する関数
function savePurchaseHistory($dbh, $user_id, $purchase_items) {
    try {
        // トランザクションの開始
        $dbh->beginTransaction();
        // SQL文の準備
        $stmt = $dbh->prepare("INSERT INTO purchase_history (user_id, product_id, quantity, purchase_date) VALUES (:user_id, :product_id, :quantity, :purchase_date)");       
        // データのバリデーションと保存
        foreach ($purchase_items as $item) {
            if (!isset($item['product_id'], $item['quantity']) || !is_int($item['product_id']) || !is_int($item['quantity']) || $item['quantity'] <= 0) {
                throw new Exception('購入情報が不正です。');
            }
            // パラメータのバインド
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':product_id', $item['product_id'], PDO::PARAM_INT);
            $stmt->bindValue(':quantity', $item['quantity'], PDO::PARAM_INT);
            $stmt->bindValue(':purchase_date', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            // SQL文の実行
            if (!$stmt->execute()) {
                throw new Exception('SQL実行に失敗しました: ' . implode(", ", $stmt->errorInfo()));
            }
        }
        // トランザクションのコミット
        $dbh->commit();
   } catch (PDOException $e) {
    // トランザクションのロールバック
    $dbh->rollBack();
    throw $e;
    echo '購入履歴の保存に失敗しました。' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
   }
}