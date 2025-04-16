<?php
/**
 * 关于主题
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-12-15 14:46:50
 * @package custom
 */
?>
<?php
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

$itemsArray = array(
    array('title' => '主题名称', 'content' => $this->title),
    array('title' => '主题作者', 'content' => '星语社长'),
    array('title' => '主题版本', 'content' => getVersion()),
    array('title' => '博客程序', 'content' => 'TYPECHO'),
    array('title' => '技术栈', 'content' => 'PHP、HTML、CSS、JS'),
    array('title' => '是否开放', 'content' => '完善中，暂不开放'),
);
?>
<?php $this->need('components/header.php'); ?>
<style type="text/css">
.theme-main .theme-cover {
  overflow: hidden;
}

.theme-main .theme-text-title {
  font-size: 1.5rem;
}

.theme-main .theme-text-content {
  font-size: 1.25rem;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  white-space: normal;
}
</style>
<!--主体st-->
<main>
  <div class="container theme-main p-2">
    <div class="row no-gutters">
      <div class="col-lg-12">
        <div class="p-1">
          <div class="card theme-cover">
            <img src="<?php echo getImgLink($this); ?>" class="img-fluid" alt="<?php $this->title()?>">
          </div>
        </div>
      </div>
      <?php foreach ($itemsArray as $item): ?>
      <div class="col-6 col-lg-4">
        <div class="theme-item p-1">
          <div class="d-flex flex-column align-items-center card py-3">
            <span class="theme-text-title text-shadow-style font-weight-bold"><?php echo $item['title']; ?></span>
            <span class="theme-text-content"><?php echo $item['content']; ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <div class="col-lg-12">
        <!-- 文章内容st -->
        <!-- 文章st -->
        <div class="p-1 mt-2">
          <div class="post card card-body">
            <!-- 文章内容 -->
            <div class="post-content markdown-body" itemprop="articleBody"><?php $this->content(); ?></div>
            <!-- 版权声明 -->
            <div class="post-copyright mt-4 p-3">
              <div class="copyright-content">
                <div class="copyright-title">&#169版权申明</div>
                <p class="copyright-desc"><span class="ml-3"></span>- 本文由作者 <a
                    href="<?php $this->author->permalink(); ?>"
                    title="<?php $this->author(); ?>">@<?php $this->author(); ?></a>
                  原创发布在<?php $this->options->title(); ?>站点。未经许可，禁止转载。</p>
              </div>
              <div class="copyright-svgname">
                <?php $this->options->svgName(); ?>
              </div>
            </div>
          </div>
        </div>
        <!-- 文章en -->
        <!--文章评论-->
        <?php $this->need('components/comments.php'); ?>
        <!-- 文章内容en -->
      </div>
    </div>
  </div>
</main>
<!--主体end-->
<?php $this->need('components/footer.php'); ?>