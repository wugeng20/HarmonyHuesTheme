<?php
/* ------------------------------------
 * 独立页面
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
?>
<?php if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

$isSidebar = $this->fields->showSidebar;
if ($isSidebar == '') {
    $isSidebar = true;
}
?>
<?php $this->need('components/header.php'); ?>

<!--主体st-->
<main>
  <div class="container p-2 <?php echo $isSidebar ? 'pl-lg-4 pl-xl-4' : 'px-lg-3 px-xl-3' ?>">
    <!-- 面包屑 -->
    <?php $this->need('components/breadcrumb.php'); ?>
    <div class="row no-gutters">
      <!-- 文章内容st -->
      <div class="<?php echo $isSidebar ? 'col-lg-9' : 'col-12' ?>">
        <?php $this->need('components/post-content.php'); ?>
      </div>
      <!-- 文章内容end -->
      <!--侧边栏st-->
      <?php if ($isSidebar): ?>
      <?php $this->need('components/sidebar.php'); ?>
      <?php endif; ?>
      <!--侧边栏end-->
    </div>
  </div>
</main>
<!--主体end-->
<?php $this->need('components/footer.php'); ?>