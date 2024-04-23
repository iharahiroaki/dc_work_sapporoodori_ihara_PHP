<?php
// セッションを開始
session_start();

// ブラウザにエラーを表示
ini_set('display_errors', "On");

// もし未ログインであれば、index.phpにリダイレクト
if (!isset($_SESSION['username'])) {
    header("Location: ./index.php");
    exit;
}

// データベースに接続
require_once './dbConnect.php';
$dbh = dbConnect();

// function.phpの読み込み
require_once('./function.php');

// データを取得
$table = 'product';
$allProducts = getAllData($dbh, $table);

// ログアウト処理
if (isset($_POST['logout'])) {
    logout();
}

// 商品の登録
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // フォームから送信されたデータが存在するかを確認
    if(isset($_POST['product_name'], $_POST['price'], $_POST['quantity'], $_FILES['product_image'], $_POST['public_flag'])) {
        // フォームから送信されたデータを取得
        $productName = $_POST['product_name'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $productImage = $_FILES['product_image']['name'];
        $publicFlag = $_POST['public_flag'];
        
        // 商品画像の拡張子をチェック
        $allowedExtensions = array('jpg', 'jpeg', 'png');
        $fileExtension = pathinfo($productImage, PATHINFO_EXTENSION);
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "画像ファイルの拡張子は.jpg、.jpeg、.pngのみ許可されています。";
            exit;
        }
        
        // 商品情報をセッションに保存
        $product = [
            'product_name' => $productName,
            'price' => $price,
            'quantity' => $quantity,
            'product_image' => $productImage,
            'public_flag' => $publicFlag,
        ];

        // セッションに商品情報を追加
        $_SESSION['products'][] = $product;

        // 商品画像のアップロード処理
        $imagePath = 'product_images/' . $productImage;
        move_uploaded_file($_FILES['product_image']['tmp_name'], $imagePath);

        // 商品情報をデータベースに挿入
        $stmt = $dbh->prepare("INSERT INTO product (product_name, price, quantity, product_image, public_flag) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$productName, $price, $quantity, $imagePath, $publicFlag]);

        // 商品登録成功メッセージをセッションに保存
        $_SESSION['register_success'] = "商品が正常に登録されました。";

        // 商品一覧ページにリダイレクト
        header('Location: ./product.php');
        exit;
    } else {
        // フォームからのデータが提供されていない場合はエラーメッセージを出力するか、適切な処理を行います。
        echo "フォームからのデータが提供されていません。";
    }
}

// 在庫数を変更する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    // フォームから送信されたデータを取得
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // 在庫数をデータベースに更新
    $stmt = $dbh->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
    $stmt->execute([$quantity, $productId]);

    // 在庫数変更成功メッセージをセッションに保存
    $_SESSION['quantity_update'] = "在庫数が正常に変更されました。";

    // 商品一覧ページにリダイレクト
    header('Location: ./product.php');
    exit;
}

// 公開フラグの更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['public_flag'])) {
    $productId = $_POST['product_id'];
    $publicFlag = $_POST['public_flag'];

    // 公開フラグをデータベースに更新
    $stmt = $dbh->prepare("UPDATE product SET public_flag = ? WHERE product_id = ?");
    $stmt->execute([$publicFlag, $productId]);

    // 公開フラグ更新成功メッセージをセッションに保存
    $_SESSION['public_flag_update'] = "公開フラグが正常に更新されました。";

    // 商品一覧ページにリダイレクト
    header('Location: ./product.php');
    exit;
}

include_once('./product.php');
?>