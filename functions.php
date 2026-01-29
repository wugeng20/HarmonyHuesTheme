<?php

/**
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 */
?>
<?php
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

require_once 'core/core.php';

/**
 * 如果您需要添加一些自定义的PHP代码
 * 您可以在当前目录下新建一个 func.php 的文件，写入你的php代码
 * 主题会自动判断文件进行引入
 * 使用此方式在线更新主题的时候，func.php文件的内容将不会被覆盖（手动更新仍然会覆盖）
 * 当然需要注意php的代码规范，错误代码将会引起网站严重错误！
 */
if (file_exists($options->themeFile('HarmonyHues', 'func.php')) || file_exists($options->themeFile('HarmonyHuesTheme', 'func.php'))) {
    require_once 'func.php';
}

/**
 * 您也可以将您的临时自定义php代码写在下方
 * 主题更新请自行备份您的自定义代码
 */

?>