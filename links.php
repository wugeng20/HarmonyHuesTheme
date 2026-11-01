<?php

/**
 * 友情链接
 *
 * @author  星语社长
 * @link    https://biibii.cn
 * @update  2026-10-29 23:30:00
 * @package custom
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
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
      if (! empty($attr[1])) {
        $key = strtolower($attr[1]);
        $value = $attr[3];
      } else {
        $key = strtolower($attr[4]);
        $value = $attr[5];
      }
      if (array_key_exists($key, $defaults)) {
        $link[$key] = htmlspecialchars_decode($value ?? '', ENT_QUOTES);
      }
    }
    if (empty($link['group'])) {
      $link['group'] = '网上邻居';
    }
    $group = $link['group'];
    if (! isset($groupedLinks[$group])) {
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

// 统计可见分组与友链数量、收集首屏头像
$visibleGroups = array();
$totalLinks = 0;
$lostLinks = 0;
$heroAvatars = array();
foreach ($groupedLinks as $groupName => $items) {
  if (count($items) === 1 && isset($items[0]['status']) && $items[0]['status'] === '隐藏') {
    continue;
  }
  $visible = array();
  foreach ($items as $item) {
    if (($item['status'] ?? '') !== '隐藏') {
      $visible[] = $item;
    }
  }
  if (! $visible) {
    continue;
  }
  $visibleGroups[$groupName] = $visible;
  $totalLinks += count($visible);
  foreach ($visible as $item) {
    if (($item['status'] ?? '') === '失联') {
      $lostLinks++;
    }
    if (count($heroAvatars) < 5 && ! empty($item['avatar'])) {
      $heroAvatars[] = $item['avatar'];
    }
  }
}
$siteName = $this->options->title;
$siteUrl = rtrim((string) $this->options->siteUrl, '/');
$siteDesc = $this->options->description;
$siteLogo = getAssets('assets/images/favicon.png', false);
$applyUrl = $this->permalink;
$linksFallbackImg = getAssets('assets/images/blogimg.webp', false); // 头像默认图
?>
<style type="text/css">
  /* ===== 友情链接：页面局部样式（仅本页） ===== */
  .links-page .links-hero {
    position: relative;
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(0, 0.6fr);
    gap: 1rem;
    padding: 1.5rem 1.6rem;
    overflow: hidden;
  }

  .links-page .links-hero::after {
    content: '';
    position: absolute;
    right: -8%;
    bottom: -55%;
    width: 26rem;
    height: 20rem;
    background: radial-gradient(closest-side, var(--color-primary-light-5), transparent 72%);
    pointer-events: none;
  }

  :root[data-theme="dark"] .links-page .links-hero::after {
    background: radial-gradient(closest-side, rgba(106, 161, 255, 0.16), transparent 72%);
  }

  .links-page .links-hero-main {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
  }

  .links-page .links-kicker {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--color-primary-text);
  }

  .links-page .links-hero-title {
    margin: 0;
    font-size: 1.85rem;
    font-weight: 800;
    line-height: 1.35;
    color: var(--font-color-main);
  }

  .links-page .links-hero-desc {
    max-width: 34rem;
    margin: 0;
    font-size: 0.84rem;
    line-height: 1.8;
    color: var(--font-color-muted);
  }

  .links-page .links-hero-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    margin-top: 0.35rem;
  }

  .links-page .links-stat {
    display: inline-flex;
    align-items: baseline;
    gap: 0.28rem;
    padding: 0.3rem 0.7rem;
    font-size: 0.72rem;
    color: var(--font-color-muted);
    background: var(--bg-color-primary);
    border: 1px solid var(--border-color-main);
    border-radius: var(--border-radius-xlarge);
    box-shadow: var(--shadow-inset-box);
  }

  .links-page .links-stat b {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--color-primary-text);
  }

  .links-page .links-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
  }

  .links-page .links-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.42rem 1rem;
    font-size: 0.82rem;
    color: var(--font-color-main);
    background: var(--bg-color-primary);
    border: 1px solid var(--border-color-main);
    border-radius: var(--border-radius-xlarge);
    box-shadow: var(--shadow-box-small);
    cursor: pointer;
    transition: transform 0.28s var(--transition-ease-1, ease), box-shadow 0.28s ease, color 0.28s ease;
  }

  .links-page .links-btn:hover {
    color: var(--color-primary-text);
    transform: translateY(-2px);
    box-shadow: var(--shadow-box-hover);
  }

  .links-page .links-btn-primary {
    color: #fff !important;
    background: var(--color-primary);
    border-color: transparent;
    box-shadow: 0 8px 18px rgba(22, 93, 255, 0.3);
  }

  .links-page .links-btn-primary:hover {
    color: #fff;
    background: var(--color-primary-dark-1);
  }

  .links-page .links-hero-avatars {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    align-content: center;
    flex-wrap: wrap;
    gap: 0.45rem;
  }

  .links-page .links-hero-avatar {
    width: 3rem;
    height: 3rem;
    object-fit: cover;
    border-radius: var(--border-radius-circle);
    background: var(--bg-color-primary);
    border: 3px solid var(--bg-color-main);
    box-shadow: var(--shadow-box-small);
    transition: transform 0.3s var(--transition-ease-1, ease);
  }

  .links-page .links-hero-avatar:hover {
    transform: translateY(-4px) scale(1.04);
  }

  /* 分组 */
  .links-page .links-group {
    margin: 1.15rem 0;
  }

  .links-page .links-group-head {
    display: flex;
    align-items: flex-end;
    gap: 0.65rem;
    margin: 0.2rem 0.15rem 0.55rem;
  }

  .links-page .links-group-title {
    margin: 0;
    font-size: 2rem;
    line-height: 1.1;
    font-weight: 700;
    letter-spacing: 0.01em;
    opacity: 0.55;
    -webkit-text-stroke: 1.4px var(--font-color-main);
    transition: opacity 0.3s ease;
  }

  .links-page .links-group:hover .links-group-title {
    opacity: 0.75;
    -webkit-text-fill-color: var(--font-color-main);
  }

  .links-page .links-group-count {
    margin-bottom: 0.55rem;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    color: var(--font-color-muted);
  }

  .links-page .links-group .links-group-count::before {
    content: '/ ';
    opacity: 0.6;
  }

  /* 申请区标题保持小号实心 */
  .links-page .links-apply .links-group-head {
    align-items: center;
    margin-bottom: 0.95rem;
  }

  .links-page .links-apply .links-group-title {
    font-size: 1.25rem;
    line-height: 1.3;
    opacity: 1;
    -webkit-mask: none;
    mask: none;
    -webkit-text-fill-color: currentColor;
  }

  @media (max-width: 575.98px) {
    .links-page .links-group-title {
      font-size: 1.9rem;
    }
  }

  /* 卡片 */
  .links-page .links-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.6rem;
  }

  .links-page .links-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.7rem 2rem 0.7rem 0.8rem;
    background: var(--card-surface);
    border: var(--border-solid-main);
    border-radius: var(--border-radius-medium);
    box-shadow: var(--shadow-card);
    transition: opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1) var(--d, 0ms),
      transform 0.6s cubic-bezier(0.22, 1, 0.36, 1) var(--d, 0ms),
      box-shadow 0.3s ease;
  }

  .links-page .links-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-card-hover);
  }

  @media (prefers-reduced-motion: no-preference) {
    .links-page.links-anim .links-card:not(.is-in) {
      opacity: 0;
      transform: translateY(16px) scale(0.985);
    }
  }

  .links-page .links-avatar {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 2.7rem;
    width: 2.7rem;
    height: 2.7rem;
    border-radius: var(--border-radius-circle);
    background: var(--bg-color-primary);
    box-shadow: inset 0 0 0 1px var(--border-color-main);
  }

  .links-page .links-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--border-radius-circle);
  }

  .links-page .links-dot {
    position: absolute;
    right: -1px;
    bottom: -1px;
    width: 0.72rem;
    height: 0.72rem;
    border: 2px solid var(--bg-color-main);
    border-radius: var(--border-radius-circle);
    background: var(--success);
  }

  .links-page .links-dot.is-lost {
    background: var(--danger);
  }

  .links-page .links-meta {
    flex: 1;
    min-width: 0;
  }

  .links-page .links-name {
    display: block;
    overflow: hidden;
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--font-color-main);
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .links-page .links-desc {
    display: -webkit-box;
    margin-top: 0.12rem;
    overflow: hidden;
    font-size: 0.72rem;
    line-height: 1.55;
    color: var(--font-color-muted);
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }

  .links-page .links-go {
    position: absolute;
    bottom: 0;
    right: 0.85rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    line-height: 1;
    color: var(--font-color-muted);
    opacity: 0.45;
    transform: translateY(-50%);
    transition: opacity 0.3s ease, color 0.3s ease;
  }

  .links-page .links-card:hover .links-go {
    opacity: 1;
    color: var(--color-primary-text);
    transform: translateY(-50%) translateX(3px);
  }

  .links-page .links-card.is-lost {
    opacity: 0.7;
    cursor: not-allowed;
  }

  .links-page .links-card.is-lost:hover {
    transform: none;
    box-shadow: var(--shadow-card);
  }

  .links-page .links-lost-badge {
    position: absolute;
    top: 0.4rem;
    right: 0.45rem;
    padding: 0 0.34rem;
    font-size: 0.6rem;
    line-height: 1.5;
    color: #fff;
    background: var(--danger);
    border-radius: var(--border-radius-xlarge);
  }

  /* 申请区 */
  .links-page .links-apply {
    margin-top: 1.2rem;
    padding: 1.2rem 1.35rem 1.3rem;
  }

  .links-page .links-apply .post-content {
    font-size: 0.9rem;
  }

  .links-page .links-progress {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin: 1rem 0 0.7rem;
  }

  .links-page .links-progress-bar {
    position: relative;
    flex: 1;
    height: 0.42rem;
    overflow: hidden;
    background: var(--bg-color-primary);
    border-radius: var(--border-radius-xlarge);
    box-shadow: var(--shadow-inset-box);
  }

  .links-page .links-progress-bar span {
    display: block;
    width: 0;
    height: 100%;
    background: linear-gradient(90deg, var(--color-primary-light-2), var(--color-primary));
    border-radius: inherit;
    transition: width 0.35s var(--transition-ease-1, ease);
  }

  .links-page .links-progress-count {
    font-size: 0.72rem;
    color: var(--font-color-muted);
    white-space: nowrap;
  }

  .links-page .links-steps {
    display: grid;
    gap: 0.45rem;
    margin: 0;
  }

  .links-page .links-step {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    padding: 0.6rem 0.75rem;
    background: var(--bg-color-primary);
    border: 1px solid transparent;
    border-radius: var(--border-radius-base);
    cursor: pointer;
    transition: background-color 0.25s ease, border-color 0.25s ease;
  }

  .links-page .links-step:hover {
    background: var(--bg-color-secondary);
  }

  .links-page .links-step input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }

  .links-page .links-step-box {
    position: relative;
    flex: 0 0 1.15rem;
    width: 1.15rem;
    height: 1.15rem;
    margin-top: 0.1rem;
    background: var(--bg-color-main);
    border-radius: 0.35rem;
    box-shadow: var(--shadow-inset-box);
    transition: background-color 0.2s ease;
  }

  .links-page .links-step-box::after {
    content: '';
    position: absolute;
    left: 0.3rem;
    top: 0.1rem;
    width: 0.3rem;
    height: 0.58rem;
    border: solid #fff;
    border-width: 0 2px 2px 0;
    transform: rotate(42deg) scale(0);
    transition: transform 0.2s ease;
  }

  .links-page .links-step input:checked+.links-step-box {
    background: var(--color-primary);
    box-shadow: none;
  }

  .links-page .links-step input:checked+.links-step-box::after {
    transform: rotate(42deg) scale(1);
  }

  .links-page .links-step.is-checked {
    background: var(--color-primary-light-5);
    border-color: var(--color-primary-light-4);
  }

  :root[data-theme="dark"] .links-page .links-step.is-checked {
    background: rgba(106, 161, 255, 0.14);
    border-color: rgba(106, 161, 255, 0.24);
  }

  .links-page .links-step-text {
    font-size: 0.8rem;
    line-height: 1.65;
    color: var(--font-color-main);
  }

  .links-page .links-step-text b {
    color: var(--color-primary-text);
  }

  /* 本站信息 */
  .links-page .links-site {
    margin-top: 1rem;
    padding: 0.9rem 1rem;
    background: var(--bg-color-primary);
    border: 1px dashed var(--border-color-main);
    border-radius: var(--border-radius-medium);
  }

  .links-page .links-site-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.55rem 0.9rem;
  }

  .links-page .links-site-item {
    font-size: 0.72rem;
    color: var(--font-color-muted);
  }

  .links-page .links-site-item b {
    display: block;
    margin-top: 0.1rem;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--font-color-main);
    word-break: break-all;
  }

  .links-page .links-site-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
  }

  .links-page .links-svgname {
    position: absolute;
    right: 1rem;
    bottom: 0.8rem;
    opacity: 0.6;
  }

  @media (max-width: 991.98px) {
    .links-page .links-hero {
      grid-template-columns: minmax(0, 1fr);
    }

    .links-page .links-hero-avatars {
      justify-content: flex-start;
    }

    .links-page .links-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 575.98px) {
    .links-page .links-hero {
      padding: 1.1rem 1rem;
    }

    .links-page .links-hero-title {
      font-size: 1.5rem;
    }

    .links-page .links-grid {
      grid-template-columns: minmax(0, 1fr);
    }

    .links-page .links-site-grid {
      grid-template-columns: minmax(0, 1fr);
    }

    .links-page .links-apply {
      padding: 1rem;
    }
  }

  @media (prefers-reduced-motion: reduce) {

    .links-page .links-btn:hover,
    .links-page .links-card:hover,
    .links-page .links-hero-avatar:hover {
      transform: none;
    }
  }
</style>
<main class="links-page">
  <div class="container p-1 p-md-2">
    <section class="card links-hero">
      <div class="links-hero-main">
        <span class="links-kicker">Friends · 友情链接</span>
        <h1 class="links-hero-title">
          <?php echo $this->authorId ? $this->author->screenName() : $this->user->screenName(); ?>のTA的朋友们</h1>
        <p class="links-hero-desc">互联网很大，邻居很近。这里收录一路同行、彼此串门的独立博客，欢迎随手点开一位，去看看别人的生活与思考。</p>
        <div class="links-hero-stats">
          <span class="links-stat"><b><?php echo $totalLinks; ?></b>位邻居</span>
          <span class="links-stat"><b><?php echo count($visibleGroups); ?></b>个分组</span>
          <span class="links-stat"><b><?php echo $lostLinks; ?></b>位失联</span>
        </div>
        <div class="links-hero-actions">
          <button type="button" class="links-btn links-btn-primary" id="links-random">
            <i class="iconfont icon-random"></i>随机串门
          </button>
          <a class="links-btn" href="#links-apply">
            <i class="iconfont icon-lianjie"></i>申请友链
          </a>
        </div>
      </div>
      <div class="links-hero-avatars" aria-hidden="true">
        <?php foreach ($heroAvatars as $avatar): ?>
          <img class="links-hero-avatar" src="<?php echo htmlspecialchars($avatar); ?>"
            data-fallback="<?php echo htmlspecialchars(getAvatarLazyload(false)); ?>" alt="" loading="lazy"
            decoding="async" onerror="this.onerror=null;this.src=this.dataset.fallback;">
        <?php endforeach; ?>
      </div>
    </section>

    <?php foreach ($visibleGroups as $groupName => $links): ?>
      <section class="links-group">
        <div class="links-group-head">
          <h2 class="links-group-title title-text-stroke"><?php echo htmlspecialchars($groupName ?? ''); ?></h2>
          <span class="links-group-count"><?php echo count($links); ?></span>
        </div>
        <div class="links-grid">
          <?php foreach ($links as $link):
            $isLost = ($link['status'] ?? '') === '失联';
            $linkUrl = $link['url'] ?? '';
          ?>
            <?php if ($isLost): ?>
              <div class="links-card is-lost" title="<?php echo htmlspecialchars($link['title'] ?? ''); ?>">
              <?php else: ?>
                <a class="links-card" href="<?php echo htmlspecialchars($linkUrl); ?>" target="_blank" rel="noopener"
                  title="<?php echo htmlspecialchars($link['title'] ?? ''); ?>"
                  data-url="<?php echo htmlspecialchars($linkUrl); ?>">
                <?php endif; ?>
                <span class="links-avatar">
                  <img src="<?php echo htmlspecialchars($link['avatar'] ?: $linksFallbackImg); ?>"
                    data-fallback="<?php echo htmlspecialchars($linksFallbackImg); ?>"
                    alt="<?php echo htmlspecialchars($link['title'] ?? ''); ?>" loading="lazy" decoding="async"
                    onerror="this.onerror=null;this.src=this.dataset.fallback;">
                  <i class="links-dot<?php echo $isLost ? ' is-lost' : ''; ?>"></i>
                </span>
                <span class="links-meta">
                  <span class="links-name"><?php echo htmlspecialchars($link['title'] ?? ''); ?></span>
                  <span class="links-desc"><?php echo htmlspecialchars($link['desc'] ?: $linkUrl); ?></span>
                </span>
                <?php if ($isLost): ?>
                  <span class="links-lost-badge">失联</span>
                <?php else: ?>
                  <i class="iconfont icon-qianwang links-go"></i>
                <?php endif; ?>
                <?php echo $isLost ? '</div>' : '</a>'; ?>
              <?php endforeach; ?>
              </div>
      </section>
    <?php endforeach; ?>
    <section class="card links-apply" id="links-apply">
      <div class="links-head text-center">
        <div class="links-hero-title font-weight-bold">
          <p class="text-shadow-style">本站添加的友链要求</p>
        </div>
      </div>

      <div class="post-content markdown-body" itemprop="articleBody"><?php echo $content; ?></div>

      <div class="links-progress">
        <div class="links-progress-bar"><span></span></div>
        <span class="links-progress-count">0 / 5 已确认</span>
      </div>

      <div class="links-steps">
        <label class="links-step">
          <input type="checkbox" class="links-check">
          <span class="links-step-box"></span>
          <span class="links-step-text">我已添加：<b><?php $this->options->title() ?></b> 博客的友情链接</span>
        </label>
        <label class="links-step">
          <input type="checkbox" class="links-check">
          <span class="links-step-box"></span>
          <span class="links-step-text">我的链接主体为<b>个人</b>，网站类型为<b>博客</b></span>
        </label>
        <label class="links-step">
          <input type="checkbox" class="links-check">
          <span class="links-step-box"></span>
          <span class="links-step-text">我的网站目前可在中国大陆正常访问</span>
        </label>
        <label class="links-step">
          <input type="checkbox" class="links-check">
          <span class="links-step-box"></span>
          <span class="links-step-text">网站内容符合中国大陆相关法律法规</span>
        </label>
        <label class="links-step">
          <input type="checkbox" class="links-check">
          <span class="links-step-box"></span>
          <span class="links-step-text">我的网站首屏加载时间不超过 1 分钟</span>
        </label>
      </div>

      <div class="links-site">
        <div class="links-site-grid">
          <div class="links-site-item">网站名称<b><?php echo htmlspecialchars($siteName); ?></b></div>
          <div class="links-site-item">网站地址<b><?php echo htmlspecialchars($siteUrl); ?></b></div>
          <div class="links-site-item">网站图标<b><?php echo htmlspecialchars($siteLogo); ?></b></div>
          <div class="links-site-item">网站描述<b><?php echo htmlspecialchars($siteDesc); ?></b></div>
        </div>
        <div class="links-site-actions">
          <button type="button" class="links-btn" id="links-copy">
            <i class="iconfont icon-fenxiang"></i>复制本站信息
          </button>
          <a class="links-btn" href="mailto:1431258805@qq.com">
            <i class="iconfont icon-youxiang"></i>发送邮件申请
          </a>
        </div>
      </div>

      <div class="links-svgname animated-signature"><?php $this->options->svgName(); ?></div>
    </section>

    <?php $this->need('components/comments.php'); ?>
  </div>
</main>
<script type="text/javascript">
  (function($) {
    $(function() {
      var $page = $('.links-page');
      var $form = $('#comment-form');
      var $checks = $page.find('.links-check');
      var $bar = $page.find('.links-progress-bar span');
      var $count = $page.find('.links-progress-count');
      var template = '网站名称：\n网站地址：\n网站图标URL：\n网站描述：\n能看到友情链接的地址：';

      $form.hide();

      function sync() {
        var done = 0;
        $checks.each(function() {
          var $input = $(this);
          var checked = $input.prop('checked');
          if (checked) {
            done++;
          }
          $input.closest('.links-step').toggleClass('is-checked', checked);
        });
        $bar.css('width', (done / $checks.length * 100) + '%');
        $count.text(done + ' / ' + $checks.length + ' 已确认');
        if (done === $checks.length) {
          $form.show();
          if (!$('#textarea').val()) {
            $('#textarea').val(template);
          }
        } else {
          $form.hide();
        }
      }

      $checks.on('change', sync);
      sync();

      // 卡片入场动画
      var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (!reduceMotion && 'IntersectionObserver' in window) {
        var $cards = $page.find('.links-card');
        var vh = window.innerHeight || document.documentElement.clientHeight;
        $page.addClass('links-anim');
        $cards.each(function(index) {
          var rect = this.getBoundingClientRect();
          if (rect.top < vh * 0.94) {
            this.style.setProperty('--d', Math.min(index, 8) * 55 + 'ms');
            this.classList.add('is-in');
          } else {
            this.style.setProperty('--d', '0ms');
          }
        });
        var io = new IntersectionObserver(function(entries) {
          entries.forEach(function(entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-in');
              io.unobserve(entry.target);
            }
          });
        }, {
          threshold: 0.12,
          rootMargin: '0px 0px -6% 0px'
        });
        $cards.each(function() {
          if (!this.classList.contains('is-in')) {
            io.observe(this);
          }
        });
      }

      // 随机串门
      $page.on('click', '#links-random', function() {
        var urls = $page.find('.links-card[data-url]').map(function() {
          return this.getAttribute('data-url');
        }).get();
        if (!urls.length) {
          return;
        }
        var url = urls[Math.floor(Math.random() * urls.length)];
        window.open(url, '_blank', 'noopener');
      });

      // 复制本站信息
      $page.on('click', '#links-copy', function() {
        var text = [
          '网站名称：' + <?php echo json_encode($siteName, JSON_UNESCAPED_UNICODE); ?>,
          '网站地址：' + <?php echo json_encode($siteUrl, JSON_UNESCAPED_UNICODE); ?>,
          '网站图标URL：' + <?php echo json_encode($siteLogo, JSON_UNESCAPED_UNICODE); ?>,
          '网站描述：' + <?php echo json_encode($siteDesc, JSON_UNESCAPED_UNICODE); ?>,
          '本站友链页：' + <?php echo json_encode($applyUrl, JSON_UNESCAPED_UNICODE); ?>
        ].join('\n');
        var done = function() {
          var $btn = $page.find('#links-copy');
          $btn.html('<i class="iconfont icon-guanbi"></i>已复制');
          setTimeout(function() {
            $btn.html('<i class="iconfont icon-fenxiang"></i>复制本站信息');
          }, 1600);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(done, function() {});
        } else {
          var $ta = $('<textarea>').val(text).appendTo('body').select();
          try {
            document.execCommand('copy');
            done();
          } catch (e) {}
          $ta.remove();
        }
      });

      $('.reply.comment-footer').on('click', function() {
        $form.show();
        $('.comment-list #textarea').val('');
      });

      $('#cancel-comment-reply-link').on('click', function() {
        $form.hide();
      });
    });
  })(jQuery);
</script>
<?php $this->need('components/footer.php'); ?>