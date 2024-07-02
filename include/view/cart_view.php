<?php
$title = '毛鉤専門ショップ_買い物カゴページ';
$stylesheet = './stylesheet/cart_styles.css';
$navItems = [
    ['url' => './shopping.php', 'label' => '商品一覧'],
    ['url' => './mypage.php', 'label' => 'マイページ'],
    ['form' => ['action' => './cart.php', 'name' => 'logout'], 'label' => 'ログアウト']
];
require_once '../include/view/templates/header.php';
?>

<div class="cart-container">
    <h1>カート</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($_SESSION['message'], ENT_QUOTES); ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES); ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

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
<?php require_once '../include/view/templates/footer.php'; ?>