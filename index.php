<?php

/**
 * <ul><li>php 支持：  7.2 ~ 8.x （说明：php 8.x 需要使用 typecho 1.2+ 版本）</li><li>详细说明：<a href="https://www.biibii.cn/" target="_blank">Harmony Hues 使用文档</a></li></ul>
 * @package Harmony Hues主题 - 源于自然界中的和谐之美，简洁的轻量化新Typecho主题。
 * @author 星语社长
 * @version 1.0.23
 * @link https://biibii.cn
 */

if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

$this->need('components/header.php');

?>
<main>
  <div class="container p-2">
    <div class="row no-gutters">
      <div class="col-lg-9">
        <?php $this->need('components/widgets/widget-swiper.php'); ?>
        <?php $this->need('components/post-list.php'); ?>
      </div>
      <?php $this->need('components/sidebar.php'); ?>
      <div class="index-footer-widget col-12 mt-3 px-1">
        <?php $this->need('components/widgets/widget-hello.php'); ?>
        <?php $this->need('components/widgets/widget-timejourney.php'); ?>
      </div>
    </div>
  </div>
</main>
<?php $this->need('components/footer.php'); ?>