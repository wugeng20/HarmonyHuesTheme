<?php

/**
 * 关于主题
 *
 * @author  星语社长
 * @link    https://biibii.cn
 * @update  2026-10-29 22:40:00
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

$themeVersion = getVersion();
$themeRepo    = 'https://github.com/wugeng20/HarmonyHuesTheme';
$themeLog     = $themeRepo . '/blob/main/UpdateLog.md';

$themeSpecs = array(
  array('icon' => 'icon-yuan', 'label' => '主题名称', 'value' => $this->title),
  array('icon' => 'icon-zuozhe', 'label' => '主题作者', 'value' => '星语社长'),
  array('icon' => 'icon-shijian', 'label' => '主题版本', 'value' => 'v' . $themeVersion),
  array('icon' => 'icon-gensuixitong', 'label' => '博客程序', 'value' => 'Typecho'),
  array('icon' => 'icon-lianjie', 'label' => '技术栈', 'value' => 'PHP · HTML · CSS · JS'),
  array('icon' => 'icon-qianwang', 'label' => '开源仓库', 'value' => '<a class="theme-link" href="' . $themeRepo . '" target="_blank" rel="noopener">GitHub</a>'),
);

$themeFeatures = array(
  array('icon' => 'icon-yuan', 'title' => '轻新拟态', 'desc' => '同色系表面配双向柔和阴影，卡片像从页面里轻轻浮起，而不是贴在纸上。'),
  array('icon' => 'icon-shensemoshi', 'title' => '双色主题', 'desc' => '亮色、暗色与跟随系统三态，每个组件都有对应的暗色令牌，切换不闪白。'),
  array('icon' => 'icon-caidan', 'title' => '多模式导航', 'desc' => '全宽与居中两种导航形态，下滚隐藏、上滚即现，底部附阅读进度线。'),
  array('icon' => 'icon-shijian', 'title' => '动效体系', 'desc' => '列表、卡片、目录与进度的节奏统一，同时尊重系统的“减少动态效果”。'),
  array('icon' => 'icon-biaoqing', 'title' => '免插件能力', 'desc' => '表情包、评论邮件提醒、友情链接、Sitemap、代码高亮全部内置，装上即用。'),
  array('icon' => 'icon-sousuo', 'title' => '内容友好', 'desc' => '懒加载与灯箱、文章目录锚点、提示框与折叠短代码，外加一页发布热力图归档。'),
);

$themePalette = array(
  array('name' => '页面底色', 'hex' => '#f2f3f4'),
  array('name' => '卡片表面', 'hex' => '#f8f9fa'),
  array('name' => '主题主色', 'hex' => '#165DFF'),
  array('name' => '强调文字', 'hex' => '#0E42D2'),
  array('name' => '暗色底', 'hex' => '#242424'),
  array('name' => '暗色卡片', 'hex' => '#2f2f2f'),
);

$this->need('components/header.php');
?>
<style type="text/css">
  /* ===== 关于主题：页面局部样式（仅本页） ===== */
  .theme-page .theme-hero {
    overflow: hidden;
    padding: 0;
  }

  .theme-page .theme-hero-body {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
    padding: 1.15rem 1.5rem 1.45rem;
    background:
      radial-gradient(120% 140% at 0% 0%, var(--color-primary-light-5) 0%, transparent 52%),
      var(--card-surface);
  }

  .theme-page .theme-hero-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
  }

  .theme-page .theme-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.18rem 0.65rem;
    font-size: 0.72rem;
    line-height: 1.6;
    color: var(--font-color-main);
    background: var(--bg-color-primary);
    border: 1px solid var(--border-color-main);
    border-radius: var(--border-radius-xlarge);
    box-shadow: var(--shadow-inset-box);
  }

  .theme-page .theme-chip .iconfont {
    font-size: 0.82rem;
    color: var(--color-primary-text);
  }

  .theme-page .theme-hero-title {
    margin: 0;
    font-size: 2.05rem;
    font-weight: 800;
    letter-spacing: 0.02em;
    color: var(--font-color-main);
  }

  .theme-page .theme-hero-desc {
    max-width: 32rem;
    margin: 0;
    font-size: 0.88rem;
    line-height: 1.8;
    color: var(--font-color-muted);
  }

  .theme-page .theme-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.3rem;
  }

  .theme-page .theme-btn {
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
    transition: transform 0.28s var(--transition-ease-1, ease), box-shadow 0.28s ease, color 0.28s ease;
  }

  .theme-page .theme-btn:hover {
    color: var(--color-primary-text);
    transform: translateY(-2px);
    box-shadow: var(--shadow-box-hover);
  }

  .theme-page .theme-btn-primary {
    color: #fff;
    background: var(--color-primary);
    border-color: transparent;
    box-shadow: 0 8px 18px rgba(22, 93, 255, 0.3);
  }

  .theme-page .theme-btn-primary:hover {
    color: #fff;
    background: var(--color-primary-dark-1);
  }

  .theme-page .theme-hero-media {
    overflow: hidden;
  }

  .theme-page .theme-hero-media img {
    display: block;
    width: 100%;
    height: auto;
    transition: transform 0.8s var(--transition-ease-1, ease);
  }

  .theme-page .theme-hero:hover .theme-hero-media img {
    transform: scale(1.03);
  }

  /* 规格栅格 */
  .theme-page .theme-specs {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.6rem;
    margin-top: 0.75rem;
  }

  .theme-page .theme-spec {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.7rem 0.9rem;
    background: var(--card-surface);
    border: var(--border-solid-main);
    border-radius: var(--border-radius-medium);
    box-shadow: var(--shadow-card);
  }

  .theme-page .theme-spec-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 2.1rem;
    width: 2.1rem;
    height: 2.1rem;
    color: var(--color-primary-text);
    background: var(--bg-color-primary);
    border-radius: var(--border-radius-circle);
    box-shadow: var(--shadow-inset-box);
  }

  .theme-page .theme-spec-icon .iconfont {
    font-size: 1rem;
  }

  .theme-page .theme-spec-label {
    display: block;
    font-size: 0.72rem;
    color: var(--font-color-muted);
  }

  .theme-page .theme-spec-value {
    display: block;
    overflow: hidden;
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--font-color-main);
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .theme-page .theme-link {
    color: var(--color-primary-text);
  }

  /* 区块通用 */
  .theme-page .theme-block {
    margin-top: 0.75rem;
    padding: 1.1rem 1.2rem 1.2rem;
  }

  .theme-page .theme-block-head {
    display: flex;
    align-items: baseline;
    flex-wrap: wrap;
    gap: 0.5rem 0.7rem;
    margin-bottom: 0.9rem;
  }

  .theme-page .theme-block-head h2 {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin: 0;
    font-size: 1.05rem;
    font-weight: 800;
  }

  .theme-page .theme-block-head h2 .iconfont {
    font-size: 1.15rem;
    color: var(--color-primary-text);
  }

  .theme-page .theme-block-head p {
    margin: 0;
    font-size: 0.78rem;
    color: var(--font-color-muted);
  }

  /* 色板 */
  .theme-page .theme-palette {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 0.55rem;
  }

  .theme-page .theme-swatch {
    padding: 0.4rem;
    text-align: center;
    background: var(--card-surface);
    border: var(--border-solid-main);
    border-radius: var(--border-radius-medium);
    box-shadow: var(--shadow-box-small);
  }

  .theme-page .theme-swatch-color {
    display: block;
    height: 3rem;
    margin-bottom: 0.4rem;
    border-radius: var(--border-radius-base);
    box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.1);
  }

  .theme-page .theme-swatch-name {
    display: block;
    font-size: 0.7rem;
    color: var(--font-color-main);
  }

  .theme-page .theme-swatch-hex {
    display: block;
    font-size: 0.64rem;
    color: var(--font-color-muted);
    letter-spacing: 0.02em;
  }

  /* 特性 */
  .theme-page .theme-features {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.6rem;
  }

  .theme-page .theme-feature {
    padding: 0.95rem 1rem;
    background: var(--card-surface);
    border: var(--border-solid-main);
    border-radius: var(--border-radius-medium);
    box-shadow: var(--shadow-box-small);
    transition: transform 0.3s var(--transition-ease-1, ease), box-shadow 0.3s ease;
  }

  .theme-page .theme-feature:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-box-hover);
  }

  .theme-page .theme-feature-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.35rem;
    height: 2.35rem;
    margin-bottom: 0.55rem;
    color: var(--color-primary-text);
    background: var(--color-primary-light-5);
    border-radius: var(--border-radius-circle);
  }

  .theme-page .theme-feature-icon .iconfont {
    font-size: 1.1rem;
  }

  .theme-page .theme-feature-title {
    display: block;
    margin-bottom: 0.25rem;
    font-size: 0.92rem;
    font-weight: 700;
  }

  .theme-page .theme-feature-desc {
    margin: 0;
    font-size: 0.78rem;
    line-height: 1.7;
    color: var(--font-color-muted);
  }

  /* 正文区 */
  .theme-page .theme-intro {
    margin-top: 0.75rem;
    padding: 1.25rem 1.4rem 1.4rem;
  }

  .theme-page .theme-intro .post-content {
    margin-top: 0.2rem;
  }

  .theme-page .theme-intro .post-copyright {
    margin-top: 1.4rem;
  }

  :root[data-theme="dark"] .theme-page .theme-hero-body {
    background:
      radial-gradient(120% 140% at 0% 0%, rgba(106, 161, 255, 0.08) 0%, transparent 55%),
      var(--card-surface);
  }

  :root[data-theme="dark"] .theme-page .theme-spec-icon {
    background: var(--bg-color-primary);
    color: var(--color-primary-light-2);
  }

  :root[data-theme="dark"] .theme-page .theme-swatch-color {
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
  }

  :root[data-theme="dark"] .theme-page .theme-feature-icon {
    background: rgba(106, 161, 255, 0.16);
    color: var(--color-primary-light-2);
  }

  @media (max-width: 991.98px) {

    .theme-page .theme-specs,
    .theme-page .theme-features {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .theme-page .theme-palette {
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }
  }

  @media (max-width: 575.98px) {
    .theme-page .theme-hero-body {
      padding: 1rem;
    }

    .theme-page .theme-hero-media {
      padding: 0.5rem 0.5rem 0;
    }

    .theme-page .theme-hero-title {
      font-size: 1.55rem;
    }

    .theme-page .theme-hero-desc {
      font-size: 0.82rem;
    }

    .theme-page .theme-specs,
    .theme-page .theme-features {
      grid-template-columns: minmax(0, 1fr);
    }

    .theme-page .theme-spec {
      gap: 0.5rem;
      padding: 0.55rem 0.7rem;
    }

    .theme-page .theme-spec-icon {
      flex-basis: 1.9rem;
      width: 1.9rem;
      height: 1.9rem;
    }

    .theme-page .theme-palette {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (prefers-reduced-motion: reduce) {

    .theme-page .theme-btn:hover,
    .theme-page .theme-feature:hover {
      transform: none;
    }
  }

  .grid-img-container img {
    object-fit: contain;
  }
</style>
<!--主体st-->
<main class="theme-page">
  <div class="container p-1 p-md-2">
    <section class="card theme-hero">
      <div class="theme-hero-media">
        <img src="<?php echo getImgLink($this); ?>" alt="<?php $this->title() ?>">
      </div>
      <div class="theme-hero-body">
        <div class="theme-hero-tags">
          <span class="theme-chip"><i
              class="iconfont icon-shijian"></i>v<?php echo htmlspecialchars($themeVersion); ?></span>
          <span class="theme-chip"><i class="iconfont icon-zuozhe"></i>星语社长</span>
          <span class="theme-chip"><i class="iconfont icon-gensuixitong"></i>Typecho</span>
        </div>
        <h1 class="theme-hero-title"><?php $this->title() ?></h1>
        <p class="theme-hero-desc">轻新拟态风格、双色主题与克制的动效，把阅读这件事还给内容本身。</p>
        <div class="theme-hero-actions">
          <a class="theme-btn theme-btn-primary" href="<?php echo $themeRepo; ?>" target="_blank" rel="noopener">
            <i class="iconfont icon-qianwang"></i>GitHub 下载
          </a>
          <a class="theme-btn" href="<?php echo $themeLog; ?>" target="_blank" rel="noopener">
            <i class="iconfont icon-shijian"></i>更新日志
          </a>
        </div>
      </div>
    </section>

    <section class="theme-specs" aria-label="主题信息">
      <?php foreach ($themeSpecs as $spec): ?>
        <div class="theme-spec">
          <span class="theme-spec-icon"><i class="iconfont <?php echo htmlspecialchars($spec['icon']); ?>"></i></span>
          <span>
            <span class="theme-spec-label"><?php echo htmlspecialchars($spec['label']); ?></span>
            <span class="theme-spec-value"><?php echo $spec['value']; ?></span>
          </span>
        </div>
      <?php endforeach; ?>
    </section>

    <section class="card theme-block">
      <header class="theme-block-head">
        <h2><i class="iconfont icon-haibao"></i>色板</h2>
        <p>所有颜色都是 CSS 变量，改一处即可整体换肤。</p>
      </header>
      <div class="theme-palette">
        <?php foreach ($themePalette as $color): ?>
          <div class="theme-swatch">
            <span class="theme-swatch-color" style="background: <?php echo htmlspecialchars($color['hex']); ?>;"></span>
            <span class="theme-swatch-name"><?php echo htmlspecialchars($color['name']); ?></span>
            <span class="theme-swatch-hex"><?php echo htmlspecialchars($color['hex']); ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="card theme-block">
      <header class="theme-block-head">
        <h2><i class="iconfont icon-yuan"></i>核心特性</h2>
        <p>从视觉到交互，尽量少打扰、多留白。</p>
      </header>
      <div class="theme-features">
        <?php foreach ($themeFeatures as $feature): ?>
          <article class="theme-feature">
            <span class="theme-feature-icon"><i
                class="iconfont <?php echo htmlspecialchars($feature['icon']); ?>"></i></span>
            <span class="theme-feature-title"><?php echo htmlspecialchars($feature['title']); ?></span>
            <p class="theme-feature-desc"><?php echo htmlspecialchars($feature['desc']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="card theme-block theme-intro" id="theme-intro">
      <header class="theme-block-head">
        <h2><i class="iconfont icon-mulu"></i>主题介绍</h2>
        <p>设计思路、功能清单与安装方式。</p>
      </header>
      <div class="post-content markdown-body" itemprop="articleBody"><?php $this->content(); ?></div>

      <div class="post-copyright mt-4 p-3">
        <div class="copyright-content">
          <div class="copyright-title">&#169版权申明</div>
          <p class="copyright-desc"><span class="ml-3"></span>- 本文由作者 <a href="<?php $this->author->permalink(); ?>"
              title="<?php $this->author(); ?>">@<?php $this->author(); ?></a>
            原创发布在<?php $this->options->title(); ?>站点。未经许可，禁止转载。</p>
        </div>
        <div class="copyright-svgname">
          <?php $this->options->svgName(); ?>
        </div>
      </div>
    </section>

    <?php $this->need('components/comments.php'); ?>
  </div>
</main>
<!--主体end-->
<?php $this->need('components/footer.php'); ?>