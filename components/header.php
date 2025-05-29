<?php
/**
 * 顶部head主体内容
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<!DOCTYPE HTML>
<html lang="zh-CN" data-theme="<?php echo getThemeMode(); ?>">

<head>
  <meta charset="<?php $this->options->charset(); ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
  <meta name="renderer" content="webkit">
  <?php if ($this->options->isCSP): ?>
  <meta http-equiv="Content-Security-Policy" content="<?php $this->options->contentCSP(); ?>">
  <?php endif; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
  <?php if ($this->is('single')): ?>
  <meta name="keywords" content="<?php echo $this->fields->keywords ?: htmlspecialchars($this->_keywords); ?>" />
  <meta name="description"
    content="<?php echo $this->fields->description ?: htmlspecialchars($this->_description); ?>" />
  <?php $this->header('keywords=&description='); ?>
  <?php else: ?>
  <?php $this->header(); ?>
  <?php endif; ?>
  <?php if ($this->options->favicon): ?>
  <link rel="shortcut icon" href="<?php $this->options->favicon(); ?>">
  <?php else: ?>
  <link rel="shortcut icon" href="<?php getAssets('assets/images/favicon.webp'); ?>">
  <?php endif; ?>
  <title><?php if ($this->_currentPage > 1) {
    echo '第 '.$this->_currentPage.' 页 - ';
}
?><?php $this->archiveTitle(array(
    'category' => _t('分类 %s 下的文章'),
    'search' => _t('包含关键字 %s 的文章'),
    'tag' => _t('标签 %s 下的文章'),
    'author' => _t('%s 发布的文章'),
), '', ' - '); ?><?php $this->options->title(); ?><?php if ($this->is('index')): ?> -
    <?php $this->options->description(); ?><?php endif; ?></title>
  <!-- IE Out -->
  <script>
  if (false || (!!window.MSInputMethodContext && !!document.documentMode))
    window.location.href =
    'https://support.dmeng.net/upgrade-your-browser.html?referrer=' +
    encodeURIComponent(window.location.href);
  </script>
  <!-- bootstrap -->
  <link rel="stylesheet" href="<?php getAssets('assets/lib/bootstrap4/bootstrap.min.css'); ?>" type="text/css"
    media="all">
  <!-- iconfont -->
  <link rel="stylesheet" href="<?php getAssets('assets/lib/iconfont/iconfont.min.css?v='.getVersion()); ?>"
    type="text/css" media="all">
  <link rel="stylesheet" href="<?php $this->options->iconfont()?>" type="text/css" media="all">
  <!-- 自定义css -->
  <link rel="stylesheet" href="<?php getAssets('assets/css/style.min.css?v='.getVersion()); ?>" type="text/css"
    media="all">
  <link rel="stylesheet" href="<?php getAssets('assets/css/widgets.min.css?v='.getVersion()); ?>" type="text/css"
    media="all">
  <?php if ($this->is('post') || $this->is('page')): ?>
  <!-- Md样式 -->
  <link rel="stylesheet" href="<?php getAssets('assets/css/markdow-style.min.css'); ?>" type="text/css" media="all">
  <!--代码高亮-->
  <link rel="stylesheet" href="<?php getAssets('assets/lib/prism/prism-one-dark.min.css'); ?>" type="text/css"
    media="all">
  <?php endif; ?>
  <!-- jQuery -->
  <script type="text/javascript" src="<?php getAssets('assets/lib/jquery/jquery.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php getAssets('assets/lib/jquery/jquery.lazyload.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php getAssets('assets/js/svg-icon.min.js?v='.getVersion()); ?>"></script>
  <!-- 轮播图js+css -->
  <?php if ( ! empty($this->options->indexBlock) && in_array('ShowSwiper', $this->options->indexBlock) && $this->is('index')): ?>
  <link rel="stylesheet" href="<?php getAssets('assets/lib/swiper3/swiper-3.4.2.min.css'); ?>" type="text/css"
    media="all">
  <script type="text/javascript" src="<?php getAssets('assets/lib/swiper3/swiper-3.4.2.jquery.min.js'); ?>"></script>
  <?php endif; ?>
  <!-- 自定义style -->
  <style>
  <?php $this->options->customStyle();
?>
  </style>
  <?php $this->options->customHeadEnd(); ?>
</head>

<body>
  <?php $this->need('components/navbar.php'); ?>
  <div class="nav-fixed"></div>