<?php
/**
 * 归档页面
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-12-15 12:20:12
 * @package custom
 */
?>
<?php if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php $this->need('components/header.php');?>
<style type="text/css">
.archives-item {
  position: relative;
}

.archives-year {
  position: absolute;
  top: -1.2rem;
  display: inline-block;
  font-weight: 700;
  font-size: 5rem;
  line-height: 1;
  transition: all 0.3s ease-in-out;
}

.archives-list {
  font-size: 1.25rem;
  line-height: 1.75rem;
}

.archives-list:hover li {
  filter: blur(2px);
  width: 100%;
}

.archives-item:hover .archives-year {
  opacity: 0.5;
  -webkit-text-fill-color: var(--font-color-main);
  transform: translateY(-1rem);
}

.archives-list>li {
  cursor: pointer;
  opacity: 0.7;
  transition: all 0.3s ease-in-out;
}

.archives-list>li:hover {
  opacity: 0.9;
  transform: translateX(1rem);
  filter: blur(0);
}

.archives-list-box {
  position: relative;
  font-weight: 700;
}

.archives-list-box .a-title {
  font-size: 2.5rem;
}

.archives-list-box .a-num {
  font-size: 2rem;
  color: var(--font-color-main);
}
</style>

<!--主体st-->
<?php $stat = Typecho_Widget::widget('Widget_Stat');?>
<main>
  <div class="container p-2">
    <div class="row no-gutters">
      <div class="col-3 col-md-2">
        <div class="archives-list-box text-center text-md-left">
          <span class="title-text-stroke a-title">文章</span>
          <span class="a-num"><?php $stat->publishedPostsNum()?>篇</span>
        </div>
      </div>
      <div class="col-3 col-md-2">
        <div class="archives-list-box text-center text-md-left">
          <span class="title-text-stroke a-title">分类</span>
          <span class="a-num"><?php $stat->categoriesNum()?>个</span>
        </div>
      </div>
      <div class="col-3 col-md-2">
        <div class="archives-list-box text-center text-md-left">
          <span class="title-text-stroke a-title">评论</span>
          <span class="a-num"><?php $stat->publishedCommentsNum()?>条</span>
        </div>
      </div>
      <div class="col-3 col-md-2">
        <div class="archives-list-box text-center text-md-left">
          <span class="title-text-stroke a-title">标签</span>
          <span class="a-num"><?php $stat->TagsNum()?>个</span>
        </div>
      </div>
      <div class="col-md-12 my-2">
        <?php
Typecho_Widget::widget('Widget_Contents_Post_Recent', 'pageSize='.$stat->publishedPostsNum)->to($archives);

$year = 0;
$output = '<div class="archives-content">';

while ($archives->next()) {
    $year_tmp = date('Y', $archives->created);

    if ($year != $year_tmp) {
        if ($year != 0) {
            $output .= '</ul></div>';
        }
        $year = $year_tmp;
        $output .= '<div class="card my-4 p-4 archives-item"><span class="archives-year text-webkit-mask title-text-stroke">'.$year.'</span><ul class="archives-list m-0">';
    }

    $output .= '<li class="py-1"><article>'.date('m-d', $archives->created).' <a href="'.$archives->permalink.'">'.$archives->title.'</a></article></li>';
}

$output .= '</ul></div></div>';
echo $output;
?>
      </div>
    </div>
  </div>
</main>
<!--主体end-->
<?php $this->need('components/footer.php');?>