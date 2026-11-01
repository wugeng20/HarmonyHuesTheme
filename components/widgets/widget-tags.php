<?php

/**
 * 标签
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2026-10-29 22:10:00
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}
?>
<?php if (! empty($this->options->sidebarBlock) && in_array('ShowTags', $this->options->sidebarBlock)): ?>
  <?php
  $this->widget('Widget_Metas_Tag_Cloud', 'ignoreZeroCount=1&limit=10')->to($hhTagCloud);

  $hhTagRows = array();
  if ($hhTagCloud->have()) {
    while ($hhTagCloud->next()) {
      ob_start();
      $hhTagCloud->permalink();
      $hhTagUrl = ob_get_clean();

      ob_start();
      $hhTagCloud->name();
      $hhTagName = ob_get_clean();

      $hhTagRows[] = array(
        'name'  => (string) $hhTagName,
        'url'   => (string) $hhTagUrl,
        'count' => (int) $hhTagCloud->count,
      );
    }
  }

  $hhTagMax = 0;
  foreach ($hhTagRows as $hhTagRow) {
    $hhTagMax = max($hhTagMax, $hhTagRow['count']);
  }
  ?>
  <!-- 标签 -->
  <div class="hh-widget mt-3">
    <div class="widget-title p-2 w-100">
      <div class="widget-title-top-bg" style="background:var(--tags-h-bg);"></div>
      <i class="iconfont icon-biaoqian mr-1"></i>热门文章
    </div>
    <div class="widget-content tags-widget p-2">
      <?php if (! empty($hhTagRows)): ?>
        <div class="tags-cloud d-flex">
          <?php foreach ($hhTagRows as $hhIndex => $hhTag): ?>
            <?php
            $hhName   = htmlspecialchars($hhTag['name'], ENT_QUOTES, 'UTF-8');
            $hhUrl    = htmlspecialchars($hhTag['url'], ENT_QUOTES, 'UTF-8');
            ?>
            <a class="tag-chip<?php echo $hhIndex < 3 ? ' is-top' : ''; ?>" href="<?php echo $hhUrl; ?>"
              title="<?php echo $hhName; ?>（<?php echo $hhTag['count']; ?> 篇文章）">
              <span class="tag-chip-name"><?php echo $hhName; ?></span>
              <span class="tag-chip-count"><?php echo $hhTag['count']; ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="text-center">暂无标签</div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>