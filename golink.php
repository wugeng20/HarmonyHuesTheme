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
  header('Location: /404.php');
  exit;
}

$t_url = $_GET['target'];

// 判断取值是否加密
if ($t_url === base64_encode(base64_decode($t_url))) {
  $t_url = base64_decode($t_url);
}

// 防止 XSS
$t_url = htmlspecialchars($t_url, ENT_QUOTES, 'UTF-8');

// 对取值进行网址校验和判断
$allowed_protocols = array('http', 'https', 'thunder', 'qqdl', 'ed2k', 'Flashget', 'qbrowser');
$pattern = '/^(' . implode('|', $allowed_protocols) . '):\/\//i';

if (preg_match($pattern, $t_url, $matches)) {
  $url = $t_url;
} elseif (preg_match('/\./i', $t_url)) {
  $url = 'http://' . $t_url;
} else {
  header('Location: /404.php');
  exit;
}

// 替换 &amp; 为 &
$url = str_replace('&amp;', '&', $url);

// 设置页面标题
$title = $options->title . ' - 安全中心';
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
  <!-- jQuery -->
  <script type="text/javascript" src="<?php getAssets('assets/lib/jquery/jquery.min.js'); ?>"></script>
  <style>
    body {
      height: 100vh;
      overflow: hidden;
    }

    .go-main {
      margin-top: 10rem;
    }

    .go-body {
      margin: 0 auto;
      max-width: 100%;
      width: 50rem;
    }

    .go-logo img {
      width: 4rem;
    }

    .go-url {
      position: relative;
      color: var(--font-color-main-transparent);
      background-color: var(--bg-color-primary);
      border-radius: var(--border-radius-base);
      transition: var(--transition-ease-all);
    }

    .go-url:hover {
      background-color: var(--border-color-main);
    }

    .go-url-icon {
      position: absolute;
      left: 0.5rem;
      line-height: 1;
    }

    .go-button-item {
      gap: 1rem;
    }

    .go-button {
      font-size: 0.9rem;
      background-image: var(--gradient-45deg);
      box-shadow: var(--shadow-box-main);
      border: var(--border-solid-main);
      border-radius: var(--border-radius-medium);
      background-clip: padding-box;
      padding: 0.5rem 1rem;
      transition: var(--transition-ease-all);
    }

    .go-button:hover {
      box-shadow: var(--shadow-inset-box);
    }

    footer {
      font-size: 13px;
      --font-color-main: var(--font-color-main-light);
      color: var(--font-color-main-light);
      text-align: center;
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      box-sizing: border-box;
    }

    @media (min-width: 1024px) {
      .go-main {
        margin-top: 10%;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="go-main">
      <div class="card go-body p-4">
        <div class="go-logo text-center mb-4">
          <img src="<?php getAssets('assets/images/favicon.webp'); ?>" alt="<?php $options->title(); ?>">
        </div>
        <div class="go-content text-center">
          <h4 class="go-title">即将离开 <?php $options->title(); ?></h4>
          <div class="go-msg">
            <span><i class="fa fa-exclamation-triangle"
                aria-hidden="true"></i>你访问的网站可能包含未知的安全风险，如需继续访问，请手动复制链接访问，并注意保护帐号和隐私信息。</span>
          </div>
          <div id="copyUrl" class="go-url my-4 py-2">
            <div class="go-url-icon">
              <svg class="cp-icon" viewBox="0 0 1024 1024" p-id="39835" width="20" height="20">
                <path
                  d="M172.9 536.9V201.8c0-16.5 13.5-29.9 30.2-29.9h448.8c16.7 0 30.2 13.4 30.2 29.9V537c0 16.5-13.5 29.9-30.2 29.9h-153c-15 0-27.2 12.2-27.2 27.2v5.5c0 15 12.2 27.2 27.2 27.2H682c33.3 0 60.4-26.8 60.4-59.9v-395c0-33.1-27-59.9-60.4-59.9H142.7c-16.7 0-30.2 13.4-30.2 29.9v424.9c0 33.1 27 59.9 60.4 59.9h30.2c15 0 27.2-12.2 27.2-27.2V594c0-15-12.2-27.2-27.2-27.2-16.7 0.1-30.2-13.3-30.2-29.9z"
                  fill="var(--font-color-muted)" p-id="39836"></path>
                <path
                  d="M852.1 397.3h-30.2c-13.3 0-24.2 10.8-24.2 24.2V433c0 13.3 10.8 24.2 24.2 24.2 16.7 0 30.2 13.4 30.2 29.9v335.2c0 16.5-13.5 29.9-30.2 29.9H370.1c-16.7 0-30.2-13.4-30.2-29.9V487.1c0-16.5 13.5-29.9 30.2-29.9h159c13.3 0 24.2-10.8 24.2-24.2v-11.5c0-13.3-10.8-24.2-24.2-24.2H339.9c-33.3 0-60.4 26.8-60.4 59.9v395c0 33.1 27 59.9 60.4 59.9h512.2c33.3 0 60.4-26.8 60.4-59.9v-395c0-33.1-27-59.9-60.4-59.9z"
                  fill="var(--font-color-muted)" p-id="39837"></path>
              </svg>
            </div>
            <span><?php echo $url; ?></span>
          </div>
        </div>
        <hr>
        <div class="go-button-item d-flex flex-wrap justify-content-center align-items-center">
          <button class="go-button" onclick="location.replace('<?php $options->siteUrl(); ?>')">返回首页</button>
          <button class="btn btn-card go-button" onclick="location.replace('<?php echo $url; ?>')">继续访问</button>
        </div>
      </div>
    </div>
  </div>
  <footer class="p-2">
    <p>
      Copyright © 2024 Designed by <a href="<?php $options->siteUrl(); ?>"><?php $options->title(); ?>-安全中心</a> &Iota;
      <?php $this->options->icp(); ?>
    </p>
  </footer>
  <script>
    $('#copyUrl').on('click', function() {
      const self = $(this);
      const textToCopy = self.find('span').text();
      const tempTextarea = $('<textarea></textarea>');
      $('body').append(tempTextarea);
      tempTextarea.val(textToCopy).select();
      try {
        document.execCommand('copy');
        self.css('border', '1px solid var(--success)');
      } catch (err) {
        console.error('Unable to copy text', err);
        self.css('border', '1px solid var(--red)');
      }
      tempTextarea.remove();
      setTimeout(function() {
        self.css('border', '');
      }, 2000);
    });
  </script>
</body>

</html>