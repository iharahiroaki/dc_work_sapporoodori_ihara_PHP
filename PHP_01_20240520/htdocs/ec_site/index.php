<?php
session_start();

require_once '../../include/model/index_model.php';
require_once '../../include/utility/common_utilities.php';

// エラーメッセージ用の変数を初期化
$errorMessage = ''; 

// ログアウトの処理
if (isset($_POST["logout"])) {
    // セッション変数を空にする
    $_SESSION = [];
    // セッションを破棄する
    session_destroy();
    // ログインページにリダイレクト
    header('Location: loginPage.php');
    exit();
}

// ログイン済みの場合は商品一覧ページにリダイレクト
if (isset($_SESSION['login_id'])) {
    header('Location: catalog.php');
    exit();
}

// ログインフォームが送信された場合
if (!empty($_POST['login_id']) && !empty($_POST['password'])) {
    // ユーザー認証
    if (authenticate($_POST['login_id'], $_POST['password'])) {
        // 認証成功: セッションにログインIDを保存
        $_SESSION['login_id'] = $_POST['login_id'];
        // 管理者の場合は管理ページへ、それ以外は商品一覧ページへリダイレクト
        $redirectPage = ($_POST['login_id'] == "ec_admin") ? 'product_management.php' : 'catalog.php';
        header('Location: ' . $redirectPage);
        exit();
    } else {
        // 認証失敗: エラーメッセージを設定
        $errorMessage = 'ログインが失敗しました:正しいログインID(半角英数字)を入力してください。';
    }
}

// ビューファイルを読み込み、フォームやエラーメッセージを表示
require_once '../../include/view/index_view.php';
?>
