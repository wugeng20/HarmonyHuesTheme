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
?>
<?php $this->need('components/header.php'); ?>

<!--主体st-->
<main>
  <div class="container p-2 pl-lg-4 pl-xl-4">
    <!-- 面包屑 -->
    <?php $this->need('components/breadcrumb.php'); ?>
    <div class="row no-gutters">
      <!-- 左侧主体 -->
      <div class="col-lg-9">
        <?php $this->need('components/post-content.php'); ?>
      </div>
      <!--右侧主体-->
      <!--侧边栏st-->
      <?php $this->need('components/sidebar.php'); ?>
      <!--侧边栏end-->
    </div>
  </div>
</main>
<!--主体end-->
<?php $this->need('components/footer.php'); ?>