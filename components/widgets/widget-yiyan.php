<?php

/**
 * 一言
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2026-10-29 21:40:00
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if (! empty($this->options->sidebarBlock) && in_array('ShowSidebarYiyan', $this->options->sidebarBlock) && ! $this->is('post')): ?>
    <?php
    if (! function_exists('getYiyan')) {
        function getYiyan()
        {
            static $cache = null;

            if ($cache !== null) {
                return $cache;
            }

            // 获取文件路径，若获取失败则返回默认值
            $filePath = getAssets('assets/yiyan/yiyan.txt', false);
            if (! $filePath) {
                return $cache = array('一言文件不存在。', '——');
            }

            // 读取文件内容，忽略空行
            $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false || count($lines) === 0) {
                return $cache = array('一言为空或读取失败。', '——');
            }

            // 随机选择一行
            $randomLine = trim($lines[mt_rand(0, count($lines) - 1)]);
            // 分割句子与出处，确保返回数组始终包含两个元素
            $parts = explode('——', $randomLine, 2);
            $parts[0] = trim($parts[0]);
            $parts[1] = isset($parts[1]) ? trim($parts[1]) : '';

            if ($parts[1] === '') {
                $parts[1] = '佚名';
            }

            return $cache = $parts;
        }
    }

    if (! function_exists('getGreeting')) {
        function getGreeting()
        {
            // 获取当前时间的小时数（24 小时制）
            $hour = (int) date('H');

            // 根据时间判断问候语
            if ($hour >= 5 && $hour < 9) {
                return '早上好';
            } elseif ($hour >= 9 && $hour < 12) {
                return '上午好';
            } elseif ($hour >= 12 && $hour < 14) {
                return '中午好';
            } elseif ($hour >= 14 && $hour < 18) {
                return '下午好';
            } elseif ($hour >= 18 && $hour < 23) {
                return '晚上好';
            } else {
                return '夜深了，早点休息';
            }
        }
    }

    // 星期
    $weekNames = array('日', '一', '二', '三', '四', '五', '六');
    $weekText  = '星期' . $weekNames[(int) date('w')];

    // 随机一言图
    $randImg = getAssets('assets/yiyan/' . rand(1, 20) . '.webp', false);

    $yiyan     = getYiyan();
    $yiyanText = htmlspecialchars((string) $yiyan[0], ENT_QUOTES, 'UTF-8');
    $yiyanFrom = htmlspecialchars((string) $yiyan[1], ENT_QUOTES, 'UTF-8');
    ?>
    <!-- 一言 -->
    <div class="hh-widget mt-3">
        <div class="yiyan-widget">
            <div class="yiyan-date p-2 pb-0">
                <div class="yiyan-date-main d-flex">
                    <p class="yiyan-greet m-0"><?php echo getGreeting(); ?></p>
                    <p class="yiyan-dayline m-0 d-flex">
                        <span class="yiyan-day"><?php echo date('d'); ?></span>
                        <span class="yiyan-month">/<?php echo date('m'); ?></span>
                        <span class="yiyan-week"><?php echo $weekText; ?></span>
                    </p>
                </div>
                <button type="button" class="yiyan-refresh-btn" title="换一句" onclick="location.reload()">
                    <i class="iconfont icon-yuan"></i><span>换一句</span>
                </button>
            </div>
            <div class="yiyan-content d-flex flex-column justify-content-between">
                <div class="yiyan-cover">
                    <img data-original="<?php echo htmlspecialchars((string) $randImg, ENT_QUOTES, 'UTF-8'); ?>"
                        src="<?php getLazyload(); ?>" alt="一言壁纸" class="yiyan-img widget-lazy" />
                </div>
                <div class="yiyan-text p-2">
                    <p class="yiyan-quote m-0" title="<?php echo $yiyanText; ?>"><?php echo $yiyanText; ?></p>
                    <p class="yiyan-author text-right m-0" title="<?php echo $yiyanFrom; ?>">—— <?php echo $yiyanFrom; ?></p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
