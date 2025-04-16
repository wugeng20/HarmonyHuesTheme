<?php
/**
 * 最新评论
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if ( ! empty($this->options->sidebarBlock) && in_array('ShowSidebarComments', $this->options->sidebarBlock) && ! $this->is('post')): ?>
<?php
function parseCommentContens($content) {
    $content = parseContens($content);
    $content = preg_replace('#<br>#si', '', $content); // 去除换行

    return $content;
}

?>
<!-- 最新评论 -->
<div class="hh-widget mt-3 p-3">
  <div class="widget-title mb-2">最新评论</div>
  <div class="widget-content newreply-widget scroll-cover">
    <!-- 评论项模板 -->
    <?php $this->widget('Widget_Comments_Recent', 'ignoreAuthor=true&pageSize=5')->to($item); ?>
    <?php if ($item->have()): ?>
    <?php while ($item->next()): ?>
    <div class="newreply-item mb-2">
      <div class="comment-info d-flex align-items-center">
        <div class="comment-avatar">
          <img class="avatar-img widget-lazy" src="<?php getLazyload(); ?>"
            data-original="<?php echo getGravatar($item->mail); ?>" alt="<?php $item->author(false)?>">
        </div>
        <div class="comment-name ml-2">
          <a href="<?php $item->url()?>"><?php $item->author(false)?></a>：
        </div>
      </div>
      <div class="comment-content mt-1 p-2">
        <a href="<?php echo $item->permalink; ?>"><?php echo parseCommentContens($item->content); ?></a>
      </div>
    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <div class="empty text-center">人气很差！一条回复都没有！</div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>