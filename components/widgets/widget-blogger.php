<?php
/**
 * 博主信息
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if ( ! empty($this->options->sidebarBlock) && in_array('ShowAboutMe', $this->options->sidebarBlock)): ?>
<?php $stat = Typecho_Widget::widget('Widget_Stat');?>
<?php $isOnline = isAdminOnline(); // 是否在线?>
<!-- 博主信息 -->
<div class="hh-widget">
  <div class="author-content p-3 d-flex flex-column justify-content-center">
    <div class="author-info-avatar">
      <img class="avatar-img" src="<?php echo getGravatar($this->authorId ? $this->author->mail : $this->user->mail) ?>"
        alt="<?php $this->authorId ? $this->author->screenName() : $this->user->screenName();?>" />
      <span class="author-oneline<?php echo $isOnline ? '' : ' author-offline' ?>"
        title="博主今天<?php echo $isOnline ? '在线' : '离线' ?>"></span>
    </div>
    <div class="d-flex flex-column my-2">
      <span
        class="author-nickname text-center"><?php $this->authorId ? $this->author->screenName() : $this->user->screenName();?></span>
      <span class="author-text text-center"><?php $this->options->blogmeabout();?></span>
    </div>
    <div class="row blog-count-box no-gutters">
      <div class="col-3">
        <div class="blog-count-item d-flex flex-column align-items-center justify-content-center">
          <span>文章</span>
          <span><?php $stat->publishedPostsNum()?></span>
        </div>
      </div>
      <div class="col-3">
        <div class="blog-count-item d-flex flex-column align-items-center justify-content-center">
          <span>分类</span>
          <span><?php $stat->categoriesNum()?></span>
        </div>
      </div>
      <div class="col-3">
        <div class="blog-count-item d-flex flex-column align-items-center justify-content-center">
          <span>评论</span>
          <span><?php $stat->publishedCommentsNum()?></span>
        </div>
      </div>
      <div class="col-3">
        <div class="blog-count-item d-flex flex-column align-items-center justify-content-center">
          <span>标签</span>
          <span><?php $stat->TagsNum()?></span>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif;?>