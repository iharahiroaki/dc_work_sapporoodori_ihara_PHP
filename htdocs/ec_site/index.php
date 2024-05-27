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

// もしログイン済みであれば、shopping.phpにリダイレクト
if (isset($_SESSION['username'])) {
    header("Location: ../ec_site/shopping.php");
    exit;
}

// フォームが送信されたかどうかを確認する
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ユーザーIDとパスワードが指定の値であるかチェックする
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // XSS対策
    $username = htmlspecialchars($username, ENT_QUOTES);
    $password = htmlspecialchars($password, ENT_QUOTES);


    // 入力の検証
    if(empty($username) || empty($password)) {
        $error_message = "ユーザーIDおよびパスワードを入力してください。";
    } else if (!preg_match('/^[a-zA-Z0-9_]{5,}$/', $username)) {
        $error_message = "ユーザーIDは半角英数字、5文字以上で入力してください。";
    } else if (!preg_match('/^[a-zA-Z0-9_]{8,}$/', $password)) {
        $error_message = "パスワードは半角英数字、8文字以上で入力してください。";
    } else {
        // 分岐1: ユーザーIDとパスワードが指定の値であれば、product.phpに遷移する
        if ($username === 'ec_admin' && $password === 'ec_admin') {
            // 管理者フラグをセッションに設定
            $_SESSION['admin'] = true;
            // 管理者用のセッションにユーザー情報を保存
            $_SESSION['username'] = $username;
            header("Location: ../ec_site/product.php");
            exit;
        } else {
            // データベースからユーザー情報を取得し、ユーザーIDとパスワードを検証する
            try {
                $stmt = $dbh->prepare("SELECT * FROM user WHERE user_name = :username");
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
                // 分岐2: データベースに格納された情報と一致する場合は、セッションにユーザー名とユーザーIDを保存してshopping.phpに遷移する
                if ($user && password_verify($password, $user['password'])) {
                    // セッションハイジャック対策
                    session_regenerate_id(true);
                    // 管理者フラグがfalseであればユーザーログイン
                    $_SESSION['admin'] = false;
                    // ユーザー名をセッションに保存
                    $_SESSION['username'] = $username;
                    // ユーザーIDをセッションに保存
                    $_SESSION['user_id'] = $user['user_id'];
                    // 現在のセッションIDを取得
                    $session_id = session_id();
                    // ユーザーテーブルにセッションIDを保存
                    save_session_id_to_database($username, $session_id, $dbh);
                    header("Location: ../ec_site/shopping.php");
                    exit;
                } else {
                    // 分岐3: データベースに格納された情報と不一致の場合、エラーメッセージを表示
                    $error_message = "ユーザーIDまたはパスワードが間違っています。";
                }
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error_message = "データベースエラーが発生しました。";
            }
        }
    }
}

// ユーザーテーブルにセッションIDを保存する関数
function save_session_id_to_database($username, $session_id, $dbh) {
    // ユーザー名に対応するレコードのセッションIDを更新する
    $stmt = $dbh->prepare("UPDATE user SET session_id = :session_id WHERE user_name = :username");
    $stmt->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
}

try {
    require_once('../../include/view/index_view.php');
} catch (Exception $e) {
    echo 'viewファイルの読み込みに失敗しました。' . $e->getMessage();
}