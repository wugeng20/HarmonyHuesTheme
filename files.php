<?php

/**
 * 归档页面
 *
 * @author  星语社长
 * @link    https://biibii.cn
 * @update  2026-10-29 22:30:00
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

$this->need('components/header.php');

/* ---------- 数据：一次查询，聚合日/年/连续写作 ---------- */
$archiveDb   = Typecho_Db::get();
$archiveRows = $archiveDb->fetchAll(
  $archiveDb->select('cid', 'title', 'slug', 'created', 'type')
    ->from('table.contents')
    ->where('type = ?', 'post')
    ->where('status = ?', 'publish')
    ->where('created < ?', $this->options->time)
    ->order('created', Typecho_Db::SORT_DESC)
);

$nowTs    = (int) $this->options->time;
$todayMid = strtotime(date('Y-m-d', $nowTs));
$dow      = (int) date('N', $todayMid);            // 1=周一 … 7=周日
$heatCols = 53;
$heatDays = $heatCols * 7;
$heatStart = $todayMid - (($dow - 1) + ($heatCols - 1) * 7) * 86400; // 对齐到周一

$dayCounts  = array();
$yearCounts = array();

foreach ($archiveRows as $archiveRow) {
  $ts = (int) $archiveRow['created'];
  $yearCounts[date('Y', $ts)] = ($yearCounts[date('Y', $ts)] ?? 0) + 1;
  if ($ts >= $heatStart) {
    $key = date('Y-m-d', $ts);
    $dayCounts[$key] = ($dayCounts[$key] ?? 0) + 1;
  }
}

$heatMax = 0;
foreach ($dayCounts as $heatCount) {
  $heatMax = max($heatMax, $heatCount);
}

/* 连续写作：最长 / 当前 */
$longestStreak = 0;
$runStreak     = 0;
$currentStreak = 0;
for ($i = 0; $i < $heatDays; $i++) {
  $dayKey = date('Y-m-d', $heatStart + $i * 86400);
  if (! empty($dayCounts[$dayKey])) {
    $runStreak++;
    $longestStreak = max($longestStreak, $runStreak);
  } else {
    $runStreak = 0;
  }
}
for ($i = $heatDays - 1; $i >= 0; $i--) {
  $dayKey = date('Y-m-d', $heatStart + $i * 86400);
  $c = (int) ($dayCounts[$dayKey] ?? 0);
  if ($c > 0) {
    $currentStreak++;
  } else {
    break;
  }
}

$totalPosts = count($archiveRows);
?>
<style type="text/css">
  /* 归档页：仅此处新增样式，通用外观复用 style.scss 的变量与工具类 */
  .files-hero {
    overflow: hidden;
  }

  .files-page .files-hero .category-box {
    height: 11rem;
  }

  .files-page .files-hero .category-item {
    top: 0;
  }

  .files-page .files-stats {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 0.4rem;
    padding: 0.65rem 0.6rem 0.75rem;
  }

  .files-page .files-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.1rem;
    padding: 0.45rem 0.2rem;
    border-radius: 12px;
    background: var(--bg-color-primary);
    box-shadow: var(--shadow-inset-box);
  }

  .files-page .files-stat b {
    font-size: 1.3rem;
    font-weight: 700;
    line-height: 1.1;
    font-variant-numeric: tabular-nums;
    color: var(--font-color-main);
  }

  .files-page .files-stat span {
    font-size: 0.68rem;
    color: var(--font-color-main-light);
    white-space: nowrap;
  }

  .files-page .files-card {
    padding: 0.9rem 1rem 1rem;
  }

  .files-page .files-card-head {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.85rem;
  }

  .files-page .files-card-title {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 1rem;
    font-weight: 600;
  }

  .files-page .files-card-title i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.55rem;
    height: 1.55rem;
    border-radius: 8px;
    background: rgba(22, 93, 255, 0.12);
    color: var(--color-primary);
    font-size: 0.85rem;
    font-style: normal;
  }

  .files-page .files-card-note {
    margin-left: 0.2rem;
    font-size: 0.7rem;
    font-weight: 400;
    color: var(--font-color-main-light);
  }

  .files-page .files-legend {
    display: flex;
    align-items: center;
    gap: 0.28rem;
    font-size: 0.68rem;
    color: var(--font-color-main-light);
  }

  .files-page .files-heat-scroll {
    overflow-x: auto;
    padding-bottom: 0.35rem;
  }

  .files-page .files-heat-months {
    position: relative;
    width: max-content;
    height: 0.95rem;
    margin-bottom: 0.28rem;
    font-size: 0.6rem;
    color: var(--font-color-main-light);
  }

  .files-page .files-heat-months span {
    position: absolute;
    bottom: 0;
    white-space: nowrap;
  }

  .files-page .files-heat {
    display: grid;
    grid-auto-flow: column;
    grid-template-rows: repeat(7, 11px);
    grid-auto-columns: 11px;
    gap: 3px;
    width: max-content;
  }

  .files-page .heat-cell {
    width: 11px;
    height: 11px;
    border-radius: 3px;
    background: var(--bg-color-primary);
    box-shadow: inset 0 0 0 1px rgba(var(--shadow-tint), 0.05);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  .files-page .heat-cell[data-level="1"] {
    background: rgba(22, 93, 255, 0.18);
    box-shadow: none;
  }

  .files-page .heat-cell[data-level="2"] {
    background: rgba(22, 93, 255, 0.36);
    box-shadow: none;
  }

  .files-page .heat-cell[data-level="3"] {
    background: rgba(22, 93, 255, 0.58);
    box-shadow: none;
  }

  .files-page .heat-cell[data-level="4"] {
    background: var(--color-primary);
    box-shadow: none;
  }

  :root[data-theme="dark"] .files-page .heat-cell[data-level="0"] {
    background: rgba(255, 255, 255, 0.06);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.04);
  }

  :root[data-theme="dark"] .files-page .heat-cell[data-level="1"] {
    background: rgba(106, 161, 255, 0.22);
  }

  :root[data-theme="dark"] .files-page .heat-cell[data-level="2"] {
    background: rgba(106, 161, 255, 0.42);
  }

  :root[data-theme="dark"] .files-page .heat-cell[data-level="3"] {
    background: rgba(106, 161, 255, 0.66);
  }

  :root[data-theme="dark"] .files-page .heat-cell[data-level="4"] {
    background: #6aa1ff;
  }

  .files-page .heat-cell:hover {
    transform: scale(1.18);
    box-shadow: 0 0 0 1px var(--color-primary), 0 4px 10px rgba(22, 93, 255, 0.28);
  }

  .files-page .files-streaks {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.5rem;
    margin-top: 0.9rem;
    padding-top: 0.85rem;
    border-top: 1px dashed var(--border-color-main);
  }

  .files-page .files-streak {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    padding: 0.55rem 0.7rem;
    border-radius: 12px;
    background: var(--card-surface);
    box-shadow: var(--shadow-card);
  }

  .files-page .files-streak b {
    font-size: 1.05rem;
    font-variant-numeric: tabular-nums;
  }

  .files-page .files-streak b em {
    margin-left: 0.15rem;
    font-size: 0.66rem;
    font-style: normal;
    font-weight: 400;
    color: var(--font-color-main-light);
  }

  .files-page .files-streak span {
    font-size: 0.7rem;
    color: var(--font-color-main-light);
  }

  .files-page .files-year {
    position: relative;
    overflow: hidden;
    padding: 0.85rem 1rem 0.9rem;
    margin-bottom: 0.9rem;
  }

  .files-page .files-year-head {
    display: flex;
    align-items: baseline;
    gap: 0.6rem;
    margin-bottom: 0.55rem;
  }

  .files-page .files-year-num {
    font-size: 2.3rem;
    font-weight: 700;
    line-height: 1;
  }

  .files-page .files-year-count {
    font-size: 0.72rem;
    color: var(--font-color-main-light);
  }

  .files-page .files-list {
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .files-page .files-list a {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.4rem 0.55rem;
    border-radius: 10px;
    color: var(--font-color-main);
    text-decoration: none;
    transition: background-color 0.22s ease, transform 0.22s ease;
  }

  .files-page .files-list a:hover {
    background: var(--bg-color-primary);
    color: var(--color-primary-text);
    text-decoration: none;
    transform: translateX(4px);
  }

  .files-page .files-list time {
    flex: none;
    width: 2.5rem;
    font-size: 0.76rem;
    font-variant-numeric: tabular-nums;
    color: var(--font-color-main-light);
  }

  .files-page .files-list .files-title {
    min-width: 0;
    font-size: 0.9rem;
  }

  @media (max-width: 767.98px) {
    .files-page .files-stats {
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .files-page .files-streaks {
      gap: 0.35rem;
    }

    .files-page .files-streak {
      padding: 0.45rem 0.3rem;
    }

    .files-page .files-streak b {
      font-size: 0.95rem;
    }

    .files-page .files-streak span {
      font-size: 0.64rem;
    }

    .files-page .files-year-num {
      font-size: 1.9rem;
    }
  }
</style>

<?php $stat = Typecho_Widget::widget('Widget_Stat'); ?>
<main class="files-page">
  <div class="container p-0 p-md-1">
    <div class="row no-gutters">
      <div class="col-12 p-1">
        <div class="card files-hero">
          <div class="category-box">
            <img class="lazy"
              data-original="<?php echo $this->fields->thumb ?: getAssets('assets/images/pages/archives.webp', false) ?>"
              alt="<?php $this->title() ?>" src="<?php getLazyload(); ?>" />
            <div class="category-item p-2">
              <div class="category-info d-flex flex-column justify-content-end">
                <span class="category-name"><?php $this->title() ?></span>
                <span class="category-desc"
                  title="<?php echo htmlspecialchars($this->fields->abstract ?? '', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($this->fields->abstract ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
            </div>
          </div>
          <div class="files-stats">
            <div class="files-stat"><b><?php $stat->publishedPostsNum() ?></b><span>文章</span></div>
            <div class="files-stat"><b><?php $stat->categoriesNum() ?></b><span>分类</span></div>
            <div class="files-stat"><b><?php $stat->publishedPagesNum() ?></b><span>页面</span></div>
            <div class="files-stat"><b><?php $stat->publishedCommentsNum() ?></b><span>评论</span></div>
            <div class="files-stat"><b><?php $stat->TagsNum() ?></b><span>标签</span></div>
            <div class="files-stat"><b><?php echo getWebsiteAgeInDays($this->options->sitedate); ?></b><span>建站天数</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 p-1">
        <div class="card files-card my-2">
          <div class="files-card-head">
            <div class="files-card-title">
              <i>▦</i>发布热力图
              <span class="files-card-note">近 <?php echo $heatCols; ?> 周 · 共 <?php echo $totalPosts; ?> 篇</span>
            </div>
            <div class="files-legend">
              少
              <span class="heat-cell" data-level="0"></span>
              <span class="heat-cell" data-level="1"></span>
              <span class="heat-cell" data-level="2"></span>
              <span class="heat-cell" data-level="3"></span>
              <span class="heat-cell" data-level="4"></span>
              多
            </div>
          </div>

          <div class="files-heat-scroll">
            <div class="files-heat-months" aria-hidden="true">
              <?php
              $monthCursor = strtotime(date('Y-m-01', $heatStart));
              $heatEnd     = $heatStart + $heatCols * 7 * 86400;
              $lastCol     = -10;
              while ($monthCursor < $heatEnd) {
                $col = (int) floor(($monthCursor - $heatStart) / 604800);
                if ($col >= 0 && $col - $lastCol >= 2 && ($col + 3) < $heatCols) {
                  $lastCol = $col;
                  echo '<span style="left: ' . ($col * 14) . 'px;">' . (int) date('n', $monthCursor) . '月</span>';
                }
                $monthCursor = strtotime('+1 month', $monthCursor);
              }
              ?>
            </div>
            <div class="files-heat">
              <?php
              for ($col = 0; $col < $heatCols; $col++) {
                for ($row = 0; $row < 7; $row++) {
                  $cellTs  = $heatStart + ($col * 7 + $row) * 86400;
                  if ($cellTs > $todayMid) {
                    echo '<span class="heat-cell" data-level="0" style="opacity:.35;"></span>';
                    continue;
                  }
                  $cellKey = date('Y-m-d', $cellTs);
                  $cellNum = (int) ($dayCounts[$cellKey] ?? 0);
                  $level   = 0;
                  if ($cellNum > 0) {
                    $level = max(1, min(4, (int) ceil($cellNum / max(1, $heatMax) * 4)));
                  }
                  $cellTip = date('Y年n月j日', $cellTs) . ' · ' . $cellNum . ' 篇';
                  echo '<span class="heat-cell" data-level="' . $level . '" title="' . $cellTip . '"></span>';
                }
              }
              ?>
            </div>
          </div>

          <script>
            (function() {
              var scroller = document.querySelector('.files-heat-scroll');
              if (!scroller) return;
              var toEnd = function() {
                if (scroller.scrollWidth <= scroller.clientWidth + 4) return;
                var active = scroller.querySelectorAll('.heat-cell[data-level]:not([data-level="0"])');
                var last = active.length ? active[active.length - 1] : null;
                if (last) {
                  var target = last.offsetLeft - scroller.clientWidth * 0.62;
                  scroller.scrollLeft = Math.max(0, Math.min(target, scroller.scrollWidth));
                } else {
                  scroller.scrollLeft = scroller.scrollWidth;
                }
              };
              toEnd();
              window.addEventListener('resize', toEnd);
            })();
          </script>

          <div class="files-streaks">
            <div class="files-streak"><b><?php echo $currentStreak; ?><em>天</em></b><span>当前连续写作</span></div>
            <div class="files-streak"><b><?php echo $longestStreak; ?><em>天</em></b><span>近一年最长连续</span></div>
            <div class="files-streak"><b><?php echo count($dayCounts); ?><em>天</em></b><span>近一年有更新</span></div>
          </div>
        </div>
      </div>

      <div class="col-12 p-1">
        <?php
        $output = '';
        $year = 0;
        foreach ($archiveRows as $archiveRow) {
          $year_tmp = date('Y', (int) $archiveRow['created']);
          if ($year != $year_tmp) {
            if ($year != 0) {
              $output .= '</ul></div>';
            }
            $year = $year_tmp;
            $output .= '<div class="card files-year"><div class="files-year-head"><span class="files-year-num text-webkit-mask title-text-stroke">' . $year . '</span><span class="files-year-count">' . ($yearCounts[$year] ?? 0) . ' 篇</span></div><ul class="files-list">';
          }
          $output .= '<li><a href="' . htmlspecialchars(getPermalinkFromRow($archiveRow), ENT_QUOTES, 'UTF-8') . '">'
            . '<time class="text-ellipsis">' . date('m-d', (int) $archiveRow['created']) . '</time>'
            . '<span class="files-title text-ellipsis">' . htmlspecialchars((string) $archiveRow['title'], ENT_QUOTES, 'UTF-8') . '</span>'
            . '</a></li>';
        }
        $output .= '</ul></div>';
        echo $output;
        ?>
      </div>
    </div>
  </div>
</main>
<?php $this->need('components/footer.php'); ?>