<?php
require_once '../include/utility/common_utilites.php';

safeSessionStart();

function getAllData($dbh, $table) {
    $stmt = $dbh->prepare("SELECT * FROM $table");
    $stmt->execute();
    return $stmt->fetchAll();
}

function registerProduct($dbh, $postData, $fileData) {
    if (isset($postData['product_name'], $postData['price'], $postData['quantity'], $fileData['product_image'], $postData['public_flag'])) {
        $productName = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $publicFlag = filter_input(INPUT_POST, 'public_flag', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        
        if (!$productName || !$price || $price <= 0 || !$quantity || $quantity < 0 || $publicFlag === null) {
            return false;
        } else {
            $allowedExtensions = array('jpg', 'jpeg', 'png');
            $productImage = $fileData['product_image']['name'];
            $fileExtension = strtolower(pathinfo($productImage, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                return false;
            } else {
                $imagePath = 'product_images/' . $productImage;
                if (!move_uploaded_file($fileData['product_image']['tmp_name'], $imagePath)) {
                    return false;
                } else {
                    try {
                        $dbh->beginTransaction();
                        $stmt = $dbh->prepare("INSERT INTO product (product_name, price, quantity, product_image, public_flag) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$productName, $price, $quantity, $imagePath, $publicFlag]);
                        $dbh->commit();
                        return true;
                    } catch (PDOException $e) {
                        $dbh->rollBack();
                        return false;
                    }
                }
            }
        }
    } else {
        return false;
    }
}

function updateProductQuantity($dbh, $productId, $quantity) {
    if (!$productId || !$quantity || $quantity < 0) {
        return false;
    }
    try {
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
        $stmt->execute([$quantity, $productId]);
        $dbh->commit();
        return true;
    } catch (PDOException $e) {
        $dbh->rollBack();
        return false;
    }
}

function updatePublicFlag($dbh, $productId, $publicFlag) {
    if (!$productId || $publicFlag === null) {
        return false;
    }
    try {
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE product SET public_flag = ? WHERE product_id = ?");
        $stmt->execute([$publicFlag, $productId]);
        $dbh->commit();
        return true;
    } catch (PDOException $e) {
        $dbh->rollBack();
        return false;
    }
}

function deleteProduct($dbh, $productId) {
    if (!$productId) {
        return false;
    }
    try {
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("DELETE FROM product WHERE product_id = ?");
        $stmt->execute([$productId]);
        $dbh->commit();
        return true;
    } catch (PDOException $e) {
        $dbh->rollBack();
        return false;
    }
}

// ログアウト処理
if (isset($_POST['logout'])) {
  logout();
}