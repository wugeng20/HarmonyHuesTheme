<?php
/**
 * 侧边栏
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if ( ! isMobile()): ?>
<div class="main-sidebar col-lg-3 pt-2 pl-lg-3 pl-xl-3 pr-lg-2 pr-xl-2 d-none d-lg-block">
  <?php if ($this->is('post') || $this->is('page')): ?>
  <?php require_once 'widgets/widget-blogger.php'; ?>
  <?php require_once 'widgets/widget-hotposts.php'; ?>
  <div class="sidebar-list sticky-top sidebar-sticky mt-3">
    <?php require_once 'widgets/widget-articletoc.php'; ?>
  </div>
  <?php else: ?>
  <?php require_once 'widgets/widget-yiyan.php'; ?>
  <?php require_once 'widgets/widget-devileyes.php'; ?>
  <div class="sticky-top sidebar-sticky mt-3">
    <?php //require_once 'widgets/widget-blogger.php';?>
    <?php require_once 'widgets/widget-hotposts.php'; ?>
    <?php require_once 'widgets/widget-newreply.php'; ?>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>