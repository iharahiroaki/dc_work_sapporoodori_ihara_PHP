<?php
$title = '毛鉤専門ショップ_商品管理ページ';
$stylesheet = './stylesheet/product_styles.css';
$navItems = [
    ['form' => ['action' => './product.php', 'name' => 'logout'], 'label' => 'ログアウト']
];
require_once '../include/view/templates/header.php';
?>
<div class="container product-container">
    <h1>商品管理ページ</h1>
    <div class="sub-title-container">
        <h3 class="sub-title">商品登録フォーム</h3>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success" role="alert">';
            echo $_SESSION['success_message'];
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['quantity_update'])) {
            echo '<div class="alert alert-success" role="alert">';
            echo $_SESSION['quantity_update'];
            echo '</div>';
            unset($_SESSION['quantity_update']);
        }
        if (isset($_SESSION['public_flag_update'])) {
            echo '<div class="alert alert-success" role="alert">';
            echo $_SESSION['public_flag_update'];
            echo '</div>';
            unset($_SESSION['public_flag_update']);
        }
        if (isset($_SESSION['product_delete'])) {
            echo '<div class="alert alert-success" role="alert">';
            echo $_SESSION['product_delete'];
            echo '</div>';
            unset($_SESSION['product_delete']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger" role="alert">';
            echo $_SESSION['error_message'];
            echo '</div>';
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
        <h3 class="sub-title">登録済み商品一覧</h3>
        <div class="product-table-container">
            <table class="product-table">
                <tr>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>画像</th>
                    <th>公開フラグ</th>
                    <th>削除</th>
                </tr>
                <?php foreach ($allProducts as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?></td>
                        <td>
                            <form action="./product.php" method="post">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                <input type="number" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>">
                                <button type="submit">更新</button>
                            </form>
                        </td>
                        <td><img src="<?= htmlspecialchars($product['product_image']) ?>" alt="Product Image" class="product-image"></td>
                        <td>
                            <form action="./product.php" method="post">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                <select name="public_flag">
                                    <option value="1" <?= $product['public_flag'] == 1 ? 'selected' : '' ?>>公開</option>
                                    <option value="0" <?= $product['public_flag'] == 0 ? 'selected' : '' ?>>非公開</option>
                                </select>
                                <button type="submit">更新</button>
                            </form>
                        </td>
                        <td>
                            <form action="./product.php" method="get">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                <button type="submit" onclick="return confirm('本当に削除しますか？')">削除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
<?php require_once '../include/view/templates/footer.php'; ?>