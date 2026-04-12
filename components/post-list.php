<?php

/**
 * 文章列表
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}
?>
<?php
/**
 * 文章列表
 */
$hiddenCategoryIds = $this->options->hideCategory ?? ''; // 要隐藏的分类ID，逗号分隔

// 判断是否有置顶文章函数
function hasArticleTop()
{
  $db = Typecho_Db::get();
  $select = $db->fetchObject($db->select('COUNT(*) as count')
    ->from('table.fields')
    ->where('name = ? && str_value = 1', 'articleTop')->limit(1));

  return $select->count > 0; // 如果有置顶文章，返回true
}

// $isArticleTop = false; // 测试用
$isArticleTop = hasArticleTop();

// 仅在首页执行
if (($isArticleTop || $hiddenCategoryIds) && $this->is('index') || $this->is('front')) {
  // 获取数据库对象
  $db = Typecho_Db::get();
  // 每页显示文章数量
  $adjustedPageSize = $this->parameter->pageSize;
  // 获取文章数据
  $normalPostQuery = $db->select('table.contents.*')->from('table.contents')->where('table.contents.type = ? && table.contents.status = ? && table.contents.created < ?', 'post', 'publish', time());
  // 需要移除的文章Cid
  $hiddenPostCidArray = array();

  // 清空原有文章的列队
  $this->row = array();
  $this->stack = array();
  $this->length = 0;

  // 置顶的文章
  if (!empty($isArticleTop)) {
    // 拼接查询条件
    $normalPostQuery->join('table.fields', 'table.contents.cid = table.fields.cid AND table.fields.name = \'articleTop\'', Typecho_Db::LEFT_JOIN);
    $normalPostQuery->order('table.fields.str_value = 1', Typecho_Db::SORT_DESC); // 按文章类型排序
  }

  // 隐藏分类的文章
  if (!empty($hiddenCategoryIds)) {
    // 子查询获取隐藏分类下的所有文章ID
    $hiddenPostSubQuery = $db->fetchAll($db->select('cid')->from('table.relationships')->where("mid IN ($hiddenCategoryIds)"));
    // 提取所有 cid 值
    $hiddenPostCids = array_column($hiddenPostSubQuery, 'cid') ?? [];
    $hiddenPostCidArray = array_merge($hiddenPostCidArray, $hiddenPostCids); // 将隐藏分类的文章ID合并到数组中
  }

  // 所有要隐藏的文章DB
  if (!empty($hiddenPostCidArray)) {
    // 用逗号连接成字符串
    $hiddenCids = implode(',', $hiddenPostCidArray);
    $normalPostQuery->where("table.contents.cid NOT IN ($hiddenCids)");
  }

  // 登录时，显示用户各自的私密文章
  $userId = $this->user->uid;
  if ($userId) {
    $normalPostQuery->orWhere('authorId = ? && status = ?', $userId, 'private');
  }

  // 普通文章
  $normalPostQuery->order('table.contents.created', Typecho_Db::SORT_DESC); // 文章按创建时间降序排列
  $normalPosts = $db->fetchAll($normalPostQuery->page($this->_currentPage, $adjustedPageSize));
  foreach ($normalPosts as $stickyPost) {
    $this->push($stickyPost);
  }

  //压入列队
  $this->setTotal($this->getTotal() - count($hiddenPostCidArray)); //隐藏文章不计算在所有文章内
}

/** 过滤掉[GridImg] */
function filterGridImg($content)
{
  $content = preg_replace('/\[GridImg\s+columns="(\d+)"\s+gap="([^"]+)"\](.*?)\[\/GridImg\]/s', '', $content);
  return strip_tags($content); // 去除HTML标签
}
?>
<div class="post-main">
  <div class="row no-gutters post-list">
    <?php while ($this->next()): ?>
      <?php if ($this->fields->articleType == 'image'): ?>
        <div
          class="<?php echo $this->options->indexLayout ? 'col-6 col-md-3 col-xl-3' : 'col-6 col-md-4 col-xl-4'; ?> d-flex post-image-card">
          <div class="post-item d-flex flex-column m-1 m-md-2 p-0">
            <div class="post-info d-flex flex-column align-content-center justify-content-between p-1 px-md-2 pt-md-2">
              <div class="post-head">
                <a href="<?php $this->permalink() ?>"
                  title="<?php $this->title() ?>"><?php $this->sticky() ?><?php $this->title() ?></a>
                <div class="post-description">
                  <?php echo mb_substr($this->fields->abstract, 0, 15) ?: mb_substr(filterGridImg($this->content), 0, 15); ?>
                </div>
              </div>
              <div class="post-meta-wrap">
                <div class="author-name">
                  <?php $this->category('/'); ?></div>
                <div class="post-meta">
                  <time datetime="<?php $this->date('c'); ?>" class="post-meta-item"><?php $this->date('m-d'); ?></time>
                </div>
              </div>
            </div>
            <div class="post-cover">
              <a href="<?php $this->permalink() ?>" title="<?php $this->title() ?>">
                <?php $imgThumbs = getImgLink($this, 3, false); ?>
                <!-- 遍历缩略图 -->
                <?php foreach ($imgThumbs as $imgThumb): ?>
                  <div class="post-cover-image">
                    <img class="lazy" src="<?php getLazyload(); ?>" data-original="<?php echo $imgThumb; ?>"
                      alt="<?php $this->title() ?>" />
                  </div>
                <?php endforeach; ?>
              </a>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div
          class="<?php echo $this->options->indexLayout ? 'col-6 col-md-3 col-xl-3' : 'col-6 col-md-4 col-xl-4'; ?> d-flex">
          <div class="post-item d-flex flex-column m-1 m-md-2 p-0">
            <div class="post-cover">
              <a href="<?php $this->permalink() ?>" title="<?php $this->title() ?>">
                <img class="lazy" src="<?php getLazyload(); ?>" data-original="<?php echo getImgLink($this); ?>"
                  alt="<?php $this->title() ?>" />
              </a>
            </div>
            <div class="post-info d-flex flex-column align-content-center justify-content-between p-1 p-md-2">
              <div class="post-head">
                <a href="<?php $this->permalink() ?>"
                  title="<?php $this->title() ?>"><?php echo $this->fields->articleTop ? '<span class="mr-1 top-tag">置顶</span>' : '' ?><?php $this->title() ?></a>
                <div class="post-description">
                  <?php echo $this->fields->abstract ?: $this->excerpt(100, '...'); ?>
                </div>
              </div>
              <div class="post-meta-wrap d-flex justify-content-between">
                <div class="author-name"><?php $this->category('/'); ?></div>
                <div class="post-meta">
                  <time datetime="<?php $this->date('c'); ?>"
                    class="post-meta-item"><?php echo ueTimeMini($this->date); ?></time>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    <?php endwhile; ?>
  </div>
  <!-- 分页 -->
  <nav class="mt-4">
    <?php
    ob_start();
    $this->pageNav(
      '<i class="iconfont icon-shangyiye"></i>',
      '<i class="iconfont icon-xiayiye"></i>',
      1,
      '',
      array(
        'wrapTag' => 'ul',
        'wrapClass' => 'pagination justify-content-center',
        'itemTag' => 'li',
        'textTag' => 'span',
        'currentClass' => 'active',
        'prevClass' => 'prev',
        'nextClass' => 'next',
      )
    );
    $content = ob_get_contents();
    ob_end_clean();

    // 删除省略号等无内容项
    $content = preg_replace("/<li><span>(.*?)<\/span><\/li>/sm", '<li class="page-item"><span class="page-link p-0 w-100 h-100 d-flex align-items-center justify-content-center">...</span></li>', $content);

    // 规范化分页项类名
    $content = preg_replace(array(
      "/<li class=\"active\">(.*?)<\/li>/sm",
      "/<li class=\"prev\">(.*?)<\/li>/sm",
      "/<li class=\"next\">(.*?)<\/li>/sm",
      "/<li>(.*?)<\/li>/sm",
    ), array(
      '<li class="page-item active">$1</li>',
      '<li class="page-item prev">$1</li>',
      '<li class="page-item next">$1</li>',
      '<li class="page-item">$1</li>',
    ), $content);

    // 智能生成 title 的核心逻辑
    $content = preg_replace_callback(
      "/<a href=\"(.*?)\">(.*?)<\/a>/sm",
      function ($matches) {
        $href = $matches[1];
        $linkContent = $matches[2];

        // 判断链接类型
        if (strpos($linkContent, 'icon-shangyiye') !== false) {
          $title = '上一页';
        } elseif (strpos($linkContent, 'icon-xiayiye') !== false) {
          $title = '下一页';
        } else {
          // 从 URL 中提取页码（适配 /page/2/ 格式）
          preg_match('/\/page\/(\d+)\/$/', $href, $pageMatch);
          $page = $pageMatch[1] ?? 1;
          $title = "第{$page}页";
        }

        // 构建安全的 title 属性
        $safeTitle = htmlspecialchars($title, ENT_QUOTES);

        // 返回优化后的 HTML
        return sprintf(
          '<a class="page-link p-0 w-100 h-100 d-flex align-items-center justify-content-center" href="%s" title="%s">%s</a>',
          $href,
          $safeTitle,
          $linkContent
        );
      },
      $content
    );

    echo $content;
    ?>
  </nav>
</div>