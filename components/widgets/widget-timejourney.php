<?php
/**
 * 首页底部时间之旅
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if ( ! empty($this->options->indexBlock) && in_array('ShowTimeJourney', $this->options->indexBlock) && $this->is('index')): ?>
<?php

// 获取建站日期，比如：2023-08-20
$siteCreationDate = new DateTime($this->options->sitedate);

/**
 * 计算日期进度（已过去天数、剩余天数、百分比）
 */
function calculateDateProgress(DateTime $startDate, DateTime $endDate, DateTime $currentDate): array {
    // 计算从开始日期到结束日期的总时间间隔
    $totalInterval = $startDate->diff($endDate);
    // 获取总天数
    $totalDays = $totalInterval->days;

    // 计算从开始日期到当前日期的时间间隔
    $passedInterval = $startDate->diff($currentDate);
    // 获取已过去的天数
    $passedDays = $passedInterval->days;

    // 计算剩余的天数
    $remainingDays = $totalDays - $passedDays;

    // 计算已过去的天数占总天数的百分比
    $passedPercentage = ($passedDays / $totalDays) * 100;
    // 计算剩余的天数占总天数的百分比
    $remainingPercentage = ($remainingDays / $totalDays) * 100;

    // 返回一个包含所有计算结果的数组
    return array(
        'total_days' => $totalDays, // 总天数
        'passed_days' => $passedDays, // 已过去的天数
        'remaining_days' => $remainingDays, // 剩余的天数
        'passed_percentage' => round($passedPercentage, 0), // 已过去的天数百分比，四舍五入到整数
        'remaining_percentage' => round($remainingPercentage, 0), // 剩余的天数百分比，四舍五入到整数
    );
}

/**
 * 替换字符串中的占位符
 */
function replacePlaceholders(string $description, float $remainingPercentage, float $passedPercentage): string {
    // 替换剩余百分比占位符
    $description = str_replace('{remainingPercentage}', $remainingPercentage, $description);
    // 替换已过去百分比占位符
    $description = str_replace('{progressPercentage}', $passedPercentage, $description);

    return $description;
}

/**
 * 处理输入字符串并返回处理后的结果
 */
function processEventString(string $input, DateTime $siteCreationDate) {
    // 分割字符串
    $parts = explode('|', $input);

    // 检查是否分割为4个部分
    if (count($parts) !== 4) {
        return false; // 输入格式不正确
    }

    // 提取并验证日期部分
    $eventDate = trim($parts[0]);
    if ( ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $eventDate)) {
        return false; // 日期格式不正确
    }

    // 将事件日期转换为 DateTime 对象
    $eventDate = new DateTime($eventDate);

    // 获取当前日期
    $currentDate = new DateTime();

    // 计算日期进度
    $progress = calculateDateProgress($siteCreationDate, $eventDate, $currentDate);

    // 替换描述中的占位符
    $description = $parts[3];
    $description = replacePlaceholders($description, $progress['remaining_percentage'], $progress['passed_percentage']);

    // 返回处理后的结果
    return array(
        'date' => $eventDate->format('Y-m-d'),
        'unit' => trim($parts[1]),
        'title' => trim($parts[2]),
        'description' => $description,
        'progress' => $progress,
    );
}

// 例如：2025-03-26|天|星际旅行|{remainingPercentage|progressPercentage}
$timeJourneyText = $this->options->timeJourneyText;
$processedEvent = processEventString($timeJourneyText, $siteCreationDate);

// 测试->输出处理后的结果
// echo '事件日期: '.$processedEvent['date']."\n";
// echo '单位: '.$processedEvent['unit']."\n";
// echo '标题: '.$processedEvent['title']."\n";
// echo '描述: '.$processedEvent['description']."\n";
// echo '总天数: '.$processedEvent['progress']['total_days']."\n";
// echo '已过去天数: '.$processedEvent['progress']['passed_days']."\n";
// echo '剩余天数: '.$processedEvent['progress']['remaining_days']."\n";
// echo '已过去百分比: '.$processedEvent['progress']['passed_percentage']."%\n";
// echo '剩余百分比: '.$processedEvent['progress']['remaining_percentage']."%\n";
?>
<div class="hh-widget mb-3">
  <?php if ($processedEvent): ?>
  <?php $passedPercentage = $processedEvent['progress']['passed_percentage']; ?>
  <div class="timejourney-widget">
    <div id="timejourney-progress" class="timejourney-progress d-flex align-items-center justify-content-end p-2"
      data-percentage="<?php echo $passedPercentage; ?>">
      <?php echo $passedPercentage.'%'; ?>
    </div>
    <div class="timejourney-content px-2 py-1">
      <div class="timejourney-text">
        <span class="timejourney-day"><?php echo $processedEvent['progress']['passed_days']; ?></span>
        <span class="timejourney-unit"><?php echo $processedEvent['unit']; ?></span>
      </div>
      <div class="timejourney-desc"><?php echo $processedEvent['title'].' | '; ?>
        <?php echo $processedEvent['description']; ?></div>
    </div>
  </div>
  <?php else: ?>
  <div class="text-center">时间之旅格式不正确或日期验证失败。请输入正确的格式，例如：2025-03-26|天|星际旅行|本站服务器燃料剩余{remainingPercentage}%</div>
  <?php endif; ?>
</div>
<?php endif; ?>