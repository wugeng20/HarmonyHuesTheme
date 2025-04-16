<?php
/**
 * 文章目录
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php $tocData = getCatalog();if ($this->is('post') && $tocData): ?>
<!-- 文章目录 -->
<div class="hh-widget widget-toc-main mt-3 p-3">
  <div class="widget-title mb-2">文章目录</div>
  <div class="atoc-content">
    <?php echo $tocData; ?>
  </div>
</div>
<?php endif;?>