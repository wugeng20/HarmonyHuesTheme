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
<footer class="footer py-2">
  <div class="container d-flex justify-content-between align-items-center">
    <?php if ($footercontent): ?>
      <?php echo $footercontent; ?>
    <?php else: ?>
      <div class="footer-left">
        <div class="footer-copyright py-2">
          <div>© <?php echo generateCopyright($this->options->sitedate); ?> Designed by <a
              href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title() ?>"
              target="_blank"><?php $this->options->title() ?></a> &Iota; <a href="/privacy.html"
              title="<?php $this->options->title() ?>隐私政策" target="_blank">隐私政策</a> &Iota; <a
              href="/copyright-license.html" title="<?php $this->options->title() ?>版权协议" target="_blank">版权协议</a> &Iota;
            <a href="/sitemap.xml" title="<?php $this->options->title() ?>站点地图" target="_blank">站点地图</a> &Iota; <a
              href="<?php $this->options->feedUrl(); ?>" title="<?php $this->options->title() ?>RSS"
              target="_blank">RSS</a>
          </div>
          <div></div>
          <div>Powered
            by <a href="//typecho.org/" title="Typecho" target="_blank">Typecho</a> & <a href="/harmonyhues.html"
              title="Harmony Hues 主题" target="_self">Harmony Hues</a> &Iota; <?php $this->options->icp(); ?> &Iota;
            <a href="https://ipw.cn/ipv6webcheck/?site=<?php $this->options->siteUrl(); ?>" title="本站支持IPv6访问"
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
            <a href="mailto:<?php $this->author('mail'); ?>" title="mail" target="_blank"><i
                class="iconfont icon-youxiang"></i></a>
            <a href="/feed/" title="SRR" target="_blank"><i class="iconfont icon-rss"></i></a>
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
  <script type="text/javascript" src="<?php getAssets('assets/lib/prism/prism.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php getAssets('assets/js/codecopy.min.js?v=' . getVersion()); ?>"></script>
  <script type="text/javascript">
    var _ARTICLE_URL = "<?php $this->permalink(); ?>";
    var _ARTICLE_NAME = "<?php $this->title(); ?>";
    var _ARTICLE_COVER_URL = "<?php echo getImgLink($this); ?>";
    var _WXQR = "<?php $this->options->wechatQrCode(); ?>";
    var _ZFBQR = "<?php $this->options->alipayQrCode(); ?>";
  </script>
  <script type="text/javascript" src="<?php getAssets('assets/lib/viewimage/view-image.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php getAssets('assets/lib/html-to-image/html-to-image.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php getAssets('assets/lib/jquery/jquery.qrcode.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php getAssets('assets/js/article-toc.min.js?v=' . getVersion()); ?>"></script>
  <script type="text/javascript" src="<?php getAssets('assets/js/comment-tools.min.js?v=' . getVersion()); ?>"></script>
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
<?php $this->options->customBodyEnd() ?>
<?php $this->options->zztj(); ?>
</body>

</html>