<?php

/**
 * 博主信息
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

// 未启用「博主信息」时直接返回，避免在容器里留下空白节点
if (empty($this->options->sidebarBlock) || ! in_array('ShowAboutMe', $this->options->sidebarBlock)) {
  return;
}

$stat     = Typecho_Widget::widget('Widget_Stat');
$isOnline = isAdminOnline(); // 是否在线

// 博主主页 / 关于我 链接（留空则不生成链接）
ob_start();
$this->authorId ? $this->author->screenName() : $this->user->screenName();
$authorName = trim(ob_get_clean());

$bloggerLink = trim((string) $this->options->bloggerLink);
if ($bloggerLink !== '' && ! preg_match('#^(?:https?:)?//#i', $bloggerLink)) {
  $bloggerLink = getSitePath(ltrim($bloggerLink, '/'));
}
$bloggerHref    = $bloggerLink !== '' ? ' href="' . htmlspecialchars($bloggerLink, ENT_QUOTES, 'UTF-8') . '"' : '';
$authorNameAttr = htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8');
?><div class="hh-widget">
  <div class="author-content p-3 d-flex flex-column justify-content-center">
    <div class="author-info-avatar<?php echo $bloggerHref !== '' ? ' is-link' : ''; ?>">
      <?php if ($bloggerHref !== ''): ?><a class="author-link"<?php echo $bloggerHref; ?> title="查看博主主页"><?php endif; ?>
      <img class="avatar-img widget-lazy" src="<?php getLazyload(); ?>"
        data-original="<?php echo getGravatar($this->authorId ? $this->author->mail : $this->user->mail) ?>"
        alt="<?php echo $authorNameAttr; ?>" />
      <?php if ($bloggerHref !== ''): ?></a><?php endif; ?>
      <span class="author-oneline<?php echo $isOnline ? '' : ' author-offline' ?>"
        title="博主今天<?php echo $isOnline ? '在线' : '离线' ?>"></span>
    </div>
    <div class="d-flex flex-column my-2">
      <span class="author-nickname text-center"><?php if ($bloggerHref !== ''): ?><a class="author-link"<?php echo $bloggerHref; ?> title="查看博主主页"><?php echo $authorNameAttr; ?></a><?php else: ?><?php echo $authorNameAttr; ?><?php endif; ?></span>
      <span class="author-text text-center"><?php $this->options->blogmeabout(); ?></span>
    </div>
    <div class="row blog-count-box no-gutters">
      <div class="col-3">
        <div class="blog-count-item d-flex flex-column align-items-center justify-content-center">
          <span>文章</span>
          <span><?php $stat->publishedPostsNum() ?></span>
        </div>
      </div>
      <div class="col-3">
        <div class="blog-count-item d-flex flex-column align-items-center justify-content-center">
          <span>分类</span>
          <span><?php $stat->categoriesNum() ?></span>
        </div>
      </div>
      <div class="col-3">
        <div class="blog-count-item d-flex flex-column align-items-center justify-content-center">
          <span>评论</span>
          <span><?php $stat->publishedCommentsNum() ?></span>
        </div>
      </div>
      <div class="col-3">
        <div class="blog-count-item d-flex flex-column align-items-center justify-content-center">
          <span>标签</span>
          <span><?php $stat->TagsNum() ?></span>
        </div>
      </div>
    </div>
  </div>
</div><?php
