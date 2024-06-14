<?php
// セッションを開始
session_start();

// ブラウザにエラーを表示
ini_set('display_errors', "On");

// データベースに接続
require_once('../../include/model/dbConnect.php');
$dbh = dbConnect();

// function.phpの読み込み
require_once('../../include/model/function.php');

// もし未ログインであれば、index.phpにリダイレクト
checkLogin();

// データを取得
$table = 'product';
$allProducts = getAllData($dbh, $table);
// var_dump($allProducts);

// ログアウト処理
if (isset($_POST['logout'])) {
    logout();
}

// 商品の登録
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // 一時的な成功フラグを初期化
    $success = false;
    
    // フォームから送信されたデータが存在するかを確認
    if(isset($_POST['product_name'], $_POST['price'], $_POST['quantity'], $_FILES['product_image'], $_POST['public_flag'])) {
        // フォームから送信されたデータを取得
        $productName = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $publicFlag = filter_input(INPUT_POST, 'public_flag', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        // 入力データのバリデーション
        if (!$productName || !$price || $price <= 0 || !$quantity || $quantity < 0 || $publicFlag === null) {
            $_SESSION['error_message'] = "価格、個数は1以上の整数を入力してください。";
        } else {
            // ファイルアップロードの検証
            $allowedExtensions = array('jpg', 'jpeg', 'png');
            $productImage = $_FILES['product_image']['name'];
            $fileExtension = strtolower(pathinfo($productImage, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                $_SESSION['error_message'] = "画像ファイルの拡張子は.jpg、.jpeg、.pngのみ許可されています。";
            } else {
                // 商品画像のアップロード処理
                $imagePath = 'product_images/' . $productImage;
                if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $imagePath)) {
                    $_SESSION['error_message'] = "画像のアップロードに失敗しました。";
                } else {
                    // 商品情報をデータベースに挿入
                    try {
                        // トランザクションの開始
                        $dbh->beginTransaction();

                        $stmt = $dbh->prepare("INSERT INTO product (product_name, price, quantity, product_image, public_flag) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$productName, $price, $quantity, $imagePath, $publicFlag]);

                        // トランザクションのコミット
                        $dbh->commit();

                        // 商品登録成功メッセージをセッションに保存
                        $_SESSION['success_message'] = "商品が正常に登録されました。";
                    } catch (PDOException $e) {
                        // トランザクションのロールバック
                        $dbh->rollBack();
                        $_SESSION['error_message'] = "商品登録に失敗しました。" . $e->getMessage();
                    }
                }
            }
        }

        // 商品一覧ページにリダイレクト
        header('Location: ./product.php');
        exit;
    } else {
        // フォームからのデータが提供されていない場合はエラーメッセージを出力
        $_SESSION['error_message'] = "フォームからのデータが提供されていません。";
    }
}

// 在庫数を変更する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    // フォームから送信されたデータを取得
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if (!$productId || !$quantity || $quantity < 0) {
        $_SESSION['error_message'] = "無効な入力データがあります。";
        header('Location: ./product.php');
        exit;
    }

    try {
        // トランザクションの開始
        $dbh->beginTransaction();

        // 在庫数をデータベースに更新
        $stmt = $dbh->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
        $stmt->execute([$quantity, $productId]);

        // トランザクションのコミット
        $dbh->commit();
    
        // 在庫数変更成功メッセージをセッションに保存
        $_SESSION['quantity_update'] = "在庫数が正常に変更されました。";
    } catch (PDOException $e) {
        // トランザクションのロールバック
        $dbh->rollBack();
        echo "在庫数の更新に失敗しました。" . $e->getMessage();
    }

    // 商品一覧ページにリダイレクト
    header('Location: ./product.php');
    exit;
}

// 公開フラグの更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['public_flag'])) {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $publicFlag = filter_input(INPUT_POST, 'public_flag', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

    if (!$productId || $publicFlag === null) {
        $_SESSION['error_message'] = "無効な入力データがあります。";
        header('Location: ./product.php');
        exit;
    }

    try {
        // トランザクションの開始
        $dbh->beginTransaction();

        // 公開フラグをデータベースに更新
        $stmt = $dbh->prepare("UPDATE product SET public_flag = ? WHERE product_id = ?");
        $stmt->execute([$publicFlag, $productId]);

        // トランザクションのコミット
        $dbh->commit();
        
        // 公開フラグ更新成功メッセージをセッションに保存
        $_SESSION['public_flag_update'] = "公開フラグが正常に更新されました。";
    } catch (PDOException $e) {
        // トランザクションのロールバック
        $dbh->rollBack();
        $_SESSION['error_message'] = "公開フラグの更新に失敗しました。" . $e->getMessage();
    }

    // 商品一覧ページにリダイレクト
    header('Location: ./product.php');
    exit;
}

// 商品IDが渡されているかどうかを確認する
if(isset($_GET['id'])) {
    $productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$productId) {
        $_SESSION['error_message'] = "無効な商品IDです。";
        header('Location: ./product.php');
        exit;
    }

    try {
        // トランザクションの開始
        $dbh->beginTransaction();

        // 商品を削除するSQL文を準備
        $stmt = $dbh->prepare("DELETE FROM product WHERE product_id = ?");
        // パラメータをバインドしてSQL文を実行
        $stmt->execute([$productId]);

        // トランザクションのコミット
        $dbh->commit();

        // 商品削除成功メッセージをセッションに保存
        $_SESSION['product_delete'] = "商品が正常に削除されました。";
    } catch (PDOException $e) {
        // トランザクションのロールバック
        $dbh->rollBack();
        $_SESSION['error_message'] = "商品の削除に失敗しました。" . $e->getMessage();
        exit;
    }

    // 商品一覧ページにリダイレクト
    header('Location: ./product.php');
    exit;
}

try {
    include_once('../../include/view/product_view.php');
} catch (Exception $e) {
    echo 'viewファイルの読み込みに失敗しました。' . $e->getMessage();
}