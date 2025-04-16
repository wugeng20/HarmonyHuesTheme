<?php
/**
 * 文章列表
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php
/** 文章置顶 */
$sticky = $this->options->sticky;
if ($sticky && $this->is('index') || $this->is('front')) {
    $sticky_cids = explode(',', $sticky); //分割文本
    $cid_count = count($sticky_cids);
    $sticky_html = "<span style='color:red'>[置顶] </span>"; //置顶标题的 html
    $db = Typecho_Db::get(); // 获取数据库
    $pageSize = $this->parameter->pageSize - $cid_count; // 每页显示文章数
    $select1 = $this->select()->where('type = ?', 'post'); // 获取文章1
    $select2 = $this->select()->where('type = ? && status = ? && created < ?', 'post', 'publish', time()); // 获取文章2

    //清空原有文章的列队
    $this->row = array();
    $this->stack = array();
    $this->length = 0;

    $order = '';
    foreach ($sticky_cids as $i => $cid) {
        if ($i == 0) {
            $select1->where('cid = ?', $cid);
        } else {
            $select1->orWhere('cid = ?', $cid);
        }

        $order .= " when $cid then $i";
        $select2->where('table.contents.cid != ?', $cid); //避免重复
    }

    if ($order) {
        $select1->order('', "(case cid$order end)");
    }

    //置顶文章的顺序 按 $sticky 中 文章ID顺序
    if ($this->_currentPage == 1 || $this->currentPage == 1) {
        foreach ($db->fetchAll($select1) as $sticky_post) { //首页第一页才显示
            $sticky_post['sticky'] = $sticky_html;
            $this->push($sticky_post); //压入列队
        }
    }

    $uid = $this->user->uid; //登录时，显示用户各自的私密文章
    if ($uid) {
        $select2->orWhere('authorId = ? && status = ?', $uid, 'private');
    }

    $sticky_posts = $db->fetchAll($select2->order('table.contents.created', Typecho_Db::SORT_DESC)->page($this->_currentPage, $pageSize));
    foreach ($sticky_posts as $sticky_post) {
        $this->push($sticky_post);
    }

    //压入列队
    $this->setTotal($this->getTotal()); //置顶文章不计算在所有文章内
}?>
<div class="post-main">
  <div class="row no-gutters post-list">
    <?php while ($this->next()): ?>
    <div class="col-6 col-md-4 col-xl-4 d-flex">
      <div class="post-item d-flex flex-column m-1 m-md-2 p-0">
        <div class="post-cover">
          <a href="<?php $this->permalink()?>" title="<?php $this->title()?>">
            <img class="lazy" src="<?php getLazyload(); ?>" data-original="<?php echo getImgLink($this); ?>"
              alt="<?php $this->title()?>" />
          </a>
        </div>
        <div class="post-info d-flex flex-column align-content-center justify-content-between p-1 p-md-2">
          <div class="post-head">
            <a href="<?php $this->permalink()?>"
              title="<?php $this->title()?>"><?php $this->sticky()?><?php $this->title()?></a>
            <div class="post-description">
              <?php echo $this->fields->abstract ?: $this->excerpt(100, '...'); ?>
            </div>
          </div>
          <div class="post-meta-wrap d-flex justify-content-between">
            <div class="author-name"><?php $this->category('', true, ''); ?></div>
            <div class="post-meta">
              <time datetime="<?php $this->date('c'); ?>"
                class="post-meta-item"><?php echo ueTimeMini($this->date); ?></time>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
  <!-- 分页 -->
  <nav class="mt-4">
    <?php
ob_start();
$this->pageNav('<i class="iconfont icon-shangyiye"></i>', '<i class="iconfont icon-xiayiye"></i>', 1, '', array('wrapTag' => 'ul', 'wrapClass' => 'pagination justify-content-center', 'itemTag' => 'li', 'textTag' => 'span', 'currentClass' => 'active', 'prevClass' => 'prev', 'nextClass' => 'next'));
$content = ob_get_contents();
ob_end_clean();
$content = preg_replace("/<li><span>(.*?)<\/span><\/li>/sm", '', $content);
$content = preg_replace("/<li [class=\"active\"]+>(.*?)<\/li>/sm", '<li class="page-item active">$1</li>', $content);
$content = preg_replace("/<li [class=\"prev\"]+>(.*?)<\/li>/sm", '<li class="page-item prev" title="上一页">$1</li>', $content);
$content = preg_replace("/<li [class=\"next\"]+>(.*?)<\/li>/sm", '<li class="page-item next" title="下一页">$1</li>', $content);
$content = preg_replace("/<li>(.*?)<\/li>/sm", '<li class="page-item">$1</li>', $content);
$content = preg_replace("/<a href=\"(.*?)\">(.*?)<\/a>/sm", '<a class="page-link p-0 w-100 h-100 d-flex align-items-center justify-content-center" href="$1">$2</a>', $content);
echo $content;
?>
  </nav>
</div>