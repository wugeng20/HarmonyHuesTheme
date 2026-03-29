<?php

/**
 * 文章目录
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

?>
<?php $tocData = generateToc($this->content);
if ($this->is('post') && $tocData): ?>
  <!-- 文章目录 -->
  <div class="hh-widget widget-toc-main mt-3">
    <div class="widget-title p-2 w-100">
      <div class="widget-title-top-bg" style="background:var(--atoc-h-bg);"></div>
      <i class="iconfont icon-mulu mr-1"></i>文章目录
    </div>
    <div class="widget-content atoc-content p-2">
      <?php echo $tocData; ?>
    </div>
  </div>
<?php endif; ?>