<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../include/view/stylesheet/cart_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>毛鉤専門ショップ_ショッピングカートページ</title>
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
                        <a class="nav-link" href="./shopping.php">商品一覧</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./mypage.php">マイページ</a>
                    </li>
                    <li class="nav-item">
                        <form action="./cart.php" method="post" class="nav-link">
                            <button type="submit" name="logout" class="btn btn-danger">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <h1>カート</h1>

        <?php if (empty($cart_items)): ?>
            <p>カートに商品がありません。</p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>商品画像</th>
                        <th>商品名</th>
                        <th>金額</th>
                        <th>数量</th>
                        <th>小計</th>
                        <th>購入をやめる</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <?php $product_info = getProductInfo($dbh, $item['product_id']); ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($product_info['product_image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($product_info['product_name'], ENT_QUOTES) ?>" style="width: 100px;"></td>
                            <td><?= htmlspecialchars($product_info['product_name'], ENT_QUOTES) ?></td>
                            <td><?= number_format($product_info['price']) ?>円</td>
                            <td>
                                <form action="" method="post" onsubmit="return confirmQuantityChange()">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_info['product_id'], ENT_QUOTES) ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1">
                                    <button type="submit" name="update" class="btn btn-primary">変更</button>
                                </form>
                            </td>
                            <td><?= number_format($product_info['price'] * $item['quantity']) ?>円</td>
                            <td>
                                <form action="" method="post" onsubmit="return confirmDelete()">
                                    <button type="submit" name="delete" value="<?= htmlspecialchars($item['product_id'], ENT_QUOTES) ?>" class="btn btn-danger">削除</button>
                                </form>
                            </td>

                            <script>
                                // 数量変更の確認メッセージを表示する関数
                                function confirmQuantityChange() {
                                    return confirm('商品の数量が変更されました');
                                }

                                // 削除の確認メッセージを表示する関数
                                function confirmDelete() {
                                    return confirm('商品がカートから削除されました');
                                }
                            </script>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- カートの合計金額を表示 -->
        <div class="container text-center">
            合計金額: <?= number_format($total_price) ?>円
        </div>

        <!-- 購入するボタン -->
        <form action="" method="post" class="text-center mt-3">
            <button type="submit" name="purchase" class="btn btn-primary">購入する</button>
        </form>
    </div>

    <!-- Bootstrap JavaScript（オプション） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

  </body>
</html>