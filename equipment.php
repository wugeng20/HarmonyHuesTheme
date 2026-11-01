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

  /* ===== 装备页：hero / 导航 / 卡片 ===== */
  .eq-hero {
    display: grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr);
    overflow: hidden;
    padding: 0;
  }

  .eq-hero-media {
    position: relative;
    min-height: 17rem;
    overflow: hidden;
  }

  .eq-hero-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .eq-hero-media::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0, 0, 0, .12), rgba(0, 0, 0, .05) 45%, rgba(0, 0, 0, .42));
  }

  .eq-hero-count {
    position: absolute;
    right: .9rem;
    bottom: .85rem;
    z-index: 2;
    padding: .3rem .7rem;
    color: #fff;
    background: rgba(0, 0, 0, .42);
    border: 1px solid rgba(255, 255, 255, .28);
    border-radius: 999px;
    font-size: .72rem;
    letter-spacing: .08em;
    backdrop-filter: blur(6px);
  }

  .eq-hero-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: .75rem;
    padding: 1.9rem 2rem;
    background: var(--card-surface);
  }

  .eq-eyebrow {
    margin: 0;
    color: var(--color-primary);
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .22em;
    text-transform: uppercase;
  }

  .eq-title {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 800;
    letter-spacing: .04em;
  }

  .eq-lead {
    margin: 0;
    max-width: 26em;
    color: var(--font-color-main-light);
    font-size: .9rem;
    line-height: 1.8;
  }

  .eq-stats {
    display: flex;
    flex-wrap: wrap;
    gap: .45rem;
    margin-top: .15rem;
  }

  .eq-chip {
    display: inline-flex;
    align-items: baseline;
    gap: .28rem;
    padding: .3rem .68rem;
    color: var(--font-color-main);
    background: var(--bg-color-primary);
    border: 1px solid var(--border-color-main);
    border-radius: 999px;
    font-size: .74rem;
    box-shadow: var(--shadow-inset-box);
  }

  .eq-chip b {
    color: var(--color-primary);
    font-size: .92rem;
    font-weight: 700;
  }

  /* 分类跳转 */
  .eq-nav {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
    margin: 1rem .5rem .2rem;
  }

  .eq-nav-link {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .7rem;
    color: var(--font-color-main);
    background: var(--bg-color-main);
    border: 1px solid var(--border-color-main);
    border-radius: 999px;
    font-size: .76rem;
    transition: color .2s, border-color .2s, background-color .2s, transform .2s;
  }

  .eq-nav-link span {
    color: var(--font-color-main-light);
    font-size: .68rem;
  }

  .eq-nav-link:hover {
    color: var(--color-primary);
    border-color: var(--color-primary-light-3);
    background: var(--color-primary-light-5);
    transform: translateY(-1px);
    text-decoration: none;
  }

  /* 分组标题 */
  .eq-group {
    margin-top: 1.6rem;
  }

  .eq-group-head {
    display: flex;
    align-items: baseline;
    gap: .6rem;
    padding: .1rem .85rem .55rem;
    border-bottom: 1px dashed var(--border-color-main);
  }

  .eq-group-index {
    color: var(--color-primary-light-2);
    line-height: 1;
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: .02em;
  }

  .eq-group-title {
    margin: 0;
    color: var(--font-color-main);
    font-size: 1.02rem;
    font-weight: 700;
  }

  .eq-group-count {
    margin-left: auto;
    color: var(--font-color-main-light);
    font-size: .72rem;
  }

  /* 装备卡片 */
  .eq-group .row>div {
    padding: .55rem;
  }

  .eq-card {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
    margin: 0;
    overflow: hidden;
    padding: 0;
    transition: transform .28s cubic-bezier(.22, 1, .36, 1), box-shadow .28s;
  }

  .eq-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-box-hover);
  }

  .eq-media {
    position: relative;
    aspect-ratio: 4 / 3;
    overflow: hidden;
    background: var(--bg-color-primary);
  }

  .eq-media img {
    display: block;
    width: 100%;
    height: 100%;
    padding: .7rem;
    object-fit: contain;
    transition: transform .5s cubic-bezier(.22, 1, .36, 1);
  }

  .eq-card:hover .eq-media img {
    transform: scale(1.05);
  }

  .eq-type {
    position: absolute;
    left: .55rem;
    top: .55rem;
    padding: .16rem .5rem;
    color: var(--font-color-main);
    background: var(--bg-color-main);
    border: 1px solid var(--border-color-main);
    border-radius: 999px;
    font-size: .62rem;
    letter-spacing: .04em;
    opacity: .94;
  }

  .eq-body {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    gap: .2rem;
    padding: .75rem .85rem .9rem;
  }

  .eq-model {
    margin: 0;
    color: var(--font-color-main);
    font-size: .92rem;
    font-weight: 700;
    line-height: 1.45;
  }

  .eq-specs {
    margin: 0 0 .25rem;
    color: var(--font-color-main-light);
    font-family: SFMono-Regular, Menlo, Consolas, monospace;
    font-size: .68rem;
    letter-spacing: .01em;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }

  .eq-desc {
    display: -webkit-box;
    margin: 0;
    overflow: hidden;
    color: var(--font-color-main-light);
    font-size: .76rem;
    line-height: 1.7;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
  }

  @media (max-width: 991.98px) {
    .eq-hero {
      grid-template-columns: 1fr;
    }

    .eq-hero-media {
      min-height: 12rem;
    }

    .eq-hero-body {
      padding: 1.35rem 1.25rem;
    }

    .eq-title {
      font-size: 1.45rem;
    }

    .eq-group .row>div {
      padding: .4rem;
    }

    .eq-media {
      aspect-ratio: 5 / 4;
    }

    .eq-media img {
      padding: .45rem;
    }
  }
</style>
<main class="equipment-main">
  <?php
  $eqTotal = 0;
  foreach ($categories as $eqItems) {
    $eqTotal += count($eqItems);
  }
  $eqCats = count($categories);
  ?>
  <div class="container theme-main p-0">
    <div class="p-2">
      <div class="card eq-hero">
        <div class="eq-hero-media">
          <img class="lazy"
            data-original="<?php echo $this->fields->thumb ?: getAssets('assets/images/pages/equipment.webp', false) ?>"
            alt="<?php $this->title() ?>" src="<?php getLazyload(); ?>" />
          <span class="eq-hero-count"><?php echo $eqTotal; ?> 件装备</span>
        </div>
        <div class="eq-hero-body">
          <p class="eq-eyebrow">EQUIPMENT · 我的设备</p>
          <h1 class="eq-title"><?php $this->title(); ?></h1>
          <p class="eq-lead">
            <?php echo htmlspecialchars($this->fields->abstract ?: '记录正在用、用过、想推荐的设备和好物。', ENT_QUOTES, 'UTF-8'); ?></p>
          <div class="eq-stats">
            <span class="eq-chip"><b><?php echo $eqTotal; ?></b> 件装备</span>
            <span class="eq-chip"><b><?php echo $eqCats; ?></b> 个分类</span>
            <span class="eq-chip">长期更新</span>
          </div>
        </div>
      </div>
    </div>

    <?php if ($eqCats > 1): ?>
      <nav class="eq-nav" aria-label="装备分类">
        <?php foreach ($categories as $eqCatName => $eqCatItems): ?>
          <a class="eq-nav-link"
            href="#eq-<?php echo md5($eqCatName); ?>"><?php echo htmlspecialchars($eqCatName, ENT_QUOTES, 'UTF-8'); ?><span><?php echo count($eqCatItems); ?></span></a>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>

    <div class="post-main">
      <?php $eqGroupIndex = 0; ?>
      <?php foreach ($categories as $category => $items): ?>
        <?php $eqGroupIndex++; ?>
        <section class="eq-group" id="eq-<?php echo md5($category); ?>">
          <header class="eq-group-head">
            <span class="eq-group-index"><?php echo str_pad($eqGroupIndex, 2, '0', STR_PAD_LEFT); ?></span>
            <h2 class="eq-group-title"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></h2>
            <span class="eq-group-count"><?php echo count($items); ?> 件</span>
          </header>
          <div class="row no-gutters">
            <?php foreach ($items as $item): ?>
              <div class="col-6 col-md-4 col-xl-3 d-flex">
                <article class="card eq-card w-100">
                  <div class="eq-media">
                    <img class="lazy" src="<?php getLazyload(); ?>"
                      data-original="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>"
                      alt="<?php echo htmlspecialchars($item['model'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if ($item['type'] !== ''): ?>
                      <span class="eq-type"><?php echo htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="eq-body">
                    <h3 class="eq-model text-overflow-base"
                      title="<?php echo htmlspecialchars($item['model'], ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($item['model'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <?php if ($item['specs'] !== ''): ?>
                      <p class="eq-specs" title="<?php echo htmlspecialchars($item['specs'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($item['specs'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                    <p class="eq-desc"><?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>

    <section class="eq-group mt-4">
      <header class="eq-group-head">
        <span class="eq-group-index"><?php echo str_pad($eqCats + 1, 2, '0', STR_PAD_LEFT); ?></span>
        <h2 class="eq-group-title">留言</h2>
        <span class="eq-group-count"><?php echo (int) $this->commentsNum; ?> 条</span>
      </header>
    </section>
    <?php $this->need('components/comments.php'); ?>
  </div>
</main>
<?php $this->need('components/footer.php'); ?>