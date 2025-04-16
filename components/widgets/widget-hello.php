<?php
/**
 * 首页底部Hello
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if ( ! empty($this->options->indexBlock) && in_array('ShowHello', $this->options->indexBlock) && $this->is('index')): ?>
<?php $helloTxt = $this->options->helloText; ?>
<div class="hh-widget mb-3">
  <div class="hello-widget">
    <img class="lazy" data-original="<?php echo getRandImg(); ?>" alt="<?php $helloTxt?>" />
    <div class="hello-content d-flex flex-column justify-content-center align-items-center">
      <span class="hello-text"><?php echo $helloTxt; ?></span>
    </div>
    <div class="hello-btn-group">
      <a href="/harmonyhues.html" class="py-1 px-2 hello-btn-item" title="关于主题" target="_self">@主题</a>
      <a href="/about.html" class="py-1 px-2 hello-btn-item" title="关于博主" target="_self">@博主</a>
      <a href="/links.html" class="py-1 px-2 hello-btn-item" title="友情链接" target="_self">@朋友</a>
    </div>
  </div>
</div>
<?php endif; ?>