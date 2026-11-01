<?php

/**
 * 侧边栏
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

// 引入侧栏小部件并去掉首尾空白，避免模板里的缩进/空行变成多余节点
$hhSidebarWidget = function ($file) {
  ob_start();
  require __DIR__ . '/widgets/' . $file;
  $html = trim(ob_get_clean());
  // 去掉小部件开头的注释与空行，避免在侧栏里留下空白文本节点
  return preg_replace('/^(?:<!--[\s\S]*?-->\s*|\s+)+/', '', $html);
};

$hhSidebarOpen  = '<div class="main-sidebar col-lg-3 pt-2 pl-lg-3 pl-xl-3 pr-lg-2 pr-xl-2 d-none d-lg-block">';
$hhSidebarClose = '</div>';

echo $hhSidebarOpen;
if ($this->is('post') || $this->is('page')) {
  echo $hhSidebarWidget('widget-blogger.php');
  echo $hhSidebarWidget('widget-hotposts.php');
  echo $hhSidebarWidget('widget-tags.php');
  echo '<div class="sidebar-list sticky-top sidebar-sticky mt-3">';
  echo $hhSidebarWidget('widget-articletoc.php');
  echo '</div>';
} else {
  echo $hhSidebarWidget('widget-yiyan.php');
  echo $hhSidebarWidget('widget-blogsignage.php');
  echo $hhSidebarWidget('widget-devileyes.php');
  echo '<div class="sticky-top sidebar-sticky mt-3">';
  echo $hhSidebarWidget('widget-hotposts.php');
  echo $hhSidebarWidget('widget-newreply.php');
  echo $hhSidebarWidget('widget-tags.php');
  echo '</div>';
}
echo $hhSidebarClose;
