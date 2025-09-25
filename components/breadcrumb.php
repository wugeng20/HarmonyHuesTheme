<?php

/**
 * 面包屑
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-1-20 13:02:19
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}
?>
<ul class="breadcrumb mb-0 p-0 pb-2">
  <li>
    <a href="<?php $this->options->siteUrl(); ?>" class="link" title="首页">首页</a>
  </li>
  <?php if (count($this->categories) > 0): ?>
    <li>
      <a class="link" href="<?php echo $this->categories[0]['permalink']; ?>"
        title="<?php echo $this->categories[0]['name']; ?>"><?php echo $this->categories[0]['name']; ?></a>
    </li>
  <?php endif; ?>
  <li><span>正文</span></li>
</ul>