<?php
/**
 * 通用（分类、搜索、标签、作者）页面
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php $this->need('components/header.php'); ?>
<main>
  <div class="container p-2">
    <div class="row no-gutters">
      <!-- 左侧主体 -->
      <div class="col-lg-9" id="main" role="main">
        <?php if ($this->have()): ?>
        <?php if ($this->is('category')): ?>
        <div class="px-1 px-md-2 my-2">
          <div class="card category-box">
            <img class="lazy" data-original="<?php echo getRandImg(); ?>" alt="文章分类">
            <div class="category-item p-2">
              <div class="category-info d-flex flex-column justify-content-end">
                <span
                  class="category-name"><?php echo $this->archiveTitle('', ''); ?>&bull;共<?php echo $this->getTotal(); ?>篇</span>
                <?php $categoryDesc = $this->getDescription(); ?>
                <span class="category-desc" title="<?php echo $categoryDesc; ?>"><?php echo $categoryDesc; ?></span>
              </div>
            </div>
          </div>
        </div>
        <?php else: ?>
        <p class="archive-title mb-2"><?php $this->archiveTitle(array(
    'category' => _t('分类 %s 下的文章'),
    'search' => _t('包含关键字 %s 的文章'),
    'tag' => _t('标签 %s 下的文章'),
    'author' => _t('%s 发布的文章'),
), '', ''); ?></p>
        <?php endif; ?>
        <?php $this->need('components/post-list.php'); ?>
        <?php else: ?>
        <div class="post card mt-2">
          <h4 class="post-title p-4"><?php _e('没有找到内容'); ?></h4>
        </div>
        <?php endif; ?>
      </div><!-- end #main -->
      <?php $this->need('components/sidebar.php'); ?>
    </div>
</main>

<?php $this->need('components/footer.php'); ?>