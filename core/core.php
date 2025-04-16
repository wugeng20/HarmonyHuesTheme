<?php
/* ------------------------------------
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php

/* 公用函数 */
require_once 'content-parse.php'; // 文章内容解析
require_once 'function.php'; // 主题函数
/* 主题函数 */
require_once 'comment-config.php'; // 主题评论拦截
require_once 'editor-config.php'; //后台文章编辑器按钮添加+文章自定义字段
require_once 'theme-config.php'; //主题设置
require_once 'theme-email.php'; // 邮件通知
/* 页面加载计时 */
startCountTime();

?>