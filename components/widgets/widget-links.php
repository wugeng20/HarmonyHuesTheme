<?php
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

// 只在首页显示，且后台勾选了「底部-友情链接」
if (! $this->is('index')) {
  return;
}
if (empty($this->options->indexBlock) || ! in_array('ShowLinks', $this->options->indexBlock)) {
  return;
}

// 后台配置提示：仅登录管理员可见，避免向访客暴露配置信息
$linksIsAdmin = false;
if (class_exists('\Widget\User')) {
  try {
    $linksIsAdmin = (bool) \Widget\User::alloc()->hasLogin();
  } catch (\Throwable $e) {
    $linksIsAdmin = false;
  }
}

$linksHint = function ($linksHintTitle, $linksHintDesc, $linksHintLink = true) use ($linksIsAdmin) {
  // 访客看到中性文案，管理员额外看到具体原因与「去设置」入口
  if (! $linksIsAdmin) {
    $linksHintTitle = '友情链接正在整理中';
    $linksHintDesc  = '稍后再来看看，或者先去看看别的文章。';
    $linksHintLink  = false;
  }
  $linksAdminUrl = rtrim((string) Helper::options()->adminUrl, '/') . '/options-theme.php';
?>
  <div class="hh-widget mb-3">
    <section class="links-widget links-widget--hint">
      <div class="links-widget-head d-flex">
        <div class="links-widget-heading d-flex">
          <span class="links-widget-icon"><i class="iconfont icon-lianjie"></i></span>
          <span class="links-widget-title">友情链接</span>
          <span class="links-widget-sub">待配置</span>
        </div>
      </div>
      <div class="links-widget-hint d-flex">
        <span class="links-widget-hint-icon d-flex align-items-center justify-content-center"><i
            class="iconfont icon-info"></i></span>
        <div class="links-widget-hint-body">
          <b><?php echo htmlspecialchars($linksHintTitle, ENT_QUOTES, 'UTF-8'); ?></b>
          <p><?php echo htmlspecialchars($linksHintDesc, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <?php if ($linksHintLink): ?>
          <a class="links-widget-tool links-widget-hint-link"
            href="<?php echo htmlspecialchars($linksAdminUrl, ENT_QUOTES, 'UTF-8'); ?>">
            <i class="iconfont icon-qianwang"></i><span>去设置</span>
          </a>
        <?php endif; ?>
      </div>
    </section>
  </div>
<?php
};

$linksPageCid = (int) $this->options->linksCid;
if ($linksPageCid <= 0) {
  $linksHint('还没有配置友情链接页面', '请在「主题设置 → 首页内容显示（非必填）」里填写友情链接独立页面的 CID。');
  return;
}

$linksDb = Typecho_Db::get();
$linksPageRow = $linksDb->fetchRow(
  $linksDb->select('cid', 'title', 'slug', 'created', 'type')
    ->from('table.contents')
    ->where('cid = ?', $linksPageCid)
    ->where('type = ?', 'page')
    ->limit(1)
);
if (! $linksPageRow) {
  $linksHint('找不到这个页面', 'CID ' . $linksPageCid . ' 对应的独立页面不存在，或它不是「独立页面」类型。');
  return;
}

$linksPageText = $linksDb->fetchObject(
  $linksDb->select('text')
    ->from('table.contents')
    ->where('cid = ?', $linksPageCid)
    ->limit(1)
);
$linksContent = $linksPageText ? (string) $linksPageText->text : '';

// 解析 [Links title="" url="" avatar="" desc="" group="" status="" /]
$linksDefaults = array(
  'title'  => '',
  'url'    => '',
  'avatar' => '',
  'desc'   => '',
  'group'  => '网上邻居',
  'status' => '正常',
);

$linksItems = array();
$linksSeen  = array();

preg_match_all('/\[Links\s+([^\]]+)\s*\/\]/', $linksContent, $linksMatches, PREG_SET_ORDER);
foreach ($linksMatches as $linksMatch) {
  $linksItem = $linksDefaults;

  preg_match_all('/(\w+)=(["\'])(.*?)\2|(\w+)=([^\s]+)/', $linksMatch[1], $linksAttrs, PREG_SET_ORDER);
  foreach ($linksAttrs as $linksAttr) {
    if (! empty($linksAttr[1])) {
      $linksKey   = strtolower($linksAttr[1]);
      $linksValue = $linksAttr[3];
    } else {
      $linksKey   = strtolower($linksAttr[4]);
      $linksValue = $linksAttr[5];
    }
    if (array_key_exists($linksKey, $linksDefaults)) {
      $linksItem[$linksKey] = htmlspecialchars_decode((string) $linksValue, ENT_QUOTES);
    }
  }

  $linksUrl = trim($linksItem['url']);
  if ($linksUrl === '' || isset($linksSeen[$linksUrl])) {
    continue;
  }

  // 失联站点不在首页展示
  $linksStatus = trim($linksItem['status']);
  if ($linksStatus !== '' && $linksStatus !== '正常') {
    continue;
  }

  $linksSeen[$linksUrl] = true;

  if (trim($linksItem['title']) === '') {
    $linksHost = parse_url($linksUrl, PHP_URL_HOST);
    $linksItem['title'] = $linksHost ? $linksHost : $linksUrl;
  }

  $linksItems[] = $linksItem;
}

if (empty($linksItems)) {
  $linksHint(
    '这个页面里没有解析到友情链接',
    'CID ' . $linksPageCid . ' 页面内没有找到有效的 [Links ... /] 短代码。',
    false
  );
  return;
}

// 少于 5 条时不跑马灯：直接静态平铺，避免空转/节奏太快的滚动
$linksUseMarquee = count($linksItems) >= 5;

shuffle($linksItems);
$linksShow = $linksUseMarquee ? array_slice($linksItems, 0, min(8, count($linksItems))) : $linksItems;
$linksShowCount = count($linksShow);

$linksPageUrl      = getPermalinkFromRow($linksPageRow);
$linksFallbackImg  = getAssets('assets/images/blogimg.webp', false); // 头像默认图
$linksEsc          = function ($value) {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

// 单张卡片（两组共用）
$linksCardHtml = function ($linksItem) use ($linksEsc, $linksFallbackImg) {
  $linksUrl   = $linksEsc($linksItem['url']);
  $linksTitle = $linksEsc($linksItem['title']);
  $linksDesc  = $linksEsc($linksItem['desc'] !== '' ? $linksItem['desc'] : $linksItem['group']);
  $linksImg   = $linksEsc($linksItem['avatar'] !== '' ? $linksItem['avatar'] : $linksFallbackImg);

  return '<a class="links-widget-card d-flex" href="' . $linksUrl . '" target="_blank" rel="noopener nofollow" title="' . $linksTitle . '">'
    . '<span class="links-widget-avatar d-flex">'
    . '<img src="' . $linksImg . '" alt="' . $linksTitle . '" loading="lazy"'
    . ' onerror="this.src=\'' . $linksEsc($linksFallbackImg) . '\';this.onerror=null;" />'
    . '</span>'
    . '<span class="links-widget-info d-flex"><b>' . $linksTitle . '</b><em>' . $linksDesc . '</em></span>'
    . '</a>';
};
?>
<div class="hh-widget mb-3">
  <section class="links-widget p-2<?php echo $linksUseMarquee ? '' : ' links-widget--static'; ?>"
    <?php echo $linksUseMarquee ? ' data-links-widget' : ''; ?>>
    <div class="links-widget-head d-flex">
      <div class="links-widget-heading d-flex">
        <span class="links-widget-icon"><i class="iconfont icon-lianjie"></i></span>
        <span class="links-widget-title">友情链接</span>
        <span
          class="links-widget-sub"><?php echo $linksUseMarquee ? '随机 ' . $linksShowCount . ' 位邻居' : '共 ' . $linksShowCount . ' 位邻居'; ?></span>
      </div>
      <div class="links-widget-tools d-flex">
        <?php if ($linksUseMarquee): ?>
          <button type="button" class="links-widget-tool" data-links-shuffle>
            <i class="iconfont icon-shuaxin"></i><span>换一批</span>
          </button>
        <?php endif; ?>
        <?php if ($linksPageUrl !== ''): ?>
          <a class="links-widget-tool" href="<?php echo $linksEsc($linksPageUrl); ?>">
            <i class="iconfont icon-qianwang"></i><span>全部邻居</span>
          </a>
        <?php endif; ?>
      </div>
    </div>
    <div class="links-widget-marquee" data-links-marquee>
      <div class="links-widget-track d-flex">
        <div class="links-widget-group d-flex" data-links-group>
          <?php foreach ($linksShow as $linksItem): ?>
            <?php echo $linksCardHtml($linksItem); ?>
          <?php endforeach; ?>
        </div>
        <?php if ($linksUseMarquee): ?>
          <div class="links-widget-group d-flex" data-links-group aria-hidden="true">
            <?php foreach ($linksShow as $linksItem): ?>
              <?php echo $linksCardHtml($linksItem); ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>