<?php
$title = '毛鉤専門ショップ_マイページ';
$stylesheet = './stylesheet/mypage_styles.css';
$navItems = [
    ['url' => './shopping.php', 'label' => '商品一覧'],
    ['url' => './cart.php', 'label' => '買い物カゴ'],
    ['form' => ['action' => './mypage.php', 'name' => 'logout'], 'label' => 'ログアウト']
];
require_once '../include/view/templates/header.php';
?>

<div class="container mypage-container">
    <h1>マイページ</h1>
    <p>ようこそ、<?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>さん</p>

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
<?php require_once '../include/view/templates/footer.php'; ?>