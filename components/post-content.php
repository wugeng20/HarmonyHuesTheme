<?php
/**
 * 文章内容
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-1-20 12:58:35
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<!-- 文章st -->
<div id="post" class="post card mt-2">
  <div class="card-body">
    <!-- 文章信息 -->
    <div class="post-header mb-4">
      <h1 class="h4 post-title font-weight-bold mb-4"><?php $this->title()?></h1>
      <?php if ( ! $this->is('page')): ?>
      <div class="meta d-flex align-items-center text-muted">
        <div class="author-left">
          <name class="d-inline-block mr-md-2"><i class="iconfont icon-zuozhe mr-1"></i><a
              href="<?php $this->author->permalink(); ?>" title="由<?php $this->author(); ?>发布"
              rel="author"><?php $this->author(); ?></a></name>
          <time datetime="<?php $this->date('c'); ?>" class="d-inline-block"><i
              class="iconfont icon-31shijian mr-1"></i><?php $this->date(); ?></time>
        </div>
        <div class="author-right ml-auto text-sm">
          <a title="浏览量" role="button"><i class="iconfont icon-liulanjilu mr-1"></i><?php PostViewCount($this); ?></a>
          <a class="mx-1" href="<?php $this->permalink()?>#comments" title="评论" role="button"><i
              class="iconfont icon-pinglun mr-1"></i><?php $this->commentsNum('0', '1', '%d'); ?></a>
        </div>
      </div>
      <?php endif; ?>
      <div class="border-theme bg-primary"></div>
    </div>
    <!-- 文章内容 -->
    <div class="post-content markdown-body" itemprop="articleBody" view-image>
      <?php $this->content(); ?>
    </div>
    <?php if ( ! $this->is('page')): ?>
    <div class="post-end d-flex align-items-center justify-content-center m-4">THE END</div>
    <!-- 文章标签+分类 -->
    <div class="category-and-tags d-flex flex-wrap align-items-center">
      <div class="post-category d-flex flex-wrap align-items-center mt-2">
        <span class="ct-title">分类：</span><?php $this->category('', true, ''); ?>
      </div>
      <div class="post-tags d-flex flex-wrap align-items-center mt-2">
        <?php if (count($this->tags) != 0): ?>
        <span class="ct-title">标签：</span><?php $this->tags('', true, ''); ?>
        <?php endif; ?>
      </div>
    </div>
    <!-- 分享+赞助 -->
    <div class="post-tools d-flex justify-content-center mt-4">
      <div class="post-tools-item d-flex flex-column align-items-center">
        <button class="btn" id="reward-btn"><i class="iconfont icon-zanshangma"></i></button>
        <span>赞赏</span>
      </div>
      <div class="post-tools-item d-flex flex-column align-items-center">
        <button class="btn" id="share-btn"><i class="iconfont icon-fenxiang1"></i></button>
        <span>分享</span>
      </div>
    </div>
    <!-- 版权声明 -->
    <div class="post-copyright mt-4 p-3">
      <div class="copyright-content">
        <div class="copyright-title">&#169版权申明</div>
        <p class="copyright-desc"><span class="ml-3"></span>- 本文由作者 <a href="<?php $this->author->permalink(); ?>"
            title="<?php $this->author(); ?>">@<?php $this->author(); ?></a>
          原创发布在<?php $this->options->title(); ?>站点。未经许可，禁止转载。</p>
      </div>
      <div class="copyright-svgname animated-signature svg-name">
        <?php $this->options->svgName(); ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
<!-- 文章en -->
<!-- 下一篇、上一篇 -->
<?php if ( ! $this->is('page')): ?>
<div class="post-next-prev d-flex justify-content-between my-4">
  <div class="post-next card p-2 p-md-3 text-left">
    <div class="next-prev-title text-webkit-mask font-weight-bold">上一篇</div>
    <?php $this->thePrev('%s', '没有了'); ?>
  </div>
  <div class="post-prev card p-2 p-md-3 text-right">
    <div class="next-prev-title text-webkit-mask font-weight-bold">下一篇</div>
    <?php $this->theNext('%s', '没有了'); ?>
  </div>
</div>
<?php endif; ?>
<!--文章评论-->
<?php $this->need('components/comments.php'); ?>