<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./stylesheet/product_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>毛鉤専門ショップ_商品管理ページ</title>

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">フライフィッシングの毛鉤専門ショップ</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav ms-auto d-flex align-items-center">
                    <li class="nav-item">
                        <form action="./product.php" method="post" class="nav-link">
                            <button type="submit" name="logout" class="btn btn-danger">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container product-container">
        <h1>商品管理ページ</h1>

        <div class="sub-title-container">
            <h3 class="sub-title">商品登録フォーム</h3>
            <?php
            // 商品登録成功メッセージがセッションにある場合、表示する
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success" role="alert">';
                echo $_SESSION['success_message'];
                echo '</div>';
                // 商品登録成功メッセージを表示した後に、セッションから削除する
                unset($_SESSION['success_message']);
            }
            // 在庫数変更成功メッセージがセッションにある場合、表示する
            if (isset($_SESSION['quantity_update'])) {
                echo '<div class="alert alert-success" role="alert">';
                echo $_SESSION['quantity_update'];
                echo '</div>';
                // 在庫数変更成功メッセージを表示した後に、セッションから削除する
                unset($_SESSION['quantity_update']);
            }
            // 公開フラグ更新成功メッセージがセッションにある場合、表示する
            if (isset($_SESSION['public_flag_update'])) {
                echo '<div class="alert alert-success" role="alert">';
                echo $_SESSION['public_flag_update'];
                echo '</div>';
                // 公開フラグ更新成功メッセージを表示した後に、セッションから削除する
                unset($_SESSION['public_flag_update']);
            }
            // 商品削除成功メッセージがセッションにある場合、表示する
            if (isset($_SESSION['product_delete'])) {
                echo '<div class="alert alert-success" role="alert">';
                echo $_SESSION['product_delete'];
                echo '</div>';
                // 商品削除成功メッセージを表示した後に、セッションから削除する
                unset($_SESSION['product_delete']);
            }
            // エラーメッセージがセッションにある場合、表示する
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger" role="alert">';
                echo $_SESSION['error_message'];
                echo '</div>';
                // エラーメッセージを表示した後に、セッションから削除する
                unset($_SESSION['error_message']);
            }
            ?>

            <form action="./product.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                <label for="product_name">商品名　　:</label>
                <input type="text" id="product_name" name="product_name" required><br>
    
                <label for="price">価　格　　:</label>
                <input type="number" id="price" name="price" required><br>
    
                <label for="quantity">個　数　　:</label>
                <input type="number" id="quantity" name="quantity" required><br>

                <label for="public_flag">公開ステータス:</label>
                <select id="public_flag" name="public_flag">
                    <option value="1">公開</option>
                    <option value="0">非公開</option>
                </select><br>
    
                <label for="product_image">商品画像　:</label>
                <input type="file" id="product_image" name="product_image" accept="image/*"><br>
    
                
                <br>
                <button type="submit" name="register">登録</button>
                
            </form>
        </div>
        
        <div class="sub-title-container">
            <h3 class="sub-title">商品情報一覧</h3>
            <table border="1" class="product-table">
                <tr>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>公開ステータス</th>
                    <th>操作</th>
                </tr>
                
                <?php if (!empty($allProducts)): ?>
                    <?php foreach ($allProducts as $product): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($product['product_image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?>" width="100"></td>
                            <td><?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?>円</td>
                            <!-- 在庫数を変更するフォーム -->
                            <td>
                                <form action="./product.php" method="post">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="number" name="quantity" value="<?= htmlspecialchars($product['quantity'], ENT_QUOTES, 'UTF-8') ?>" min="0" required>
                                    <button type="submit">変更</button>
                                </form>
                            </td>
                            <!-- 公開フラグを変更するフォーム -->
                            <td>
                                <form action="./product.php" method="post">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <select name="public_flag">
                                        <option value="1" <?php if ($product['public_flag'] == 1) echo 'selected'; ?>>公開</option>
                                        <option value="0" <?php if ($product['public_flag'] == 0) echo 'selected'; ?>>非公開</option>
                                    </select>
                                    <button type="submit">更新</button>
                                </form>
                            </td>
                            <!-- 商品を削除するaリンク -->
                            <td><a href="./product.php?id=<?= htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') ?>">削除</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">商品が見つかりませんでした。</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <!-- Bootstrap JavaScript（オプション） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>