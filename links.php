<?php

/**
 * 友情链接
 * 
 * @author  星语社长
 * @link    https://biibii.cn
 * @update  2024-12-10 18:00:04
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

$this->need('components/header.php');

global $groupedLinks;
$groupedLinks = array();

/**
 * 解析 [Links ...] 短代码，分组存储到 $groupedLinks
 * @param string $content
 * @return string 处理后的内容
 */
function parseLinksShortcode($content)
{
  global $groupedLinks;
  // 匹配所有 [Links ... /] 短代码
  preg_match_all('/\[Links\s+([^\]]+)\s*\/\]/', $content, $matches, PREG_SET_ORDER);
  $defaults = array(
    'title' => '',
    'url' => '',
    'avatar' => '',
    'desc' => '',
    'group' => '网上邻居',
    'status' => '正常',
  );
  foreach ($matches as $match) {
    $link = array_merge(array(), $defaults);
    // 支持引号和未加引号的属性
    preg_match_all('/(\w+)=(["\'])(.*?)\2|(\w+)=([^\s]+)/', $match[1], $attrs, PREG_SET_ORDER);
    foreach ($attrs as $attr) {
      if (!empty($attr[1])) {
        $key = strtolower($attr[1]);
        $value = $attr[3];
      } else {
        $key = strtolower($attr[4]);
        $value = $attr[5];
      }
      if (array_key_exists($key, $defaults)) {
        $link[$key] = htmlspecialchars_decode($value);
      }
    }
    if (empty($link['group'])) {
      $link['group'] = '网上邻居';
    }
    $group = $link['group'];
    if (!isset($groupedLinks[$group])) {
      $groupedLinks[$group] = array();
    }
    $groupedLinks[$group][] = $link;
  }
  $content = preg_replace('/\[Links\s+[^\]]+\s*\/\]/', '', $content);
  $content = preg_replace('#<p></p>|<p><br><br></p>#si', '', $content);
  $content = preg_replace('#<p>(<br\s*?>\s*)+</p>#i', '', $content);
  return $content;
}

$content = parseLinksShortcode($this->content);
?>
<style type="text/css">
  .links-list .links-card {
    opacity: 0;
    animation: fade-in-top 0.5s 0.3s forwards;
    -webkit-animation: fade-in-top 0.5s 0.3s forwards;
  }

  .links-title {
    font-size: 5rem
  }

  .links-group .links-group-title {
    font-size: 2.5rem;
    opacity: .6;
    -webkit-mask: linear-gradient(var(--bg-color-main) 50%, transparent);
    mask: linear-gradient(var(--bg-color-main) 50%, transparent)
  }

  .links-list .links-card {
    position: relative;
    border: var(--border-solid-main);
    border-radius: var(--border-radius-base);
    background-image: var(--gradient-45deg);
    box-shadow: var(--shadow-box-main);
  }

  .links-list .links-card:hover {
    box-shadow: var(--shadow-box-hover);
  }

  .links-list .link-avatar {
    position: relative;
  }

  .links-list .link-logo {
    position: relative;
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    margin: 0 auto;
    border: var(--border-solid-main);
    transition: all .5s;
  }

  .links-list .link-status {
    position: absolute;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    right: 0.3rem;
    bottom: 0.3rem;
    background-color: var(--success);
    border: var(--border-solid-main);
  }

  .links-card>a {
    height: 100%;
    gap: .5rem
  }

  .links-card .iconfont {
    font-size: 1rem;
    position: absolute;
    top: 0;
    right: 1rem;
    opacity: .5;
    transition: all .5s ease-in-out
  }

  .links-list .links-card:hover .iconfont {
    opacity: 0.8;
    transform: translateX(10px);
  }

  .links-text-clamp {
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: var(--links-line-clamp);
    -webkit-box-orient: vertical;
    white-space: normal
  }

  .link-avatar {
    position: relative;
  }

  .links-card .link-title {
    --links-line-clamp: 1;
  }

  .links-card .link-desc {
    --links-line-clamp: 2;
    font-size: 14px;
    color: var(--font-color-main-transparent);
  }

  .links-checkboxs .links-checkbox {
    box-shadow: none !important
  }

  .links-svgname {
    position: absolute;
    right: 1rem;
    bottom: 2rem;
  }

  @media (max-width: 992px) {
    .links-title {
      font-size: 2rem
    }
  }
</style>
<!--主体st-->
<main>
  <div class="container p-2">
    <!-- 标题 -->
    <div class="links-head my-4 text-center">
      <div class="links-title font-weight-bold">
        <p class="text-shadow-style">
          <?php echo $this->authorId ? $this->author->screenName() : $this->user->screenName(); ?>のTA的朋友们</p>
      </div>
    </div>
    <!-- 友邻列表srt -->
    <?php foreach ($groupedLinks as $groupName => $links): ?>
      <?php $linksCount = count($links);
      if ($linksCount === 1 && isset($links[0]['status']) && $links[0]['status'] === '隐藏') continue; ?>
      <section class="links-group my-4">
        <h2 class="font-weight-bold links-group-title title-text-stroke mt-3"><?php echo $groupName; ?></h2>
        <div class="links-list row no-gutters">
          <?php foreach ($links as $link): ?>
            <?php if ($link['status'] !== '隐藏'): ?>
              <div class="col-6 col-lg-3 d-flex flex-column align-self-stretch">
                <div class="links-card flex-fill p-2 m-1">
                  <a class="d-flex flex-direction align-items-center" href="<?php echo $link['url']; ?>" target="_blank"
                    title="<?php echo $link['title']; ?>">
                    <div class="link-avatar">
                      <img src="<?php getLazyload(); ?>" data-original="<?php echo $link['avatar'] ?: getAvatarLazyload(); ?>"
                        alt="<?php echo $link['title']; ?>" class="link-logo lazy" decoding="async">
                      <span class="link-status"
                        style="background-color:<?php echo $link['status'] == '异常' ? 'var(--danger)' : 'var(--success)' ?>"
                        title="<?php echo $link['status']; ?>"></span>
                    </div>
                    <div class="link-info flex-grow-1">
                      <div class="link-title font-weight-bold links-text-clamp"><?php echo $link['title']; ?></div>
                      <div class="link-desc links-text-clamp"><?php echo $link['desc'] ?: $link['url']; ?></div>
                    </div>
                  </a><i class="iconfont icon-qianwang"></i>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
    <!-- 友邻列表end -->
    <!-- 文章内容st -->
    <div class="links-head my-4 text-center">
      <div class="links-title font-weight-bold">
        <p class="text-shadow-style">本站添加的友链要求</p>
      </div>
    </div>
    <div class="post card mt-2">
      <div class="card-body">
        <!-- 文章内容 -->
        <div class="post-content markdown-body" itemprop="articleBody"><?php echo $content; ?></div>
        <div class="links-svgname animated-signature"><?php $this->options->svgName(); ?></div>
        <h4 class="my-2" style="font-size: 1.2rem;">[必选]申请前请先确认您是否符合以下条件：</h4>
        <div class="links-checkboxs ml-2">
          <p>
            <label class="checkbox"><input type="checkbox" class="links-checkbox">&nbsp;我已添加：<b><a
                  href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a></b>博客的友情链接</label>
          </p>
          <p>
            <label class="checkbox"><input type="checkbox"
                class="links-checkbox">&nbsp;我的链接主体为<b>个人</b>，网站类型为<b>博客</b></label>
          </p>
          <p>
            <label class="checkbox"><input type="checkbox" class="links-checkbox">&nbsp;我的网站目前可在中国大陆正常访问。</label>
          </p>
          <p>
            <label class="checkbox"><input type="checkbox" class="links-checkbox">&nbsp;网站内容符合中国大陆相关法律法规。</label>
          </p>
          <p>
            <label class="checkbox"><input type="checkbox" class="links-checkbox">&nbsp;我的网站首屏加载时间不超过 1 分钟。</label>
          </p>
        </div>
      </div>
    </div>
    <!-- 文章en -->
    <!--文章评论-->
    <?php $this->need('components/comments.php'); ?>
    <!-- 文章内容en -->
  </div>
</main>
<script type="text/javascript">
  $(document).ready(function() {
    $("#comment-form").hide();
    const commentTextareaTemplate = `网站名称：\n网站地址：\n网站图标URL：\n网站描述：\n能看到友情链接的地址：`;

    $(".links-checkbox").change(function() {
      const allChecked = $(".links-checkbox:checked").length === $(".links-checkbox").length;
      if (allChecked) {
        $("#comment-form").show();
        $("#textarea").val(commentTextareaTemplate);
      } else {
        $("#comment-form").hide();
      }
    });

    $(".reply.comment-footer").click(function() {
      $("#comment-form").show();
      $('.comment-list #textarea').val('');
    });

    $("#cancel-comment-reply-link").click(function() {
      $("#comment-form").hide();
    });
  });
</script>
<!--主体end-->
<?php $this->need('components/footer.php'); ?>