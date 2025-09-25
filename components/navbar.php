<?php

/**
 * 顶部导航栏
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-1-6 20:05:37
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}
?>
<header id="navbar" class="navbar p-2<?php echo $this->options->navStyle == 'mini' ? ' nav-ui-two' : ' nav-ui-one'; ?>">
  <div class="container navbar-box p-1 p-md-2">
    <div class="navbar-logo flex-shrink-0">
      <a id="logo" href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title() ?>">
        <?php if ($this->options->logoUrl): ?>
          <img src="<?php $this->options->logoUrl(); ?>" alt="<?php $this->options->title() ?>"
            class="d-inline-block logo-light" />
        <?php else: ?>
          <span><?php $this->options->title() ?></span>
        <?php endif; ?>
      </a>
    </div>
    <nav class="d-none d-md-block nav-inner">
      <ul class="d-flex flex-column flex-md-row nav-menu m-0">
        <li class="nav-item"><a class="nav-a <?php echo $this->is('index') ? 'active' : '' ?>"
            href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title(); ?>">首页</a></li>
        <!-- 默认导航 -->
        <?php $this->widget('Widget_Metas_Category_List')->to($category); ?>
        <?php while ($category->next()): if ($category->levels === 0): ?>
            <?php $children = $category->getAllChildren($category->mid); ?>
            <?php if (empty($children)): ?>
              <li><a class="<?php echo $this->is('category', $category->slug) ? 'active' : '' ?>"
                  href="<?php $category->permalink(); ?>" target="_self"
                  title="<?php $category->name(); ?>"><?php $category->name(); ?></a></li>
            <?php else: ?>
              <li class="nav-item">
                <a class="nav-a <?php echo (! $this->is('index') && isParentActive($this->category, $category, $children)) ? 'active' : ''; ?>"
                  href="<?php $category->permalink(); ?>" title="<?php $category->name(); ?>"
                  target="_self"><?php $category->name(); ?><i class="iconfont nav-icon icon-xiala"></i></a>
                <div class="pt-md-4 sub-menu">
                  <ul class="d-md-flex flex-md-wrap p-md-2">
                    <?php foreach ($children as $mid) { ?>
                      <?php $child = $category->getCategory($mid); ?>
                      <li class="p-2 <?php echo $this->is('category', $child['slug']) ? ' active' : ''; ?>">
                        <a href="<?php echo $child['permalink'] ?>" target="_self"
                          title="<?php echo $child['name']; ?>"><?php echo $child['name']; ?></a>
                      </li>
                    <?php } ?>
                  </ul>
                </div>
              </li>
          <?php endif;
          endif; ?>
        <?php endwhile; ?>
        <!-- 添加自定义菜单 -->
        <?php echo getCustomMenu($this->request->getRequestUrl()); ?>
      </ul>
    </nav>
    <div class="d-flex flex-row navbar-icon">
      <?php if (! empty($this->options->indexBlock) && in_array('ShowTravelling', $this->options->indexBlock)): ?>
        <div>
          <a class="nav-top-item" href="https://www.travellings.cn/go.html" target="_blank" title="开往-友链接力"><i
              class="iconfont icon-huoche"></i></a>
        </div>
      <?php endif; ?>
      <div>
        <a class="nav-top-item" href="<?php $this->options->siteUrl(); ?>?random=true" target="_self"
          title="随机进入一篇文章"><i class="iconfont icon-random"></i></a>
      </div>
      <div id="search-btn" title="站内搜索"><i class="iconfont icon-search"></i></div>
      <div class="flex-column align-items-center justify-content-center back-to-top" style="display: none;">
        <a id="nav-backtop" class="nav-top-item" href="javascript:void(0);" role="button" title="回到顶部">
          <span class="top-to-icon">回到顶部</span>
          <span class="top-to-text">0</span>
        </a>
      </div>
      <div id="menu-line" class="d-block d-md-none" title="手机端菜单栏"><i class="iconfont icon-caidan"></i></div>
    </div>
  </div>
</header>