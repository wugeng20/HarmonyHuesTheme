<?php

/**
 * go外链
 * 
 * @author  星语社长
 * @link    https://biibii.cn
 * @update  2024-12-30 23:57:20
 */
if (!defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

$options = Typecho_Widget::widget('Widget_Options');

// 获取 URL 中的 target 参数
if (!isset($_GET['target']) || empty($_GET['target']) || empty($options->isGoLink)) {
  header('Location: ' . getSitePath('404.php'));
  exit;
}

$t_url = is_string($_GET['target']) ? trim($_GET['target']) : '';

// 判断取值是否加密
if ($t_url !== '' && $t_url === base64_encode(base64_decode($t_url))) {
  $t_url = trim((string) base64_decode($t_url));
}

// 对取值进行网址校验和判断（保留原始值，输出时按各自上下文转义）
$allowed_protocols = array('http', 'https', 'thunder', 'qqdl', 'ed2k', 'Flashget', 'qbrowser');
$pattern = '/^(' . implode('|', $allowed_protocols) . '):\/\//i';

if (preg_match($pattern, $t_url)) {
  $url = $t_url;
} elseif (preg_match('/\./i', $t_url) && ! preg_match('/[\s<>"\'`\\\\]/', $t_url)) {
  $url = 'http://' . $t_url;
} else {
  header('Location: ' . getSitePath('404.php'));
  exit;
}

// 设置页面标题
$title = $options->title . ' - 安全中心';

$goHost = parse_url($url, PHP_URL_HOST);
$goScheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
$goHost = $goHost ? $goHost : $url;
$goScheme = $goScheme ? $goScheme : 'http';
$goHome = rtrim((string) $options->siteUrl, '/') . '/';
$goLogo = $options->favicon ? $options->favicon : getAssets('assets/images/favicon.webp', false);
$goJsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<!DOCTYPE HTML>
<html lang="zh-CN" data-theme="<?php echo getThemeMode(); ?>">

<head>
  <meta charset="<?php $options->charset(); ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
  <meta name="renderer" content="webkit">
  <?php if ($options->isCSP): ?>
    <meta http-equiv="Content-Security-Policy" content="<?php $options->contentCSP(); ?>">
  <?php endif; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
  <meta name="robots" content="noindex, nofollow">
  <?php if ($options->favicon): ?>
    <link rel="shortcut icon" href="<?php $options->favicon(); ?>">
  <?php else: ?>
    <link rel="shortcut icon" href="<?php getAssets('assets/images/favicon.webp'); ?>">
  <?php endif; ?>
  <title><?php echo $title; ?></title>
  <!-- IE Out -->
  <script>
    if (false || (!!window.MSInputMethodContext && !!document.documentMode))
      window.location.href =
      'https://support.dmeng.net/upgrade-your-browser.html?referrer=' +
      encodeURIComponent(window.location.href);
  </script>
  <!-- bootstrap -->
  <link rel="stylesheet" href="<?php getAssets('assets/lib/bootstrap4/bootstrap.min.css'); ?>" type="text/css"
    media="all">
  <!-- 自定义css -->
  <link rel="stylesheet" href="<?php getAssets('assets/css/style.min.css?v=' . getVersion()); ?>" type="text/css"
    media="all">
  <!-- iconfont -->
  <link rel="stylesheet" href="<?php getAssets('assets/lib/iconfont/iconfont.min.css?v=' . getVersion()); ?>"
    type="text/css" media="all">
  <style>
    html {
      height: 100%;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background-color: var(--bg-color-body);
      background-image: var(--gradient-45deg);
    }

    .go-wrap {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2.4rem 1rem 1.6rem;
    }

    .go-card {
      position: relative;
      width: 100%;
      max-width: 33rem;
      padding: 1.9rem 1.7rem 1.6rem;
      background: var(--bg-color-main);
      border: var(--border-solid-main);
      border-radius: calc(var(--border-radius-base) * 2);
      box-shadow: var(--shadow-card);
      text-align: center;
      overflow: hidden;
    }

    .go-card::before {
      content: '';
      position: absolute;
      top: -6.5rem;
      left: 50%;
      width: 13rem;
      height: 13rem;
      margin-left: -6.5rem;
      border-radius: 50%;
      background: radial-gradient(circle, var(--color-primary-light-5), transparent 68%);
      pointer-events: none;
    }

    :root[data-theme="dark"] .go-card {
      background: #2e2e2e;
    }

    :root[data-theme="dark"] .go-card::before {
      background: radial-gradient(circle, rgba(106, 161, 255, 0.1), transparent 68%);
    }

    .go-logo {
      position: relative;
      width: 3.4rem;
      height: 3.4rem;
      margin: 0 auto 1rem;
      padding: 0.45rem;
      background: var(--bg-color-main);
      border-radius: var(--border-radius-circle);
      box-shadow: 0 0 0 1px var(--border-color-main), var(--shadow-inset-box);
    }

    .go-logo img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: var(--border-radius-circle);
    }

    .go-eyebrow {
      position: relative;
      margin-bottom: 0.45rem;
      font-size: 0.7rem;
      font-weight: 700;
      letter-spacing: 0.18em;
      color: var(--color-primary-text);
      text-transform: uppercase;
    }

    .go-title {
      position: relative;
      margin: 0 0 0.55rem;
      font-size: 1.35rem;
      font-weight: 800;
      color: var(--font-color-main);
    }

    .go-sub {
      position: relative;
      margin-bottom: 1.15rem;
      font-size: 0.86rem;
      line-height: 1.7;
      color: var(--font-color-muted);
    }

    .go-chips {
      position: relative;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      gap: 0.45rem;
      margin-bottom: 1.1rem;
    }

    .go-chip {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      max-width: 100%;
      padding: 0.28rem 0.7rem;
      overflow: hidden;
      font-size: 0.76rem;
      font-weight: 600;
      color: var(--font-color-main-light);
      background: var(--bg-color-primary);
      border-radius: var(--border-radius-xlarge);
      box-shadow: var(--shadow-inset-box);
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    .go-chip.is-host {
      color: var(--font-color-main);
    }

    .go-chip .iconfont {
      font-size: 0.85rem;
      color: var(--color-primary-text);
    }

    .go-url {
      position: relative;
      display: flex;
      align-items: center;
      gap: 0.55rem;
      width: 100%;
      padding: 0.65rem 0.8rem;
      text-align: left;
      background: var(--bg-color-primary);
      border: var(--border-solid-main);
      border-radius: var(--border-radius-medium);
      box-shadow: var(--shadow-inset-box);
      cursor: pointer;
      transition: border-color 0.25s ease, background-color 0.25s ease;
    }

    .go-url:hover {
      border-color: var(--color-primary-light-3);
    }

    .go-url .iconfont,
    .go-url .go-copy-icon {
      flex: 0 0 auto;
      font-size: 1rem;
      color: var(--color-primary-text);
    }

    .go-url-text {
      flex: 1;
      min-width: 0;
      overflow: hidden;
      font-size: 0.8rem;
      color: var(--font-color-main-light);
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    .go-copy {
      flex: 0 0 auto;
      padding: 0.2rem 0.5rem;
      font-size: 0.72rem;
      font-weight: 700;
      color: var(--color-primary-text);
      background: var(--color-primary-light-5);
      border-radius: var(--border-radius-xlarge);
    }

    :root[data-theme="dark"] .go-copy {
      background: rgba(106, 161, 255, 0.16);
      color: var(--color-primary-light-2);
    }

    .go-alert {
      display: flex;
      align-items: flex-start;
      gap: 0.5rem;
      margin: 1rem 0 0.2rem;
      padding: 0.7rem 0.8rem;
      text-align: left;
      font-size: 0.78rem;
      line-height: 1.65;
      color: var(--hint-warning-text);
      background: var(--hint-warning-bg);
      border-radius: var(--border-radius-medium);
    }

    :root[data-theme="dark"] .go-alert {
      background: rgba(255, 193, 7, 0.12);
    }

    .go-alert .iconfont {
      flex: 0 0 auto;
      margin-top: 0.12rem;
      font-size: 0.95rem;
    }

    .go-count {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      margin-top: 1.15rem;
      font-size: 0.8rem;
      color: var(--font-color-muted);
    }

    .go-ring {
      position: relative;
      width: 2.1rem;
      height: 2.1rem;
    }

    .go-ring svg {
      width: 100%;
      height: 100%;
      transform: rotate(-90deg);
    }

    .go-ring circle {
      fill: none;
      stroke-width: 3;
    }

    .go-ring .go-ring-track {
      stroke: var(--border-color-main);
    }

    .go-ring .go-ring-bar {
      stroke: var(--color-primary);
      stroke-linecap: round;
      stroke-dasharray: 100.5;
      stroke-dashoffset: 0;
      transition: stroke-dashoffset 1s linear;
    }

    .go-ring-num {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      font-weight: 700;
      color: var(--font-color-main);
    }

    .go-cancel {
      padding: 0;
      font-size: 0.78rem;
      color: var(--font-color-muted);
      background: none;
      border: none;
      text-decoration: underline;
      text-underline-offset: 3px;
      cursor: pointer;
    }

    .go-cancel:hover {
      color: var(--color-primary-text);
    }

    .go-actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 0.65rem;
      margin-top: 1.25rem;
    }

    .go-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.35rem;
      min-width: 8.4rem;
      padding: 0.58rem 1.1rem;
      font-size: 0.86rem;
      font-weight: 700;
      border: var(--border-solid-main);
      border-radius: var(--border-radius-medium);
      cursor: pointer;
      transition: transform 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
    }

    .go-btn:hover {
      transform: translateY(-2px);
    }

    .go-btn-ghost {
      color: var(--font-color-main-light);
      background: var(--bg-color-secondary);
      box-shadow: var(--shadow-box-small);
    }

    .go-btn-ghost:hover {
      box-shadow: var(--shadow-card-hover);
    }

    :root[data-theme="dark"] .go-btn-ghost {
      background: #363636;
    }

    .go-btn-primary {
      color: #ffffff !important;
      background: linear-gradient(135deg, var(--color-primary-light-1), var(--color-primary));
      border-color: transparent;
      box-shadow: 0 6px 16px rgba(22, 93, 255, 0.32);
    }

    .go-btn-primary:hover {
      box-shadow: 0 9px 22px rgba(22, 93, 255, 0.4);
    }

    .go-foot {
      flex: 0 0 auto;
      padding: 0 1rem 1.2rem;
      font-size: 12px;
      line-height: 1.8;
      color: var(--font-color-muted);
      text-align: center;
    }

    .go-foot a {
      color: var(--font-color-main-light);
    }

    .go-foot a:hover {
      color: var(--color-primary-text);
    }

    @media (max-width: 575.98px) {
      .go-wrap {
        padding: 1.4rem 0.9rem 1.2rem;
      }

      .go-card {
        padding: 1.5rem 1.15rem 1.3rem;
      }

      .go-title {
        font-size: 1.15rem;
      }

      .go-btn {
        flex: 1 1 0;
        min-width: 0;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .go-ring .go-ring-bar {
        transition: none;
      }

      .go-btn:hover {
        transform: none;
      }
    }
  </style>
</head>

<body>
  <main class="go-wrap">
    <section class="go-card">
      <div class="go-logo">
        <img src="<?php echo htmlspecialchars($goLogo); ?>" alt="<?php $options->title(); ?>">
      </div>

      <p class="go-eyebrow">SAFE REDIRECT</p>
      <h1 class="go-title">即将离开 <?php $options->title(); ?></h1>
      <p class="go-sub">你点击的是一条站外链接。本站无法保证外部页面的内容与安全，请确认目标地址无误后再继续。</p>

      <div class="go-chips">
        <span class="go-chip is-host">
          <i class="iconfont icon-lianjie"></i>
          <?php echo htmlspecialchars($goHost, ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <span class="go-chip">
          <i class="iconfont icon-a-13Csuo"></i>
          <?php echo htmlspecialchars(strtoupper($goScheme), ENT_QUOTES, 'UTF-8'); ?>
          协议</span>
      </div>

      <button type="button" class="go-url" id="copyUrl" title="点击复制链接">
        <i class="iconfont icon-lianjie1"></i>
        <span class="go-url-text"><?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?></span>
        <span class="go-copy" id="copyState">复制</span>
      </button>

      <div class="go-alert">
        <i class="iconfont icon-warning"></i>
        <span>外部网站可能存在钓鱼、诈骗或恶意脚本，请勿在陌生页面输入账号与密码。本站不会记录、也不会代替你做任何授权。</span>
      </div>

      <div class="go-count" id="goCount">
        <span class="go-ring">
          <svg viewBox="0 0 40 40" aria-hidden="true">
            <circle class="go-ring-track" cx="20" cy="20" r="16"></circle>
            <circle class="go-ring-bar" cx="20" cy="20" r="16" id="goRing"></circle>
          </svg>
          <span class="go-ring-num" id="goRingNum">5</span>
        </span>
        <span id="goCountText">秒后自动前往</span>
        <button type="button" class="go-cancel" id="goCancel">取消</button>
      </div>

      <div class="go-actions">
        <button type="button" class="go-btn go-btn-ghost" id="go-back">
          <i class="iconfont icon-shangyiye"></i>
          返回上一页
        </button>
        <button type="button" class="go-btn go-btn-primary" id="go-continue">
          立即前往
          <i class="iconfont icon-icn-qianwang"></i>
        </button>
      </div>
    </section>
  </main>

  <footer class="go-foot">
    <p>
      Copyright © 2024 Designed by <a href="<?php $options->siteUrl(); ?>"><?php $options->title(); ?></a> &Iota;
      <?php $this->options->icp(); ?>
    </p>
  </footer>

  <script>
    (function() {
      var goUrl = <?php echo json_encode($url, $goJsonFlags); ?>;
      var goHome = <?php echo json_encode($goHome, $goJsonFlags); ?>;
      var $copy = document.getElementById('copyUrl');
      var $state = document.getElementById('copyState');
      var $ring = document.getElementById('goRing');
      var $num = document.getElementById('goRingNum');
      var $count = document.getElementById('goCount');
      var ringLength = 100.5;
      var seconds = 5;
      var timer = null;

      function go() {
        if (timer) {
          clearInterval(timer);
        }
        location.replace(goUrl);
      }

      function updateRing() {
        var progress = seconds / 5;
        $ring.style.strokeDashoffset = (ringLength * (1 - progress)).toFixed(2);
        $num.textContent = seconds;
      }

      var $countText = document.getElementById('goCountText');

      function startCountdown() {
        updateRing();
        timer = setInterval(function() {
          seconds -= 1;
          if (seconds <= 0) {
            clearInterval(timer);
            timer = null;
            $num.textContent = '0';
            $ring.style.strokeDashoffset = ringLength.toFixed(2);
            $countText.textContent = '正在前往…';
            go();
            return;
          }
          updateRing();
        }, 1000);
      }

      document.getElementById('go-continue').addEventListener('click', go);
      document.getElementById('go-back').addEventListener('click', function() {
        if (window.history.length > 1) {
          window.history.back();
        } else {
          location.replace(goHome);
        }
      });
      document.getElementById('goCancel').addEventListener('click', function() {
        if (timer) {
          clearInterval(timer);
          timer = null;
        }
        $countText.textContent = '已取消自动跳转';
        $ring.style.strokeDashoffset = ringLength.toFixed(2);
        $num.textContent = '—';
        this.hidden = true;
      });

      $copy.addEventListener('click', function() {
        var text = $copy.querySelector('.go-url-text').textContent;
        var done = function(ok) {
          $state.textContent = ok ? '已复制' : '复制失败';
          $state.style.opacity = '1';
          setTimeout(function() {
            $state.textContent = '复制';
          }, 1800);
        };
        var legacy = function() {
          var area = document.createElement('textarea');
          area.value = text;
          area.setAttribute('readonly', '');
          area.style.position = 'fixed';
          area.style.top = '-9999px';
          document.body.appendChild(area);
          area.select();
          var ok = false;
          try {
            ok = document.execCommand('copy');
          } catch (e) {
            ok = false;
          }
          document.body.removeChild(area);
          done(ok);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(function() {
            done(true);
          }, legacy);
          return;
        }
        legacy();
      });

      startCountdown();
    })();
  </script>
</body>

</html>