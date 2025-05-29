<?php
/**
 * 恶魔之眼
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-5-29 16:36:08
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php if ( ! empty($this->options->sidebarBlock) && in_array('ShowDevilEyes', $this->options->sidebarBlock)): ?>
<!-- 恶魔之眼 -->
<div class="hh-widget mt-3">
  <div class="devil-widget py-2">
    <div class="devil-content d-flex flex-row align-items-center justify-content-around">
      <div class="devil-eye-left px-2">
        <div class="devil-eye-o"></div>
      </div>
      <div class="devil-eye-right px-2">
        <div class="devil-eye-o"></div>
      </div>
    </div>
    <div class="devil-text text-center">小恶魔已监视站点<?php echo getWebsiteAgeInDays($this->options->sitedate); ?>天</div>
  </div>
</div>
<?php endif; ?>