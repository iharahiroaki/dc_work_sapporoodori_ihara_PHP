<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./stylesheet/mypage_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="path/to/bootstrap.min.css">

    <title>毛鉤専門ショップ_マイページ</title>
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
                        <a class="nav-link" href="./cart.php">買い物カゴ</a>
                    </li>
                    <li class="nav-item">
                        <form action="./index.php" method="post" class="nav-link">
                            <button type="submit" name="logout" class="btn btn-danger">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php 
    // mypage_process.phpをインクルード
    require_once('./mypage_process.php');
    ?>

    <div class="container mypage-container">
        <h1>マイページ</h1>
        <p>ようこそ、<?= $_SESSION['username'] ?>さん</p>
   
        <div class="history">
            <h2>購入履歴</h2>
            <table border="1" class="mypage-table">
                <thead>
                    <tr>
                        <th>購入ID</th>
                        <th>商品名</th>
                        <th>購入数量</th>
                        <th>購入日時</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($allProducts)): ?>
                        <?php foreach ($purchase_history as $purchase): ?>
                            <tr>
                                <td><?= $purchase['purchase_id'] ?></td>
                                <td><?= getProductInfo($dbh, $purchase['product_id'])['product_name'] ?></td>
                                <td><?= $purchase['quantity'] ?></td>
                                <td><?= $purchase['purchase_date'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>商品が見つかりませんでした。</p>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JavaScript（オプション） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>