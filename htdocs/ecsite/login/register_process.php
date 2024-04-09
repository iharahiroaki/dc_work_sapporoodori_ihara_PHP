<?php
// セッションを開始
session_start();

// ブラウザにエラーを表示
ini_set('display_errors', "On");

// データベースに接続
require_once('../dbConnect.php');
$dbh = dbConnect();

// function.phpの読み込み
require_once('../function.php');

// もしログイン済みであれば、shopping.phpにリダイレクト
if (isset($_SESSION['username'])) {
    header("Location: ../shopping.php");
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

    // 分岐1: ユーザーIDとパスワードが指定の値であれば、product.phpに遷移する
    if ($username === 'ec.admin' && $password === 'ec.admin') {
        // 管理者フラグをセッションに設定
        $_SESSION['admin'] = true;
        // 管理者用のセッションにユーザー情報を保存
        $_SESSION['username'] = $username;
        header("Location: ../product.php");
        exit;
    } else {
        // データベースからユーザー情報を取得し、ユーザーIDとパスワードを検証する
        $stmt = $dbh->prepare("SELECT * FROM user WHERE user_name = :username");
        $stmt->bindParam(':username', $username);
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
            // ユーザーテーブルにセッションIDを保存する処理を追加する
            // 現在のセッションIDを取得
            $session_id = session_id();
            // ユーザーテーブルにセッションIDを保存
            save_session_id_to_database($username, $session_id, $dbh);
            header("Location: ../shopping.php");
            exit;
        } else {
            // 分岐3: データベースに格納された情報と不一致ならば、エラーメッセージを表示してindex.phpに戻る
            $error_message = "ユーザーIDまたはパスワードが間違っています。";
        }
    }
}

// ユーザーテーブルにセッションIDを保存する関数
function save_session_id_to_database($username, $session_id, $dbh) {
    // ユーザー名に対応するレコードのセッションIDを更新する
    $stmt = $dbh->prepare("UPDATE user SET session_id = :session_id WHERE user_name = :username");
    $stmt->bindParam(':session_id', $session_id);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>毛鉤専門ショップ_ログインページ</title>

    <link rel="stylesheet" href="../stylesheet/index_styles.css">

</head>
<body>
    <header>
        <h1 class="site-title">フライフィッシングの毛鉤専門ショップ</h1>
    </header>
    
    <div class="login-container">
        <h2 class="login-title">ディレクトリ移動</h2>
        <?php if (isset($error_message)): ?>
            <script>
                alert("<?php echo htmlspecialchars($error_message, ENT_QUOTES); ?>");
            </script>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="login-form">
            <div class="form-group">
                <label for="username" class="login-label">ユーザーID:</label>
                <input type="text" name="username" id="username" required><br><br> <!-- ※required属性でnull禁止 -->
            </div>
            <div class="form-group">
                <label for="password" class="login-label">パスワード:</label>
                <input type="password" name="password" id="password" required><br><br> <!-- ※required属性でnull禁止 -->
            </div>
            <input type="submit" value="ログイン">
        </form>
        <br>
        <a href="./register.php" class="signup">新規登録はここをクリック！</a>
    </div>
</body>
</html>