<?php
$title = '毛鉤専門ショップ_商品一覧ページ';
$stylesheet = './stylesheet/shopping_styles.css';
$navItems = [
    ['form' => ['action' => './shopping.php', 'name' => 'logout'], 'label' => 'ログアウト']
];
require_once '../include/view/templates/header.php';
?>
<div class="container">
    <h1>商品一覧ページ</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <div class="row">
        <?php foreach ($publicProducts as $product): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="<?= htmlspecialchars($product['product_image'], ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?></h5>
                        <p class="card-text"><?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?>円</p>
                        <form action="./shopping.php" method="post">
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="number" name="num" value="1" min="1" max="<?= htmlspecialchars($product['quantity'], ENT_QUOTES, 'UTF-8') ?>" class="form-control mb-2">
                            <button type="submit" class="btn btn-primary">カートに追加</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once '../include/view/templates/footer.php'; ?>
