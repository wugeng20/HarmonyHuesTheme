<?php

/**
 * 热门文章
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if ( ! empty($this->options->sidebarBlock) && in_array('ShowHotPosts', $this->options->sidebarBlock)): ?>
<?php
function getHotPosts($limit = 10) {
    $db = Typecho_Db::get();
    $select = $db->select('cid', 'title', 'slug', 'type', 'status', 'created', 'commentsNum', 'views')
        ->from('table.contents')
        ->where('type = ?', 'post')
        ->where('status = ?', 'publish')
        ->order('views', Typecho_Db::SORT_DESC)
        ->limit($limit);

    $posts = $db->fetchAll($select);

    return $posts;
}

$hotPosts = getHotPosts(6);
$hotPosts = array_map(function ($post) {
    return Typecho_Widget::widget('Widget_Abstract_Contents')->push($post);
}, $hotPosts);
?>
<!-- 热门文章 -->
<div class="hh-widget mt-3 p-3">
  <div class="widget-title mb-3"><i class="iconfont icon-remen mr-1"></i>热门文章</div>
  <div class="widget-content hotposts-widget scroll-cover">
    <ul class="hotposts-list">
      <?php foreach ($hotPosts as $index => $post): ?>
      <?php $postTitle = htmlspecialchars($post['title']); ?>
      <li class="hotposts-item">
        <a class="hotposts-title" href="<?php echo $post['permalink']; ?>" title="<?php echo $postTitle; ?>">
          <span class="hotposts-number mr-1"><?php echo $index + 1; ?></span><?php echo $postTitle; ?>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
<?php endif; ?>