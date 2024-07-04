<?php
function addItemToCart($dbh, &$cart_items) {
    // 入力データのバリデーション
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $num = filter_input(INPUT_POST, 'num', FILTER_VALIDATE_INT);
    
    if ($pruduct_id === false || $num === false || $num <= 0) {
        $_SESSION['error'] = '無効な入力です。';
        header('Location: ./cart.php');
        exit;
    }

    try {
        // トランザクションの開始
        $dbh->beginTransaction();
        // カートに商品がすでに存在するかチェック
        $existing_index = array_search($product_id, array_column($cart_items, 'product_id'));
        
        if($existing_index !== false) {
            // すでにカートに存在する場合は数量を追加
            $cart_items[$existing_index]['quantity'] += $num;
            // データベースのカート情報も更新する
            $stmt = $dbh->prepare("UPDATE cart SET quantity = quantity + :quantity WHERE product_id = :product_id");
            $stmt->bindParam(':quantity', $num, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // カートに新しい商品を追加
            $product_info = getProductInfo($dbh, $product_id);
            if($product_info) {
                $product_info['quantity'] = $num;
                $cart_items[] = $product_info;
                $stmt = $dbh->prepare("INSERT INTO cart (quantity, product_id) VALUES (:quantity, :product_id)");
                $stmt->bindParam(':quantity', $num, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        // トランザクションのコミット
        $dbh->commit();
        $_SESSION['message'] = '商品がカートに追加されました。';
    } catch (PDOException $e) {
        // データベースエラーの処理
        $dbh->rollBack();
        error_log($e->getMessage());
        $_SESSION['error'] = 'データベースエラーが発生しました。';
        header('Location: ./cart.php');
        exit;
    }
    header('Location: ./cart.php');
    exit;
}

// 削除ボタンがクリックされた場合の関数
function removeItemFromCart($dbh, &$cart_items, $product_id) {
    $product_id = filter_input(INPUT_POST, 'delete', FILTER_VALIDATE_INT);

    if ($product_id !== false) {
        try {
            // トランザクションの開始
            $dbh->beginTransaction();

            removeFromCart($dbh, $product_id);

            // カート情報を更新
            $cart_items = array_filter($cart_items, function ($item) use ($product_id) {
                return $item['product_id'] != $product_id;
            });
            $_SESSION['cart'] = $cart_items;

            // トランザクションのコミット
            $dbh->commit();

            $_SESSION['message'] = '商品がカートから削除されました。';
        } catch (PDOException $e) {
            // トランザクションのロールバック
            $dbh->rollBack();
            error_log($e->getMessage());
            $_SESSION['error'] = 'データベースエラーが発生しました。';
            header('Location: ./cart.php');
            exit;
        }
        header('Location: ./cart.php');
        exit;
    } else {
        $_SESSION['error'] = '無効な商品IDです。';
        header('Location: ./cart.php');
        exit;
    }
}

// 購入処理の関数
function processPurchase($dbh, &$cart_items) {
    // 在庫数をチェック
    $out_of_stock = false;
    foreach ($cart_items as $item) {
        $product_info = getProductInfo($dbh, $item['product_id']);
        if ($product_info['quantity'] < $item['quantity']) {
            // 在庫が足りない場合はフラグを立ててループを抜ける
            $out_of_stock = true;
            $product_name = $product_info['product_name'];
            break;
        }
    }
    
    if ($out_of_stock) {
        // 在庫がない場合の処理
        $_SESSION['error'] = "たった今{$product_name}の在庫がなくなりました！商品を選び直してください。";
        header('Location: ./cart.php');
        exit;
    } else {
        // 在庫がある場合の処理
        // 在庫数の更新
        try {
            // トランザクションの開始
            $dbh->beginTransaction();
            foreach ($cart_items as $item) {
                $product_info = getProductInfo($dbh, $item['product_id']);
                $new_quantity = $product_info['quantity'] - $item['quantity'];
                $stmt = $dbh->prepare("UPDATE product SET quantity = :quantity WHERE product_id = :product_id");
                $stmt->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
                $stmt->execute();
            }
            // トランザクションのコミット
            $dbh->commit();
        } catch (PDOException $e) {
            // トランザクションのロールバック
            $dbh->rollBack();
            error_log($e->getMessage());
            $_SESSION['error'] = 'トランザクションエラーが発生しました。';
            header('Location: ./cart.php');
            exit;
        }

        // 購入した商品の情報をセッションに保存
        $purchase_items = [];
        foreach ($cart_items as $item) {
            $product_info = getProductInfo($dbh, $item['product_id']);
            $purchase_items[] = [
                'product_image' => htmlspecialchars($product_info['product_image'], ENT_QUOTES),
                'product_name' => htmlspecialchars($product_info['product_name'], ENT_QUOTES),
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product_info['price'],
            ];
        }

        $_SESSION['purchase_items'] = $purchase_items;

        // カートの中身をクリア
        $_SESSION['cart'] = [];

        // 購入完了ページにリダイレクト
        header("Location: ./purchase_comp.php");
        exit;
    }
}

// 数量の変更の関数
function updateCartItemQuantity($dbh, &$cart_items, $product_id, $quantity) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if ($product_id !== false && $quantity !== false && $quantity > 0) {
        try {
            // トランザクションの開始
            $dbh->beginTransaction();
    
            // カートの商品情報を更新
            foreach ($cart_items as &$item) {
                if ($item['product_id'] === $product_id) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }
            // リファレンスを解除
            unset($item);           
            // カート情報をセッションに保存
            $_SESSION['cart'] = $cart_items;
            // データベースのカート情報も更新する
            $stmt = $dbh->prepare("UPDATE cart SET quantity = :quantity WHERE product_id = :product_id");
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();    
            // トランザクションのコミット
            $dbh->commit();
            // ページをリロードして数量変更後の状態を反映
            $_SESSION['message'] = '商品の数量が変更されました。';
            header('Location: ./cart.php');
            exit;
        } catch (PDOException $e) {
            $dbh->rollBack();
            error_log($e->getMessage());
            $_SESSION['error'] = 'データベースエラーが発生しました。';
            header('Location: ./cart.php');
            exit;
        }
    } else {
        $_SESSION['error'] = '無効な数量です。';
        header('Location: ./cart.php');
        exit;
    }
}

// カートの合計金額を計算する関数
function calculateTotalPrice($dbh, $cart_items) {
    $total_price = 0;
    foreach ($cart_items as $item) {
        $product_info = getProductInfo($dbh, $item['product_id']);
        $total_price += $product_info['price'] * $item['quantity'];
    }
    return $total_price;
}

function removeFromCart($dbh, $product_id) {
    $stmt = $dbh->prepare("DELETE FROM cart WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
}

function getProductInfo($dbh, $product_id) {
    $stmt = $dbh->prepare("SELECT * FROM product WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}