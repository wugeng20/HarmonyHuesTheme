<?php

/**
 * 我的设备
 * 
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-03-28 14:46:50
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

$categories = array();
$expectedKeys = array('type', 'model', 'image', 'specs', 'description'); // 必须保留的字段
preg_match_all('/\[Equipment\s+(.*?)\s*\/\]/s', $this->content, $matches, PREG_SET_ORDER);
foreach ($matches as $match) {
  // 解析属性键值对
  preg_match_all('/(\w+)\s*=\s*(["\'])((?:\\\2|(?!\2).)*?)\2/', $match[1], $attributes, PREG_SET_ORDER);
  $item = array();
  $category = '';
  // 提取属性并处理转义
  foreach ($attributes as $attr) {
    $key = strtolower($attr[1]);
    $value = htmlspecialchars_decode(stripslashes($attr[3]));
    if ($key === 'category') {
      $category = trim($value);
    } else {
      $item[$key] = $value;
    }
  }
  // 设置默认分类
  $category = !empty($category) ? $category : '好物分享';
  // 保证字段完整性
  foreach ($expectedKeys as $key) {
    if (!isset($item[$key])) {
      $item[$key] = '';
    }
  }
  // 过滤多余字段
  $item = array_intersect_key($item, array_flip($expectedKeys));
  // 组织数据
  if (!isset($categories[$category])) {
    $categories[$category] = array();
  }
  $categories[$category][] = $item;
}

$this->need('components/header.php');
?>
<style>
  .post-category-box {
    opacity: 0;
    animation: fade-in-top 0.5s 0.3s forwards;
    -webkit-animation: fade-in-top 0.5s 0.3s forwards;
  }

  .post-category-box .post-category-title {
    position: relative;
    display: inline-block;
    font-size: 1.5rem;
    color: var(--font-color-main);
    background-color: var(--bg-color-main);
  }

  .post-category-box .post-category-title::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    top: 8px;
    left: 8px;
    background-color: var(--font-color-main);
    z-index: -1;
  }

  .post-item .post-cover {
    background: var(--overlay-color-dark-1);
  }

  .post-item .post-cover .post-cover-box {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    border: 0;
    border-radius: inherit;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: 50% 50%;
    background-clip: padding-box;
  }

  .post-item .post-cover .post-cover-mask {
    --cover-mask-bg: #f8f8f8;
    position: absolute;
    bottom: -1px;
    width: 60%;
  }

  .post-item .post-cover .post-cover-title {
    position: absolute;
    line-height: 2;
    color: var(--font-color-main);
  }

  :root[data-theme=dark] {
    .post-item .post-cover .post-cover-mask {
      --cover-mask-bg: var(--bg-color-main);
    }
  }

  .post-item:hover .post-cover .post-cover-mask {
    --cover-mask-bg: var(--bg-color-main);
  }

  .post-meta-wrap .author-name {
    line-height: 1;
  }

  .text-overflow-base {
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    white-space: normal;
  }

  @media (max-width: 992px) {
    .post-category-box .post-category-title {
      font-size: 1rem;
    }

    .post-item .post-cover .post-cover-mask {
      bottom: -3px;
    }

    .post-item .post-cover .post-cover-title {
      font-size: 0.85rem;
    }
  }
</style>
<main class="equipment-main">
  <div class="container theme-main p-2">
    <!-- 装备封面 -->
    <div class="px-1 px-md-2 my-2">
      <div class="card category-box" style="animation-delay: 0.2s;">
        <img class="lazy"
          data-original="<?php echo $this->fields->thumb ?: getAssets('assets/images/pages/equipment.webp', false) ?>"
          alt="<?php $this->title() ?>" src="<?php getLazyload(); ?>" />
        <div class="category-item p-2">
          <div class="category-info d-flex flex-column justify-content-end" style="mix-blend-mode: overlay;">
            <span class="category-name"><?php $this->title() ?></span>
            <span class="category-desc"
              title="<?php echo $this->fields->abstract ?>"><?php echo $this->fields->abstract ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="post-main">
      <?php foreach ($categories as $category => $items): ?>
        <!-- 装备分类 -->
        <div class="post-category-box px-1 px-md-2 mt-4 mb-3" style="animation-delay: 0.3s;">
          <div class="post-category-title py-1 px-4"><b><?php echo $category ?></b></div>
        </div>
        <!-- 装备列表 -->
        <div class="row no-gutters post-list">
          <?php foreach ($items as $item): ?>
            <div class="col-6 col-md-4 col-xl-3 d-flex">
              <div class="post-item d-flex flex-column m-1 m-md-2 p-0">
                <div class="post-cover">
                  <div class="post-cover-box">
                    <img class="lazy p-2" src="<?php getLazyload(); ?>" data-original="<?php echo $item['image'] ?>"
                      alt="<?php echo $item['model'] ?>">
                    <div class="post-cover-mask">
                      <div class="post-cover-title pl-1 pl-md-2 text-overflow-base">
                        <?php echo $category . '&nbsp;•&nbsp;<span style="font-size:0.85rem;">' . $item['type'] . '</span>' ?>
                      </div>
                      <svg width="100%" height="auto" viewBox="0 0 307 52" fill="var(--cover-mask-bg)">
                        <path
                          d="M255.5 0L0 0L0 52L307 52C307 52 302.344 49.3312 299.5 47C296.656 44.6688 285.5 29.5 285.5 29.5L269.5 7.5C269.5 7.5 266.019 3.57708 263 2C259.981 0.422919 255.5 0 255.5 0Z">
                        </path>
                      </svg>
                    </div>
                  </div>
                </div>
                <div class="post-info d-flex flex-column align-content-center justify-content-between p-1 p-md-2">
                  <div class="post-head">
                    <div class="font-weight-bold text-overflow-base" title="<?php echo $item['model'] ?>">
                      <?php echo $item['model'] ?></div>
                    <div class="post-meta-wrap mb-1 mb-md-2">
                      <div class="author-name"><?php echo $item['specs'] ?></div>
                    </div>
                    <div class="post-description"><?php echo $item['description'] ?></div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="post-category-box px-1 px-md-2 my-4" style="animation-delay: 0.3s;">
      <div class="post-category-title py-1 px-4"><b>留言</b></div>
    </div>
    <?php $this->need('components/comments.php'); ?>
  </div>
</main>
<?php $this->need('components/footer.php'); ?>