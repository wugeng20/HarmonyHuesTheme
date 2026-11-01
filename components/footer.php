<?php

/**
 * 底部footer主体内容
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
$footercontent = $this->options->footerContent; // 自定义底部内容
/**
 * 生成动态版权信息
 */
function generateCopyright($siteCreationDate = '')
{
  $currentYear = date('Y');

  if (empty($siteCreationDate) || ! strtotime($siteCreationDate)) {
    return "$currentYear";
  }

  $creationYear = date('Y', strtotime($siteCreationDate));

  if ($creationYear == $currentYear) {
    return "$creationYear";
  }

  return "$creationYear-$currentYear";
}

?>
<footer class="footer py-2 <?php echo $this->options->navStyle == 'mini' ? 'footer-ui-two' : 'footer-ui-one'; ?>">
  <div class="container d-flex justify-content-between align-items-center">
    <?php if ($footercontent): ?>
      <?php echo $footercontent; ?>
    <?php else: ?>
      <div class="footer-left">
        <div class="footer-copyright py-2">
          <div>© <?php echo generateCopyright($this->options->sitedate); ?> Designed by <a
              href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title() ?>"
              target="_blank"><?php $this->options->title() ?></a> &Iota; <a
              href="<?php echo getSitePath('privacy.html'); ?>" title="<?php $this->options->title() ?>隐私政策"
              target="_blank">隐私政策</a> &Iota; <a href="<?php echo getSitePath('copyright-license.html'); ?>"
              title="<?php $this->options->title() ?>版权协议" target="_blank">版权协议</a> &Iota;
            <a href="<?php echo getSitePath('sitemap.xml'); ?>" title="<?php $this->options->title() ?>站点地图"
              target="_blank">站点地图</a> &Iota; <a href="<?php $this->options->feedUrl(); ?>"
              title="<?php $this->options->title() ?>RSS" target="_blank">RSS</a>
          </div>
          <div></div>
          <div>Powered
            by <a href="//typecho.org/" title="Typecho" target="_blank">Typecho</a> & <a
              href="<?php echo getSitePath('harmonyhues.html'); ?>" title="Harmony Hues 主题" target="_self">Harmony
              Hues</a><?php $this->options->icp(); ?> &Iota;
            <a href="https://ipv6test.wcode.net/?q=<?php $this->options->siteUrl(); ?>" title="本站支持IPv6访问"
              target='_blank'>本站支持IPv6访问</a> &Iota; <a
              href="https://creativecommons.org/licenses/by-nc-sa/4.0/deed.zh-hans" title="CC BY-NC-SA 4.0"
              target="_blank">CC-BY-NC-SA 4.0</a>
          </div>
        </div>
      </div>
      <div class="footer-right d-flex flex-column">
        <div class="d-flex align-items-center justify-content-center p-1 theme-toggle">
          <button type="button" title="浅色模式" class="px-2 py-1 active"><i class="iconfont icon-qiansemoshi"></i></button>
          <button type="button" title="跟随系统" class="px-2 py-1"><i class="iconfont icon-gensuixitong"></i></button>
          <button type="button" title="深色模式" class="px-2 py-1"><i class="iconfont icon-shensemoshi"></i></button>
        </div>
        <div class="d-flex align-items-center justify-content-center mt-2 social-info-list" style="gap: 0.5rem;">
          <?php if ($this->options->socialInfo): ?>
            <?php echo socialInfo(); ?>
          <?php else: ?>
            <a class="d-flex align-items-center justify-content-center" href="mailto:<?php $this->author('mail'); ?>" title="mail" target="_blank"><i
                class="iconfont icon-youxiang"></i></a>
            <a class="d-flex align-items-center justify-content-center" href="<?php echo getSitePath('feed/'); ?>" title="SRR" target="_blank"><i class="iconfont icon-rss"></i></a>
          <?php endif; ?>
        </div>
      </div>
  </div>
<?php endif; ?>
</footer>
<!--手机导航栏-->
<div id="mobile-nav"></div>
<!--搜索框-->
<?php $this->need('components/search.php'); ?>
<?php if ($this->is('post') || $this->is('page')): ?>
  <!-- 文章或者页面 -->
  <?php
  // prism.min.js 为 576KB 的全量语言包，只有正文真的包含代码块时才需要
  $hasCodeBlock = (bool) preg_match('/<pre[\s>]|<code[\s>]|```|\[code/i', (string) $this->content);
  ?>
  <?php if ($hasCodeBlock): ?>
    <script type="text/javascript" src="<?php getAssets('assets/lib/prism/prism.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php getAssets('assets/js/codecopy.min.js?v=' . getVersion()); ?>"></script>
  <?php endif; ?>
  <script type="text/javascript">
    <?php
    // 统一用 JSON 编码输出，避免标题/URL 中的引号把内联脚本截断
    $jsValue = function ($value) {
      return json_encode((string) $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    };
    ?>
    var _ARTICLE_URL = <?php echo $jsValue($this->permalink); ?>;
    var _ARTICLE_NAME = <?php echo $jsValue($this->title); ?>;
    var _ARTICLE_COVER_URL = <?php echo $jsValue(getImgLink($this)); ?>;
    var _WXQR = <?php echo $jsValue($this->options->wechatQrCode); ?>;
    var _ZFBQR = <?php echo $jsValue($this->options->alipayQrCode); ?>;
  </script>
  <script type="text/javascript" src="<?php getAssets('assets/lib/viewimage/view-image.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php getAssets('assets/lib/html-to-image/html-to-image.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php getAssets('assets/lib/jquery/jquery.qrcode.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php getAssets('assets/js/article-toc.min.js?v=' . getVersion()); ?>"></script>
  <script type="text/javascript">
    // 表情包清单由 assets/emoji 目录自动扫描生成，见 core/function.php:getEmojiPacks()
    window.HH_EMOJI_PACKS =
      <?php echo json_encode(getEmojiPacks(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  </script>
  <script type="text/javascript" src="<?php getAssets('assets/js/comment-tools.min.js?v=' . getVersion()); ?>"></script>
<?php endif; ?>
<?php if (! needsSvgIcon($this)): ?>
  <script type="text/javascript">
    // 未加载 svg-icon.min.js 的页面提供空实现：harmonyhues 会无条件调用 showSvg()
    window.showSvg = window.showSvg || function() {};
  </script>
<?php endif; ?>
<script type="text/javascript" src="<?php getAssets('assets/js/harmonyhues.min.js?v=' . getVersion()); ?>"></script>
<script type="text/javascript">
  <?php $this->options->customScript(); ?>
  console.log("%c © Harmony Hues主题 | Version: <?php echo getVersion(); ?> | https://biibii.cn",
    "color:#000; background: linear-gradient(270deg, #FFB6C1, #FFF0F5, #FFC0CB); padding: 8px 15px; border-radius: 8px");
  console.log("%c © BIIBII.CN | 内容均为原创或授权转发，请勿盗用",
    "color:#000; background: linear-gradient(270deg, #87CEFA, #F0F8FF, #87CEEB); padding: 8px 15px; border-radius: 8px");
  console.log("%c页面加载耗时：<?php endCountTime(); ?>",
    "color:#fff; background: linear-gradient(270deg, #986fee, #8695e6, #68b7dd, #18d7d3); padding: 8px 15px; border-radius: 8px"
  );
</script>
<?php
// 主题版本更新提示：浏览器缓存的主题版本低于当前版本时，提示刷新静态资源
$themeVersion = getVersion();
?>
<script type="text/javascript">
  window.HH_THEME_VERSION =
    <?php echo json_encode($themeVersion, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<div class="hh-update-tip" id="hh-update-tip" hidden>
  <span class="hh-update-tip-icon" aria-hidden="true"><i class="iconfont icon-shuaxin"></i></span>
  <div class="hh-update-tip-body">
    <p class="hh-update-tip-title">主题已更新到 v<span id="hh-update-tip-ver"></span></p>
    <p class="hh-update-tip-desc">样式与脚本有改动，刷新一下即可加载新版本</p>
  </div>
  <button type="button" class="hh-update-tip-btn" id="hh-update-tip-refresh">刷新缓存</button>
  <button type="button" class="hh-update-tip-close" id="hh-update-tip-close" aria-label="关闭">&times;</button>
</div>
<script type="text/javascript">
  (function() {
    var STORE_KEY = 'hh_theme_version';
    var current = window.HH_THEME_VERSION || '';
    var tip = document.getElementById('hh-update-tip');
    if (!current || !tip) return;

    var readStore = function() {
      try {
        return localStorage.getItem(STORE_KEY) || '';
      } catch (e) {
        return '';
      }
    };
    var writeStore = function(value) {
      try {
        localStorage.setItem(STORE_KEY, value);
      } catch (e) {}
    };
    // 逐段比较版本号，返回 cached 是否低于 current
    var isOlder = function(cached, target) {
      var a = String(cached).split('.');
      var b = String(target).split('.');
      var len = Math.max(a.length, b.length);
      for (var i = 0; i < len; i++) {
        var x = parseInt(a[i], 10) || 0;
        var y = parseInt(b[i], 10) || 0;
        if (x !== y) return x < y;
      }
      return false;
    };

    var stored = readStore();
    if (!stored) {
      // 首次访问：静默记录当前版本
      writeStore(current);
      return;
    }
    if (!isOlder(stored, current)) return;

    document.getElementById('hh-update-tip-ver').textContent = current;
    tip.hidden = false;

    document.getElementById('hh-update-tip-refresh').addEventListener('click', function() {
      writeStore(current);
      // 给当前地址加时间戳，并清掉已被缓存的版本参数，确保拉到新的 HTML 与静态资源
      var url = new URL(window.location.href);
      url.searchParams.set('hhc', String(Date.now()));
      window.location.replace(url.toString());
    });

    document.getElementById('hh-update-tip-close').addEventListener('click', function() {
      // 关闭后视为已读，下次版本再变才会提示
      writeStore(current);
      tip.hidden = true;
    });
  })();
</script>
<?php $this->options->customBodyEnd() ?>
<?php $this->options->zztj(); ?>
</body>

</html>