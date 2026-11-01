<?php

/**
 * 热门文章
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2026-10-29 22:10:00
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}
?>
<?php if (! empty($this->options->sidebarBlock) && in_array('ShowHotPosts', $this->options->sidebarBlock)): ?>
  <?php
  if (! function_exists('getHotPostsCid')) {
    function getHotPostsCid($limit = 10)
    {
      try {
        $db = Typecho_Db::get(); // 获取数据库对象

        // views 字段不存在时不查询，避免非法字段报错
        if (! ensureViewsColumn()) {
          return array();
        }

        // 获取热门文章（只取列表需要的列，避免把正文一起读出来）
        $select = $db->select('cid', 'title', 'slug', 'created', 'type', 'views')
          ->from('table.contents')
          ->where('type = ?', 'post')
          ->where('status = ?', 'publish')
          ->order('views', Typecho_Db::SORT_DESC)
          ->limit($limit);

        $posts = $db->fetchAll($select);

        return $posts;
      } catch (Exception $e) {
        return array();
      }
    }
  }

  $hotPostsCid = getHotPostsCid(6);

  // 热度归一化：以当前列表最大浏览量为基准
  $hotMaxViews = 0;
  foreach ($hotPostsCid as $hotRow) {
    $hotMaxViews = max($hotMaxViews, (int) ($hotRow['views'] ?? 0));
  }
  ?>
  <!-- 热门文章 -->
  <div class="hh-widget mt-3">
    <div class="widget-title p-2 w-100">
      <div class="widget-title-top-bg" style="background:var(--hotposts-h-bg);"></div>
      <i class="iconfont icon-remen mr-1"></i>热门文章
    </div>
    <div class="widget-content hotposts-widget scroll-cover p-2">
      <?php if (! empty($hotPostsCid)): ?>
        <ul class="hotposts-list">
          <?php foreach ($hotPostsCid as $index => $hotPost): ?>
            <?php
            // 查询结果里已有 cid/标题/别名/时间/浏览量，直接拼链接，省掉每篇一次 Helper::widgetById()
            $postTitle = htmlspecialchars((string) ($hotPost['title'] ?? ''), ENT_QUOTES, 'UTF-8');
            $postUrl   = getPermalinkFromRow($hotPost);

            $postViews = (int) ($hotPost['views'] ?? 0);
            $viewText  = $postViews >= 1000 ? number_format($postViews / 1000, 1) . 'k' : (string) $postViews;
            $heat      = $hotMaxViews > 0 ? (int) round($postViews / $hotMaxViews * 100) : 0;
            $heat      = max(6, min(100, $heat));
            ?>
            <li class="hotposts-item">
              <a class="hotposts-link d-flex" href="<?php echo $postUrl; ?>" title="<?php echo $postTitle; ?>">
                <span class="hotposts-number"><?php echo $index + 1; ?></span>
                <span class="hotposts-title"><?php echo $postTitle; ?></span>
                <span class="hotposts-views" title="<?php echo $postViews; ?> 次浏览"><?php echo $viewText; ?></span>
              </a>
              <span class="hotposts-heat" style="--heat: <?php echo $heat; ?>%;" aria-hidden="true"></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <div class="text-center">正在统计数据中...</div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
