<?php
$title = '毛鉤専門ショップ_購入完了ページ';
$stylesheet = './stylesheet/purchase_comp_styles.css';
$navItems = [
    ['url' => './shopping.php', 'label' => '商品一覧'],
    ['url' => './mypage.php', 'label' => 'マイページ'],
    ['form' => ['action' => './purchase_comp.php', 'name' => 'logout'], 'label' => 'ログアウト']
];
require_once '../include/view/templates/header.php';
?>
    
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
<?php require_once '../include/view/templates/footer.php'; ?>