<?php

/**
 * 指定された値が整数かどうかを検証する関数
 *
 * @param mixed $value 検証する値。
 * @return bool 値が整数の場合はtrue、そうでない場合はfalseを返します。
 */
function checkInteger($value) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}


/**
 * ファイルの拡張子が許可されたものかどうかを検証する関数
 *
 * @param string $fileName 検証するファイル名。
 * @return bool 拡張子が許可されたものであればtrue、そうでなければfalseを返します。
 */
function extensionCheck($fileName) {
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    return in_array(strtolower($extension), $allowedExtensions);
}



/**
 * 公開フラグに応じた表示テキストを返す関数
 *
 * @param int $publicFlg 公開フラグ（1: 公開, 0: 非公開）
 * @return string 表示テキスト
 */
function checkPublic($publicFlg) {
    return $publicFlg == 1 ? "公開" : "非公開";
}


/**
 * 文字列を安全な形式にエスケープする
 *
 * @param string $string エスケープする文字列
 * @return string エスケープされた文字列
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

?>
