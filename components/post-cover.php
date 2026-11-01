<?php

/**
 * 文章页面封面图（沉浸式 hero）
 *
 * 说明：仅在文章有封面图且主题选项“文章页面封面图”开启时输出；
 * 面包屑、标题、作者/时间/浏览/评论都放在封面内部，
 * 此时正文卡片不再重复输出 .post-header。
 *
 * @author  星语社长
 * @link  https://biibii.cn
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}
if (! usePostCover($this)) {
  return;
}
$coverImage = getPostCoverImage($this);
?>
<!-- 文章封面（含标题与元信息） -->
<figure class="post-cover post-cover--hero card" view-image>
  <img class="post-cover-img" src="<?php echo htmlspecialchars($coverImage, ENT_QUOTES, 'UTF-8'); ?>"
    alt="<?php echo htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8'); ?>" fetchpriority="high" decoding="async"
    onload="this.classList.add('is-loaded');" onerror="this.closest('.post-cover').classList.add('is-broken');">
  <span class="post-cover-shade"></span>
  <div class="post-cover-top d-flex">
    <?php $this->need('components/breadcrumb.php'); ?>
    <span class="post-cover-zoom" role="button" tabindex="0" aria-label="查看大图"
      onclick="this.closest('.post-cover').querySelector('.post-cover-img').click();"
      onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
      <i class="iconfont icon-search"></i>查看大图</span>
  </div>
  <figcaption class="post-cover-body">
    <h1 class="post-cover-title"><?php $this->title() ?></h1>
    <div class="post-cover-meta d-flex">
      <a href="<?php $this->author->permalink(); ?>" rel="author" title="作者"><i
          class="iconfont icon-zuozhe"></i><?php $this->author(); ?></a>
      <time datetime="<?php $this->date('c'); ?>" title="发布时间"><i
          class="iconfont icon-shijian"></i><?php $this->date(); ?></time>
      <a title="浏览量" role="button"><i class="iconfont icon-liulanjilu"></i><?php PostViewCount($this); ?></a>
      <a href="<?php $this->permalink() ?>#comments" title="评论"><i
          class="iconfont icon-pinglun1"></i><?php $this->commentsNum('0', '1', '%d'); ?></a>
    </div>
  </figcaption>
</figure>