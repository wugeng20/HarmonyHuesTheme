<?php

/**
 * 文章内容页面
 *
 * @author  星语社长
 * @link    https://biibii.cn
 * @update  2024-7-6 18:00:04
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

$isSidebar = $this->fields->showSidebar;
$indexLayout = $this->options->indexLayout;
if ((empty($isSidebar) || $isSidebar !== false) && $isSidebar != '0' && $indexLayout != '1') {
  $isSidebar = true;
} else {
  $isSidebar = (bool) $isSidebar;
}

$isSidebar = $this->options->indexLayout ? false : $isSidebar;

$this->need('components/header.php');
?>

<!--主体st-->
<main>
  <div class="container py-2 px-0">
    <!-- 面包屑（启用封面时由封面内部输出） -->
    <?php if (! usePostCover($this)): ?>
      <?php $this->need('components/breadcrumb.php'); ?>
    <?php endif; ?>
    <div class="row no-gutters">
      <!-- 文章内容st -->
      <div class="px-1 px-md-2 my-2 <?php echo htmlspecialchars($isSidebar ? 'col-lg-9' : 'col-12'); ?>">
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