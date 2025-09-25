<?php

/**
 * 单栏页面
 * 
 * @author  星语社长
 * @link    https://biibii.cn
 * @update  2025-5-25 17:57:11
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

$this->need('components/header.php');
?>

<!--主体st-->
<main>
  <div class="container p-2 px-lg-3 px-xl-3">
    <!-- 面包屑 -->
    <?php $this->need('components/breadcrumb.php'); ?>
    <div class="row no-gutters">
      <!-- 左侧主体 -->
      <div class="col-12">
        <?php $this->need('components/post-content.php'); ?>
      </div>
    </div>
  </div>
</main>
<!--主体end-->
<?php $this->need('components/footer.php'); ?>