<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../view/stylesheet/purchase_comp_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>毛鉤専門ショップ_購入完了ページ</title>
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
                        <form action="./purchase_comp.php" method="post" class="nav-link">
                            <button type="submit" name="logout" class="btn btn-danger">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container purchase-container">
        <div class="comp-message">
            <h1>購入が完了しました！</h1>
        </div>
    
        <p>ご購入いただきありがとうございました。</p>
        <table border="1" class="purchase-table">
            <thead>
                <tr>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>数量</th>
                    <th>小計</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($purchase_items as $item): ?>
                <tr>
                    <td><?php echo isset($item['product_image']) ? '<img src="' . $item['product_image'] . '" alt="' . $item['product_name'] . '" style="max-width: 100px; max-height: 100px;">' : ''; ?></td>
                    <td><?php echo isset($item['product_name']) ? $item['product_name'] : ''; ?></td>
                    <td><?php echo isset($item['price']) ? number_format($item['price']) . '円' : ''; ?></td>
                    <td><?php echo isset($item['quantity']) ? $item['quantity'] : ''; ?></td>
                    <td><?php echo isset($item['price']) && isset($item['quantity']) ? number_format($item['price'] * $item['quantity']) . '円' : ''; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-container">
            <?php
            // 合計金額を計算
            $total_price = 0;
            foreach ($purchase_items as $item) {
                $total_price += $item['price'] * $item['quantity'];
            }
            ?>
            <p>合計金額: <?php echo number_format($total_price); ?>円</p>
        </div>

    </div>

    <!-- Bootstrap JavaScript（オプション） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>