<?php

/**
 * 搜索框
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-5-30 12:57:51
 */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<div class="main-search">
  <div class="site-search d-flex justify-content-center">
    <div class="pop-tool-box search-box card no-animation p-3">
      <div class="search-name mb-4">
        <h5>搜索</h5>
      </div>
      <div class="search-form">
        <form id="search" method="post" action="<?php $this->options->siteUrl(); ?>" role="search">
          <input type="text" id="s" name="s" class="form-control" placeholder="<?php _e('输入关键字搜索'); ?>" />
          <button type="submit" class="btn p-0 site-search-btn"><i class="iconfont icon-sousuo"></i></button>
        </form>
      </div>
      <div class="category-and-tags">
        <div class="post-tags d-flex flex-wrap mt-4">
          <?php $this->widget('Widget_Metas_Tag_Cloud', 'ignoreZeroCount=1&limit=15')->to($tags); ?>
          <?php if ($tags->have()): ?>
          <?php while ($tags->next()): ?>
          <a href="<?php $tags->permalink(); ?>" rel="tag" title="<?php $tags->count(); ?> 个话题"><?php $tags->name(); ?>
            (<?php $tags->count(); ?>)</a>
          <?php endwhile; ?>
          <?php else: ?>
          <div><?php _e('没有任何标签'); ?></div>
          <?php endif; ?>
        </div>
      </div>
      <div id="search-close-btn" class="close-btn"><i id="close-btn" class="iconfont icon-guanbi"></i></div>
    </div>
  </div>
  <div class="pop-tool-overlay-bg"></div>
</div>