<?php

/**
 * 博客路牌
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-9-22 14:39:24
 */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if ( ! empty($this->options->sidebarBlock) && in_array('ShowBlogSignage', $this->options->sidebarBlock)): ?>
<!-- 博客路牌 -->
<div class="hh-widget mt-3">
  <div class="blogsignage-widget">
    <div class="blogsignage-head d-flex justify-content-center align-items-center font-weight-bold">
      <?php $this->options->blogSignageText(); ?></div>
    <div class="blogsignage-foot p-2 d-flex justify-content-between align-items-center">
      <div class="blogsignage-foot-west"><i class="iconfont icon-xiala"></i>西</div>
      <div class="blogsignage-foot-center"><?php $this->options->title(); ?>站</div>
      <div class="blogsignage-foot-east">东<i class="iconfont icon-xiala"></i></div>
    </div>
  </div>
</div>
<?php endif; ?>