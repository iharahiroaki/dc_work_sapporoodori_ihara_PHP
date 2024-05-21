<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品管理画面</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h3>商品管理画面</h3>
            <!-- 成功メッセージの表示 -->
            <?php if (!empty($_SESSION['successMessage'])): ?>
        <div class="success">
            <?php echo h($_SESSION['successMessage']); unset($_SESSION['successMessage']); ?>
        </div>
            <?php endif; ?>
            <!-- エラーメッセージの表示 -->
            <?php if (!empty($_SESSION['errMessage'])): ?>
        <div class="error">
            <?php echo h($_SESSION['errMessage']); unset($_SESSION['errMessage']); ?>
        </div>
            <?php endif; ?>

        <div class="form-container">
            <form action="product_management.php" method="post" enctype="multipart/form-data">
                商品名：<input type="text" name="productName"><br>
                価格：<input type="number" name="price"><br>
                個数：<input type="number" name="productQty"><br>
                画像：<input type="file" name="fileName"><br>
                公開状態：
                <input type="radio" name="publicFlg" value="1" checked>公開
                <input type="radio" name="publicFlg" value="0">非公開<br>
                <input type="submit" value="商品を登録">
            </form>
        </div>
        <!-- ここからテーブルを追加 -->
        <table border="1" rules="rows" style="margin:20px auto; width: 80%;">
            <thead>
                <tr>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>公開・非公開</th>
                    <th>削除</th>
                </tr>
            </thead>
            <tbody style="text-align:center;">
            <?php
            // Controllerから渡された商品データの配列をループ処理
            foreach ($rows as $row) :
                $productName = h($row['product_name']);
                $price = h($row['price']);
                $productId = h($row['product_id']);
                $stockQty = h($row['stock_qty']);
                $publicFlg = h($row['public_flg']); // キー名を修正
                $imagePath = h('assets/products/' . $row['image_name']); // キー名を修正
                $rowStyle = $publicFlg == 0 ? ' style="background-color: #f2f2f2;"' : '';// 公開フラグが0の場合、背景色を薄グレーに設定
            ?>
                <tr<?php echo $rowStyle; ?>>
                    <td><img src="<?php echo $imagePath; ?>" alt="商品画像" style="width: 100px; height: auto;"></td>
                    <td><?php echo $productName; ?></td>
                    <td><?php echo $price; ?>円</td>
                    <td>
                        <form action="product_management.php" method="post">
                            <input type="number" name="new_quantity" value="<?php echo $stockQty; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                            <input type="submit" name="update_stock" value="在庫数を更新">
                        </form>
                    </td>
                    <td>
                        <form action="product_management.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                            <!-- 現在のpublic_flgの値に基づいて値を反転させる -->
                            <input type="hidden" name="public_flg" value="<?php echo $row['public_flg']; ?>">
                            <input type="submit" name="toggle_public" value="<?php echo $row['public_flg'] == 1 ? "非公開に" : "公開に" ; ?>">
                        </form>
                    </td>
                    <td>
                        <form method="post"><input type="submit" name="<?php echo $row['product_id']; ?>" value="削除"></form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
