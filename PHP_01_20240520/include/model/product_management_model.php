<?php
require_once '../../include/utility/database_functions.php';

/**
 * 商品登録リクエストを処理します。
 * 
 * @param array &$errMessages エラーメッセージを格納する配列（参照渡し）
 * @param int &$flg_productRegistration 商品登録成功フラグ（参照渡し）
 */
function handleProductRegistrationRequest(&$errMessages, &$flg_productRegistration) {
    // リクエストメソッドを確認（この関数はPOSTリクエスト時にのみ呼び出されることを想定）
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 必要なPOSTデータを取得
        $productName = $_POST['productName'] ?? '';
        $price = $_POST['price'] ?? '';
        $productQty = $_POST['productQty'] ?? '';
        $fileName = $_FILES['fileName']['name'] ?? '';
        $publicFlg = $_POST['publicFlg'] ?? '';

        // 入力検証
        if (empty($productName) || empty($price) || empty($productQty) || empty($fileName)) {
            $errMessages[] = "入力されていないフィールドがあります。";
        } elseif (!checkInteger($price) || !checkInteger($productQty)) {
            $errMessages[] = "価格と個数は正の整数で入力してください。";
        } elseif (!extensionCheck($fileName)) {
            $errMessages[] = "適切な画像ファイルではありません。";
        } else {
            // ファイルアップロード処理
            $uploadResult = fileUpload($fileName, $_FILES['fileName']['tmp_name']);
            if ($uploadResult) {
                // データベースへの登録処理
                $insertResult = db_insert($productName, $price, $productQty, $fileName, $publicFlg);
                if ($insertResult) {
                    $flg_productRegistration = 1; // 商品登録成功
                } else {
                    $errMessages[] = "データベースへの登録に失敗しました。";
                }
            } else {
                $errMessages[] = "ファイルのアップロードに失敗しました。";
            }
        }
    }
}

/**
 * ファイルをアップロードし、成功したかどうかを返します。
 *
 * @param string $fileName ファイル名。
 * @param string $tmpName 一時ファイル名。
 * @return bool アップロードが成功したかどうか。
 */
function fileUpload($fileName, $tmpName) {
    $uploadPath = '../../htdocs/ec_site/assets/products/' . basename($fileName);
    return move_uploaded_file($tmpName, $uploadPath);
}

/**
 * 商品情報をデータベースに登録します。
 *
 * @param string $productName 商品名
 * @param int $price 価格
 * @param int $productQty 在庫数
 * @param string $fileName 画像ファイル名
 * @param int $publicFlg 公開フラグ
 * @return bool 登録が成功したかどうか
 */
function db_insert($productName, $price, $productQty, $fileName, $publicFlg) {
    $pdo = get_connection();
    
    try {
        // トランザクション開始
        $pdo->beginTransaction();

        // 商品情報を登録
        $sqlProduct = "INSERT INTO products (product_name, price, public_flg, create_date, update_date) VALUES (?, ?, ?, NOW(), NOW())";
        $stmtProduct = $pdo->prepare($sqlProduct);
        $stmtProduct->execute([$productName, $price, $publicFlg]);
        $productId = $pdo->lastInsertId(); // 登録した商品のIDを取得

        // 画像情報を登録
        $sqlImage = "INSERT INTO images (product_id, image_name, create_date, update_date) VALUES (?, ?, NOW(), NOW())";
        $stmtImage = $pdo->prepare($sqlImage);
        $stmtImage->execute([$productId, $fileName]);

        // 在庫情報を登録
        $sqlStock = "INSERT INTO stocks (product_id, stock_qty, create_date, update_date) VALUES (?, ?, NOW(), NOW())";
        $stmtStock = $pdo->prepare($sqlStock);
        $stmtStock->execute([$productId, $productQty]);

        // トランザクション確定
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // エラー発生時はロールバック
        $pdo->rollBack();
        echo "エラー発生: " . $e->getMessage();
        error_log("商品登録失敗: " . $e->getMessage());
        return false;
    }
}


function db_select() {
    $pdo = get_connection(); // データベース接続取得
    try {
        // 商品情報、画像情報、在庫情報を結合して取得するSQLクエリ
        // 画像テーブルにpublic_flgがないため、この列を取得する部分を除去
        $sql = "SELECT p.product_id, p.product_name, p.price, p.public_flg, 
                       i.image_name, 
                       s.stock_qty 
                FROM products p
                LEFT JOIN images i ON p.product_id = i.product_id
                LEFT JOIN stocks s ON p.product_id = s.product_id";

        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    } catch (PDOException $e) {
        error_log("商品情報取得失敗: " . $e->getMessage());
        return [];
    }
}


/**
 * 指定された商品IDの在庫数を新しい値に更新します。
 *
 * @param int $productId 商品ID。
 * @param int $newQuantity 新しい在庫数。
 * @return bool 更新が成功したかどうか。
 */
function changeStockQuantity($productId, $newQuantity) {
    // データベース接続を取得
    $pdo = get_connection();
    
    try {
        // 在庫数を更新するSQL文を準備
        $sql = "UPDATE stocks SET stock_qty = ? WHERE product_id = ?";
        $stmt = $pdo->prepare($sql);
        
        // SQL文を実行し、結果を返す
        return $stmt->execute([$newQuantity, $productId]);
    } catch (PDOException $e) {
        // エラーが発生した場合はエラーメッセージをログに記録
        error_log("在庫数更新失敗: " . $e->getMessage());
        return false;
    }
}


/**
 * 指定された商品の公開フラグを切り替えます。
 *
 * @param int $productId 商品ID。
 * @param int $newStatus 新しい公開フラグの状態（公開: 1、非公開: 0）。
 * @return bool 更新が成功したかどうか。
 */
function togglePublicFlag($productId, $newStatus) {
    $pdo = get_connection();
    
    try {
        $sql = "UPDATE products SET public_flg = ? WHERE product_id = ?";
        $stmt = $pdo->prepare($sql);
        
        return $stmt->execute([$newStatus, $productId]);
    } catch (PDOException $e) {
        error_log("公開フラグ更新失敗: " . $e->getMessage());
        return false;
    }
}

/**
 * 指定された商品IDの商品を削除します。
 * 関連する画像や在庫情報も適切に削除します。
 *
 * @param int $productId 商品ID。
 * @return bool 削除が成功したかどうか。
 */
function deleteProduct($productId) {
    $pdo = get_connection();
    
    try {
        // トランザクション開始
        $pdo->beginTransaction();

        // 在庫情報を削除
        $sqlStock = "DELETE FROM stocks WHERE product_id = ?";
        $stmtStock = $pdo->prepare($sqlStock);
        $stmtStock->execute([$productId]);

        // 画像情報を削除
        $sqlImage = "DELETE FROM images WHERE product_id = ?";
        $stmtImage = $pdo->prepare($sqlImage);
        $stmtImage->execute([$productId]);

        // 商品情報を削除
        $sqlProduct = "DELETE FROM products WHERE product_id = ?";
        $stmtProduct = $pdo->prepare($sqlProduct);
        $stmtProduct->execute([$productId]);

        // トランザクション確定
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // エラーが発生した場合はロールバック
        $pdo->rollBack();
        error_log("商品削除失敗: " . $e->getMessage());
        return false;
    }
}


?>
