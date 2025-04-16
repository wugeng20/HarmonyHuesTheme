<?php
/**
 * 一言
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if ( ! empty($this->options->sidebarBlock) && in_array('ShowSidebarYiyan', $this->options->sidebarBlock) && ! $this->is('post')): ?>
<?php
function getYiyan() {
    // 获取句子文件的绝对路径
    $filePath = getAssets('assets/yiyan/yiyan.txt', false);

    // 读取文件内容，忽略空行
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines) {
        // 随机选择一句
        $randomLine = $lines[array_rand($lines)];
        return explode('——', $randomLine);
    } else {
        return array('一言为空或读取失败。', '——');
    }
}

function getGreeting() {
    // 获取当前时间的小时数（24 小时制）
    $hour = date('H');

    // 根据时间判断问候语
    if ($hour >= 5 && $hour < 9) {
        return '早上好！';
    } elseif ($hour >= 9 && $hour < 12) {
        return '上午好！';
    } elseif ($hour >= 12 && $hour < 14) {
        return '中午好！';
    } elseif ($hour >= 14 && $hour < 18) {
        return '下午好！';
    } elseif ($hour >= 18 && $hour < 23) {
        return '晚上好！';
    } else {
        return '夜深了，早点休息哦！';
    }
}

// 随机一言图
$randImg = getAssets('assets/yiyan/'.rand(1, 20).'.webp', false);

?>
<!-- 一言 -->
<div class="hh-widget mt-3">
  <div class="yiyan-widget">
    <div class="yiyan-date p-2">
      <p class="m-0"><?php echo getGreeting(); ?></p>
      <p class="m-0"><span class="yiyan-day"><?php echo date('d'); ?></span>/<span
          class="yiyan-month"><?php echo date('m'); ?></span></p>
    </div>
    <div class="yiyan-refresh-btn" title="刷新一言"><i onclick="location.reload()" class="iconfont icon-yuan"></i></div>
    <div class="yiyan-content d-flex flex-column justify-content-between">
      <div class="yiyan-cover">
        <img data-original="<?php echo $randImg; ?>" src="<?php getLazyload(); ?>" alt="一言" class="yiyan-img lazy" />
      </div>
      <?php $yiyan = getYiyan(); ?>
      <div class="yiyan-text p-2">
        <p class="yiyan-quote m-0" title="<?php echo $yiyan[0]; ?>"><?php echo $yiyan[0]; ?></p>
        <p class="yiyan-author text-right m-0" title="<?php echo $yiyan[1]; ?>">——<?php echo $yiyan[1]; ?></p>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>