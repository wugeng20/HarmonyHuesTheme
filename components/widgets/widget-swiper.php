<?php
/**
 * 轮播幻灯片
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-2-11 17:12:15
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if ( ! empty($this->options->indexBlock) && in_array('ShowSwiper', $this->options->indexBlock) && $this->is('index')): ?>
<?php
function slidesHtml($content) {
    // 预处理输入内容
    $cleanedContent = trim($content ?? '');
    if ($cleanedContent === '') {
        echo '幻灯片的数据为空或格式无效';
        return;
    }

    $slidesHtml = '';

    foreach (preg_split('/\r?\n|\r/', $cleanedContent) as $line) {
        // 忽略空行
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        // 分割并处理字段
        $parts = explode('|', $line);
        if (count($parts) !== 5) {
            continue;
        }

        // 字段处理管道
        $fields = array_map(function ($field) {
            $field = trim($field);
            return $field === 'NULL' ? '' : $field;
        }, $parts);

        $title = @htmlspecialchars($fields[0]);
        $description = @htmlspecialchars($fields[1]);
        $label = @htmlspecialchars($fields[2]);
        $image = @htmlspecialchars($fields[3] ?: getRandImg(false));
        $link = @htmlspecialchars($fields[4]);

        // 获取文件扩展名
        $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        // 生成HTML
        $slidesHtml .= '<div class="swiper-slide">';
        $slidesHtml .= '<a href="'.$link.'" target="_blank" title="'.($title ?: $description).' - 点击前往">';
        if ($extension === 'mp4') {
            $slidesHtml .= '<video class="swiper-slide-video" autoplay loop muted playsinline>';
            $slidesHtml .= '<source src="'.$image.'" type="video/mp4">';
            $slidesHtml .= '</video>';
        } else {
            $slidesHtml .= '<img class="swiper-lazy" src="'.getLazyload(false).'" data-src="'.$image.'" alt="'.($title ?: $description).'图片">';
        }

        $slidesHtml .= empty($label) ? '' : '<div class="px-2 py-1 swiper-slide-label">'.$label.'</div>';

        if ( ! empty($title) || ! empty($description)) {
            $slidesHtml .= '<div class="swiper-slide-content px-2 pb-2">';
            $slidesHtml .= empty($title) ? '' : '<div class="swiper-slide-title font-weight-bold" data-swiper-parallax-x="-200" data-swiper-parallax-duration="1200">'.$title.'</div>';
            $slidesHtml .= empty($description) ? '' : '<p class="swiper-slide-description m-0" data-swiper-parallax-x="-210" data-swiper-parallax-duration="1200">'.$description.'</p>';
            $slidesHtml .= '</div>';
        }
        $slidesHtml .= '</a></div>';
    }

// 检查生成的HTML是否为空
    if ($slidesHtml === '') {
        echo '幻灯片的数据为空或格式无效';
        return;
    }

    echo $slidesHtml;
}

$swiperText = $this->options->swiperText;
?>
<!-- 轮播幻灯片 -->
<div class="card w-auto m-1 m-md-2 swiper-widget">
  <div class="swiper-container">
    <div class="swiper-wrapper"><?php slidesHtml($swiperText)?></div>
    <div class="swiper-pagination d-flex align-items-center mx-1"></div>
    <div class="d-flex justify-content-center align-items-center m-2 swiper-button-wrapper">
      <div class="swiper-button-prev d-flex justify-content-center align-items-center" title="上一张"><i
          class="iconfont icon-shangyizhang"></i></div>
      <div class="swiper-button-next d-flex justify-content-center align-items-center" title="下一张"><i
          class="iconfont icon-xiayizhang"></i></div>
    </div>
  </div>
</div>
<?php endif; ?>