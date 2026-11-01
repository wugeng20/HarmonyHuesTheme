<?php

/**
 * 最新评论
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2026-10-29 21:30:00
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

if (empty($this->options->sidebarBlock) || ! in_array('ShowSidebarComments', $this->options->sidebarBlock) || $this->is('post')) {
  return;
}

if (! function_exists('parseCommentContens')) {
  /**
   * 解析评论正文：走主题短代码解析，并去掉多余换行
   */
  function parseCommentContens($content)
  {
    $content = parseContens($content);

    return preg_replace('#<br\s*/?>#si', ' ', (string) $content);
  }
}

$escape = function ($value) {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$this->widget('Widget_Comments_Recent', 'ignoreAuthor=true&pageSize=5')->to($item);

if (! $item->have()) {
  echo '<div class="hh-widget mt-3">
  <div class="widget-title p-2 w-100">
    <div class="widget-title-top-bg" style="background:var(--newreply-h-bg);"></div><i
      class="iconfont icon-pinglun1 mr-1"></i>最新评论
  </div>
  <div class="widget-content newreply-widget p-2">
    <div class="newreply-empty">还没有评论，来抢沙发吧。</div>
  </div>
</div>';

  return;
}

ob_start();
?>
<div class="hh-widget mt-3">
  <div class="widget-title p-2 w-100">
    <div class="widget-title-top-bg" style="background:var(--newreply-h-bg);"></div>
    <i class="iconfont icon-pinglun1 mr-1"></i>最新评论
  </div>
  <div class="widget-content newreply-widget scroll-cover p-2">
    <?php while ($item->next()): ?>
      <?php
      ob_start();
      $item->author(false);
      $authorName = trim(ob_get_clean());
      $authorName = $authorName !== '' ? $authorName : '匿名';

      $commentTime = function_exists('ueTime') ? ueTime($item->date) : $item->date->format('Y-m-d');
      $commentText = parseCommentContens($item->content);
      $commentUrl = (string) $item->permalink;
      $postTitle = trim((string) $item->title);
      ?>
      <div class="newreply-item">
        <div class="newreply-head d-flex">
          <img class="newreply-avatar widget-lazy" src="<?php getLazyload(); ?>"
            data-original="<?php echo $escape(getGravatar($item->mail)); ?>" alt="" width="32" height="32">
          <span class="newreply-author"><?php echo $escape($authorName); ?></span>
          <time class="newreply-time"
            datetime="<?php echo $escape($item->date->format('c')); ?>"><?php echo $escape($commentTime); ?></time>
        </div>
        <a class="newreply-text" href="<?php echo $escape($commentUrl); ?>"
          title="<?php echo $escape(($postTitle !== '' ? $postTitle . ' · ' : '') . $authorName); ?>">
          <?php echo $commentText; ?>
        </a>
      </div>
    <?php endwhile; ?>
  </div>
</div>
<?php
$html = trim(ob_get_clean());
echo preg_replace('/^(?:<!--[\s\S]*?-->\s*|\s+)+/', '', $html);
