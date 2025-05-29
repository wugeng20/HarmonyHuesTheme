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
<?php $this->need('components/header.php'); ?>
<style type="text/css">
.files-hero {
  overflow: hidden;
}

.files-hero .category-box {
  height: 10rem;
}

.files-hero .category-box>.category-item {
  top: 0;
}

.archives-item {
  position: relative;
}

.archives-year {
  position: absolute;
  top: -1.2rem;
  display: inline-block;
  font-weight: 700;
  font-size: 3.5rem;
  line-height: 1;
  transition: all 0.3s ease-in-out;
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
  display: flex;
  flex-direction: column;
  align-items: center;
}

.archives-list-box .a-title {
  font-size: 2rem;
}

.archives-list-box .a-num {
  font-size: 1.5rem;
  color: var(--font-color-main);
}
</style>

<!--主体st-->
<?php $stat = Typecho_Widget::widget('Widget_Stat'); ?>
<main>
  <div class="container p-2">
    <div class="row no-gutters">
      <div class="col-12 p-1">
        <div class="card files-hero">
          <div class="category-box">
            <img class="lazy"
              data-original="<?php echo $this->fields->thumb ?: getAssets('assets/images/pages/archives.webp', false) ?>"
              alt="<?php $this->title()?>" src="<?php getLazyload(); ?>" />
            <div class="category-item p-2">
              <div class="category-info d-flex flex-column justify-content-end">
                <span class="category-name"><?php $this->title()?></span>
                <span class="category-desc"
                  title="<?php echo $this->fields->abstract ?>"><?php echo $this->fields->abstract ?></span>
              </div>
            </div>
          </div>
          <div class="d-flex flex-row justify-content-around py-1">
            <div class="archives-list-box text-center text-md-left">
              <span class="title-text-stroke a-title">文章</span>
              <span class="a-num"><?php $stat->publishedPostsNum()?>篇</span>
            </div>
            <div class="archives-list-box text-center text-md-left">
              <span class="title-text-stroke a-title">分类</span>
              <span class="a-num"><?php $stat->categoriesNum()?>个</span>
            </div>
            <div class="archives-list-box text-center text-md-left">
              <span class="title-text-stroke a-title">评论</span>
              <span class="a-num"><?php $stat->publishedCommentsNum()?>条</span>
            </div>
            <div class="archives-list-box text-center text-md-left">
              <span class="title-text-stroke a-title">标签</span>
              <span class="a-num"><?php $stat->TagsNum()?>个</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12 my-2 p-1">
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
<?php $this->need('components/footer.php'); ?>