<?php

/**
 * 首页布局
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2026-04-12 21:46:25
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}
?>
<?php if (isMobile() || $this->options->indexLayout): ?>
  <div class="col-lg-12 no-sidebar">
    <?php require_once 'widgets/widget-swiper.php'; ?>
    <?php require_once 'post-list.php'; ?>
  </div>
<?php else: ?>
  <div class="col-lg-9">
    <?php require_once 'widgets/widget-swiper.php'; ?>
    <?php require_once 'post-list.php'; ?>
  </div>
  <?php require_once 'sidebar.php'; ?>
<?php endif; ?>