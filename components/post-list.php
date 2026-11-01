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

// 判断是否有置顶文章函数（static 缓存 + function_exists 防止重复 include 时重定义）
if (! function_exists('hasArticleTop')) {
  function hasArticleTop()
  {
    static $hasTop = null;

    if ($hasTop !== null) {
      return $hasTop;
    }

    $db = Typecho_Db::get();
    $select = $db->fetchObject($db->select('COUNT(*) as count')
      ->from('table.fields')
      ->where('name = ? && str_value = 1', 'articleTop')->limit(1));

    $hasTop = ($select->count ?? 0) > 0; // 如果有置顶文章，返回true

    return $hasTop;
  }
}

// $isArticleTop = false; // 测试用
$isArticleTop = hasArticleTop();

// 仅在首页（含 front 归档首页）且启用了置顶或隐藏分类时执行
$hasCustomQueue = $isArticleTop || trim((string) $hiddenCategoryIds) !== '';

if ($hasCustomQueue && ($this->is('index') || $this->is('front'))) {
  // 获取数据库对象
  $db = Typecho_Db::get();
  // 每页显示文章数量
  $adjustedPageSize = $this->parameter->pageSize;
  // 需要移除的文章Cid
  $hiddenPostCidArray = array();

  // 隐藏分类ID（只保留数字，避免非法值拼进 SQL）
  $hiddenMids = array_filter(array_map('intval', explode(',', (string) $hiddenCategoryIds)));

  // 隐藏分类下的所有文章ID
  if (!empty($hiddenMids)) {
    $hiddenPostSubQuery = $db->fetchAll($db->select('cid')->from('table.relationships')->where('mid IN (' . implode(',', $hiddenMids) . ')'));
    $hiddenPostCidArray = array_column($hiddenPostSubQuery, 'cid') ?? [];
  }

  // 清空原有文章的列队
  $this->row = array();
  $this->stack = array();
  $this->length = 0;

  // 登录时，显示用户各自的私密文章
  $userId = $this->user->uid;

  // 统一构造查询，保证列表与总数口径一致
  $buildPostQuery = function ($select) use ($db, $isArticleTop, $hiddenPostCidArray, $userId) {
    $query = $db->select($select)->from('table.contents')
      ->where('table.contents.type = ? && table.contents.status = ? && table.contents.created < ?', 'post', 'publish', time());

    // 置顶的文章
    if (!empty($isArticleTop)) {
      $query->join('table.fields', 'table.contents.cid = table.fields.cid AND table.fields.name = \'articleTop\'', Typecho_Db::LEFT_JOIN);
    }

    // 隐藏分类的文章
    if (!empty($hiddenPostCidArray)) {
      $query->where('table.contents.cid NOT IN (' . implode(',', array_map('intval', $hiddenPostCidArray)) . ')');
    }

    if ($userId) {
      $query->orWhere('authorId = ? && status = ?', $userId, 'private');
    }

    return $query;
  };

  // 排序：置顶文章存在时优先置顶，其次按发布时间
  $orderSql = !empty($isArticleTop) ? 'IF(table.fields.str_value = \'1\', 0, 1) ,table.contents.created' : 'table.contents.created';

  // 分页
  $normalPosts = $db->fetchAll($buildPostQuery('table.contents.*')->order($orderSql, Typecho_Db::SORT_DESC)->page($this->_currentPage, $adjustedPageSize));
  foreach ($normalPosts as $stickyPost) {
    $this->push($stickyPost);
  }

  // 总数按同一条件统计，避免按隐藏分类全量 cid 扣减导致页数偏少
  $totalRow = $db->fetchObject($buildPostQuery(array('COUNT(*)' => 'num')));
  $this->setTotal((int) ($totalRow->num ?? 0));
}

/** 文章摘要-过滤短代码标签 */
function archiveDescriptionText($content, $length = 100)
{
  // 移除所有 短代码 标签标记（包括开始、结束、自闭合）
  $content = preg_replace('/\[\/?\w+(?:[^]]*?)\/?\]/u', '', $content);

  // 移除 HTML 标签
  $content = strip_tags($content);

  // 截取前 100 个字符（UTF-8 安全）
  return mb_substr($content, 0, $length, 'UTF-8');
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
                  <?php echo htmlspecialchars(mb_substr($this->fields->abstract ?: archiveDescriptionText($this->content), 0, 15, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>
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
                  <?php
                  $postDescription = trim($this->fields->abstract ?? $this->fields->description ?? '') !== '' ? ($this->fields->abstract ?? $this->fields->description) : archiveDescriptionText($this->content);
                  echo htmlspecialchars($postDescription, ENT_QUOTES, 'UTF-8');
                  ?>
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
        $safeTitle = htmlspecialchars($title ?? '', ENT_QUOTES);

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