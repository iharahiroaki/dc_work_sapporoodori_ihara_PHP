<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../view/stylesheet/product_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>毛鉤専門ショップ_商品管理ページ</title>

    <script>
    function validateForm() {
        var productName = document.getElementById('product_name').value;
        var price = document.getElementById('price').value;
        var quantity = document.getElementById('quantity').value;
        var productImage = document.getElementById('product_image').value;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

        var errorMessage = '';

        if (productName === '') {
            errorMessage += '商品名が未入力です。\n';
        }
        if (price === '' || isNaN(price) || price <= 0 || !Number.isInteger(parseFloat(price))) {
            errorMessage += '価格は1以上の整数を入力してください。\n';
        }
        if (quantity === '' || isNaN(quantity) || quantity <= 0 || !Number.isInteger(parseFloat(quantity))) {
            errorMessage += '個数は1以上の整数を入力してください。\n';
        }
        if (productImage === '') {
            errorMessage += '商品画像が未選択です。\n';
        } else if (!allowedExtensions.exec(productImage)) {
            errorMessage += '画像ファイルの拡張子は.jpg、.jpeg、.pngのみ許可されています。\n';
        }

        if (errorMessage !== '') {
            alert(errorMessage);
            return false;
        }
        return true;
    }

    </script>

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
                        <form action="../ec_site/product.php" method="post" class="nav-link">
                            <button type="submit" name="logout" class="btn btn-danger">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- product.phpをインクルード -->
    <?php 
    require_once('../ec_site/product.php');
    ?>

    <div class="container product-container">
        <h1>商品管理ページ</h1>

        <div class="sub-title-container">
            <h3 class="sub-title">商品登録フォーム</h3>
                <form action="../ec_site/product.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
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
                            <td><img src="<?= $product['product_image'] ?>" alt="<?= $product['product_name'] ?>" width="100"></td>
                            <td><?= $product['product_name'] ?></td>
                            <td><?= $product['price'] ?>円</td>
                            <!-- 在庫数を変更するフォーム -->
                            <td>
                                <form action="./product.php" method="post">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <input type="number" name="quantity" value="<?= $product['quantity'] ?>" min="0" required>
                                    <button type="submit">変更</button>
                                </form>
                            </td>
                            <!-- 公開フラグを変更するフォーム -->
                            <td>
                                <form action="../ec_site/product.php" method="post">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <select name="public_flag">
                                        <option value="1" <?php if ($product['public_flag'] == 1) echo 'selected'; ?>>公開</option>
                                        <option value="0" <?php if ($product['public_flag'] == 0) echo 'selected'; ?>>非公開</option>
                                    </select>
                                    <button type="submit">更新</button>
                                </form>
                            </td>
                            <!-- 商品を削除するaリンク -->
                            <td><a href="../ec_site/product.php?id=<?= $product['product_id'] ?>">削除</a></td>
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

    <script>
        // 商品登録成功時のメッセージ表示
        <?php if (isset($_SESSION['register_success'])): ?>
            alert("<?php echo $_SESSION['register_success']; ?>");
            <?php unset($_SESSION['register_success']); ?>
        <?php endif; ?>

        // 在庫数変更時のメッセージ表示
        <?php if (isset($_SESSION['quantity_update'])): ?>
            alert("<?php echo $_SESSION['quantity_update']; ?>");
            <?php unset($_SESSION['quantity_update']); ?>
        <?php endif; ?>

        // 公開フラグ更新時のメッセージ表示
        <?php if (isset($_SESSION['public_flag_update'])): ?>
            alert("<?php echo $_SESSION['public_flag_update']; ?>");
            <?php unset($_SESSION['public_flag_update']); ?>
        <?php endif; ?>

        // 商品削除時のメッセージ表示
        <?php if (isset($_SESSION['product_delete'])): ?>
            alert("<?php echo $_SESSION['product_delete']; ?>");
            <?php unset($_SESSION['product_delete']); ?>
        <?php endif; ?>
    </script>

    <!-- Bootstrap JavaScript（オプション） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>