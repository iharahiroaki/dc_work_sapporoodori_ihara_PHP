<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>毛鉤専門ショップ_商品一覧ページ</title>
    
    <link rel="stylesheet" href="../include/view/stylesheet/shopping_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">フライフィッシングの毛鉤専門ショップ</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto d-flex align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="./cart.php">買い物カゴ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./mypage.php">マイページ</a>
                    </li>
                    <li class="nav-item">
                        <form action="./shopping.php" method="post" class="nav-link">
                            <button type="submit" name="logout" class="btn btn-danger">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- shopping.phpをインクルード -->
    <?php 
    require_once('./shopping.php');
    ?>

    <div class="container text-center">
        <h1>ショッピングページ</h1>
        <p>ようこそ、<?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES); ?> さん</p>
        
        <strong class="h6 text-danger"><a href="./cart.php" class="text-danger">買い物カゴ</a>は、
        <?php
            if(isset($_SESSION["cart"]) && count($_SESSION["cart"]) > 0) {
                echo count($_SESSION["cart"]) . "商品が入っています。";
            } else {
                echo "空です。";
            }
        ?>
        </strong>
    </div>
    
    <!-- カートの合計金額を表示 -->
    <div class="container total-container text-center mt-3">
        <?php
            $total_price = 0;
            if(isset($_SESSION["cart"]) && count($_SESSION["cart"]) > 0) {
                foreach ($_SESSION["cart"] as $cart_item) {
                    // カート内の商品の価格を取得して合計金額を計算
                    $product_id = $cart_item['product_id'];
                    $stmt = $dbh->prepare("SELECT price FROM product WHERE product_id = ?");
                    $stmt->execute([$product_id]);
                    $price = $stmt->fetchColumn();
                    $total_price += $price * $cart_item['quantity'];
                }
                echo "合計金額: " . number_format($total_price) . "円";
            }
        ?>
    </div>

    <div class="shopping-container">
        <?php foreach (array_chunk($publicProducts, 2) as $products_chunk): ?>
            <div class="row">
                <?php foreach ($products_chunk as $product): ?>
                    <div class="col-md-6">
                        <div class="product">
                            <?php
                                // 商品の在庫数量を取得
                                $stmt = $dbh->prepare("SELECT quantity FROM product WHERE product_id = ?");
                                $stmt->execute([$product['product_id']]);
                                $available_quantity = $stmt->fetchColumn();
                                
                                // 在庫が0の場合は売り切れ表示
                                if ($available_quantity == 0) {
                                    echo '<div class="product-image-container">';
                                    echo '<img src="' . htmlspecialchars($product['product_image'], ENT_QUOTES) . '" class="img-fluid rounded">';
                                    echo '<div class="sold-out-overlay">';
                                    echo '<span class="sold-out-text">売り切れ</span>';
                                    echo '</div>';
                                    echo '</div>';
                                } else {
                                    echo '<img src="' . htmlspecialchars($product['product_image'], ENT_QUOTES) . '" class="img-fluid rounded">';
                                }
                            ?>
                            <h3><?= htmlspecialchars($product['product_name'], ENT_QUOTES) ?></h3>
                            <span class="text-danger"><?= number_format($product['price']) ?>円</span>
                            <form action="" class="mt-3" method="post">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id'], ENT_QUOTES) ?>">
                                <?php if ($available_quantity > 0): ?>
                                    <select name="num">
                                        <?php for ($i = 1; $i <= $available_quantity; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary" onclick="showMessage()">カゴに入れる</button>
                                <?php else: ?>
                                    <!-- 在庫が0の場合はボタンを無効化 -->
                                    <button type="button" class="btn btn-primary" disabled>売り切れ</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function showMessage() {
            alert("カートに商品が追加されました");
        }
    </script>

    <!-- Bootstrap JavaScript（オプション） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>