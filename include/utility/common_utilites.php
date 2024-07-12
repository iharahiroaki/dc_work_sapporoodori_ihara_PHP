<?php
// HTMLエスケープを行う関数
function escapeHTML($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// リダイレクトを行う関数
function redirect($url) {
    header("Location: $url");
    exit;
}

// セッションを安全に開始する関数
function safeSessionStart() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// セッションを安全に破棄する関数
function safeSessionDestroy() {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

// ログアウト処理する関数
function logout() {
  // セッションを破棄してログアウトする
  session_unset();
  session_destroy();
  // ログアウト後はログインページにリダイレクトする
  header("Location: ./index.php");
  exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'logout') {
  logout();
}

// データを取得する関数
function getAllData($dbh, $table) {
    try {
        // ①sql文の準備
        $sql = "SELECT * FROM " . $table;
        // ②sql文の実行
        $stmt = $dbh->query($sql);
        // ③sql文の結果取り出し
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch(PDOException $e) {
        echo 'クエリの実行に失敗しました。' . $e->getMessage();
        exit();
    }
}