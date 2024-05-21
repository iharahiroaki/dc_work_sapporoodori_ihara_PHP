<?php
session_start();

require_once '../../include/model/signup_model.php';
require_once '../../include/utility/common_utilities.php';

// すでにログインしている場合は商品一覧ページへリダイレクトします。
if (isset($_SESSION['login_id'])) {
    header('Location: catalog.php');
    exit();
}

// エラーメッセージ用の変数を初期化します。
$errorMessage = "";

// フォームが送信された（POSTメソッドでアクセスされた）場合の処理を開始します。
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // フォームから送信されたユーザー名とパスワードを変数に格納します。
    $loginId = $_POST['login_id'];
    $password = $_POST['password'];

    // 入力値のバリデーションを行い、エラーがあればメッセージを設定します。
    $errorMessage = validateInput($loginId, $password);

    // エラーメッセージが空（つまりエラーがない）場合、ユーザーをデータベースに登録します。
    if (empty($errorMessage)) {
        // ユーザー登録処理を実行し、成功すればユーザーIDが返されます。
        $userId = insertUser($loginId, $password);
        if ($userId) {
            // 登録成功時のメッセージを設定します。
            $errorMessage = "User registered successfully.";
        } else {
            // 登録失敗時のメッセージを設定します。
            $errorMessage = "Failed to register user.";
        }
    }
}

// 最終的な画面（ビュー）を表示するためのファイルを読み込みます。
include_once '../../include/view/signup_view.php';
?>
