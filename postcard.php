<?php

/**
 * 明信片留言
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-1-20 12:48:15
 * @package custom
 */
?>
<?php if (! defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}
?>
<?php
global $authorName;
$authorName = $this->author->screenName;

if (! function_exists('pcGetToName')) {
  /**
   * 取某条留言所回复对象的昵称（纯文本）
   */
  function pcGetToName($coid)
  {
    $db = Typecho_Db::get();
    $prow = $db->fetchRow($db->select('parent')->from('table.comments')->where('coid = ? AND status = ?', $coid, 'approved'));
    if (! $prow || (int) $prow['parent'] === 0) {
      return '';
    }
    $arow = $db->fetchRow($db->select('author')->from('table.comments')->where('coid = ?', (int) $prow['parent']));
    return $arow ? (string) $arow['author'] : '';
  }
}
$this->need('components/header.php');
?>

<style>
  @font-face {
    font-family: "瑞美加张清平硬笔行书";
    src: url("<?php getAssets('assets/fonts/瑞美加张清平硬笔行书.ttf'); ?>");
  }

  .postcard-cover {
    background: linear-gradient(to right, #ccc 1px, transparent 1px), linear-gradient(to bottom, #ccc 1px, transparent 1px);
    background-size: 1.5rem 1.5rem;
    overflow: hidden;
    border-radius: var(--border-radius-small);
  }

  .postcard-cover-text {
    font-family: "瑞美加张清平硬笔行书";
    font-weight: bold;
    font-size: 2rem;
  }

  .postcard-cover-bg {
    position: relative;
  }

  .postcard-cover-bg .postcard-cover-name {
    position: absolute;
    top: 0;
    width: 100%;
    height: 100%;
  }

  .postcard-cover-bg .postcard-cover-text {
    color: #fff;
    font-size: 2.2rem;
  }

  .postcard-box {
    position: relative;
    display: block;
    flex: 1 1 auto;
    width: 100%;
    color: var(--font-color-main);
    border: var(--border-solid-main);
    box-shadow: var(--shadow-box-main);
    background: var(--gradient-45deg);
    background-clip: padding-box;
    border-radius: var(--border-radius-small);
  }

  .postcard-box:hover {
    box-shadow: var(--shadow-box-hover);
  }

  .postcard-code .postcard-code-item {
    display: inline-block;
    width: 1.5rem;
    height: 1.5rem;
    text-align: center;
    border: 1px solid var(--font-color-main-light);
    font-family: "瑞美加张清平硬笔行书";
  }

  .postcard-to,
  .postcard-from {
    font-family: "瑞美加张清平硬笔行书";
    font-weight: bold;
    border-bottom: 1px solid #ccc;
  }

  .postcard-from {
    gap: 0.5rem;
    border: none;
  }

  .postcard-content {
    --line-height: 1.5rem;
    background:
      repeating-linear-gradient(to bottom,
        transparent,
        transparent calc(var(--line-height) - 1px),
        #ccc calc(var(--line-height) - 1px),
        #ccc var(--line-height));
    min-height: 4rem;
  }

  .postcard-content.postcard-text {
    text-indent: 2em;
    font-family: "瑞美加张清平硬笔行书";
    font-weight: bold;
  }

  .postcard-content p {
    margin: 0;
  }

  .postcard-content .emoji-image {
    width: 1.5rem;
    height: 1.5rem;
  }

  .postcard-stamp {
    position: relative;
    float: right;
    width: 100px;
    height: 100px;
    background: #fff;
    mask:
      radial-gradient(circle at 4px, transparent 4px, red 5px),
      radial-gradient(circle at 50% 4px, red 4px, transparent 5px);
    mask-position: -5px, 50% -5px;
    mask-size: 100% 20px, 20px 100%;
    mask-composite: subtract;
    -webkit-mask:
      radial-gradient(circle at 4px, transparent 4px, red 5px),
      radial-gradient(circle at 50% 4px, red 4px, transparent 5px);
    -webkit-mask-position: -5px, 50% -5px;
    -webkit-mask-size: 100% 20px, 20px 100%;
    -webkit-mask-composite: source-out;
  }

  .postcard-title {
    text-align: center;
    font-size: 3rem;
  }

  .comment-reply-list:hover {
    color: var(--color-primary) !important;
  }

  .comment-reply-link .iconfont,
  .comment-reply-list .iconfont {
    font-size: 1rem;
  }

  .comment-reply-list {
    background-color: transparent;
  }

  @media (max-width: 1024px) {
    .postcard-cover-text {
      font-size: 1rem;
    }

    .svg-name svg {
      width: 2rem;
    }

    .postcard-cover-bg .postcard-cover-text {
      font-size: 1.5rem;
      text-align: center;
    }

    .postcard-title {
      font-size: 2rem;
    }
  }

  /* 评论 */
  .postcard-comments-list>.comment-list {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
  }

  .postcard-comments-list>.comment-list>.postcard-comment-parent .comment-list {
    display: none;
  }

  .postcard-parent-box.active {
    position: absolute;
    width: 100%;
    z-index: 2;
    background-image: var(--widget-bg-gradient);
    border-radius: var(--border-radius-small);
    box-shadow: var(--shadow-box-small);
  }

  .postcard-item .respond form {
    position: fixed;
    z-index: 998;
    max-width: 50rem;
    width: calc(-20px + 100vw);
    opacity: 0;
    top: 40%;
    left: 50%;
    padding: 1rem;
    transform: translate(-50%, -50%);
    border: var(--border-solid-main);
    border-radius: var(--border-radius-medium);
    box-shadow: var(--shadow-box-main);
    background: var(--gradient-45deg);
    background-clip: padding-box;
    -webkit-transition: all 0.3s ease;
    transition: all 0.3s ease;
  }

  .postcard-item .open form {
    opacity: 1;
    top: 45%;
  }

  .comment-list,
  .postcard-animation {
    opacity: 0;
    animation: fade-in-top 0.5s 0.3s forwards;
    -webkit-animation: fade-in-top 0.5s 0.3s forwards;
  }

  .postcard-comments-list .comment-pagination .iconfont {
    font-size: 0.9rem;
  }

  /* ===== 明信片 hero：邮票 / 邮戳 ===== */
  .pc-hero {
    display: grid;
    grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr);
    overflow: hidden;
    padding: 0;
  }

  .pc-hero-media {
    position: relative;
    min-height: 20rem;
    overflow: hidden;
  }

  .pc-hero-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .pc-hero-media::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0, 0, 0, .18), rgba(0, 0, 0, .06) 40%, rgba(0, 0, 0, .5));
  }

  .pc-hero-caption {
    position: absolute;
    left: 1.1rem;
    bottom: 1rem;
    z-index: 2;
    color: #fff;
    font-family: "瑞美加张清平硬笔行书";
    font-size: 1.5rem;
    letter-spacing: .12em;
    text-shadow: 0 2px 10px rgba(0, 0, 0, .45);
  }

  .pc-postmark {
    position: absolute;
    top: 1.1rem;
    right: 1.1rem;
    z-index: 2;
    display: grid;
    place-items: center;
    width: 5.4rem;
    height: 5.4rem;
    color: rgba(255, 255, 255, .92);
    border: 2px solid rgba(255, 255, 255, .7);
    border-radius: 50%;
    font-size: .62rem;
    line-height: 1.25;
    text-align: center;
    letter-spacing: .08em;
    transform: rotate(-9deg);
    box-shadow: inset 0 0 0 3px rgba(255, 255, 255, .18), inset 0 0 0 4px rgba(255, 255, 255, .55);
    mix-blend-mode: screen;
  }

  .pc-postmark b {
    display: block;
    font-size: .74rem;
    letter-spacing: 0;
    white-space: nowrap;
  }

  .pc-hero-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: .9rem;
    padding: 2rem 2.2rem;
    background: var(--card-surface);
  }

  .pc-eyebrow {
    margin: 0;
    color: var(--color-primary);
    font-size: .72rem;
    font-weight: 600;
    letter-spacing: .22em;
    text-transform: uppercase;
  }

  .pc-title {
    margin: 0;
    font-size: 1.9rem;
    font-weight: 800;
    letter-spacing: .04em;
  }

  .pc-lead {
    margin: 0;
    max-width: 28em;
    color: var(--font-color-main-light);
    font-size: .92rem;
    line-height: 1.85;
  }

  .pc-stats {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    margin-top: .2rem;
  }

  .pc-chip {
    display: inline-flex;
    align-items: baseline;
    gap: .3rem;
    padding: .32rem .7rem;
    color: var(--font-color-main);
    background: var(--bg-color-primary);
    border: 1px solid var(--border-color-main);
    border-radius: 999px;
    font-size: .75rem;
    box-shadow: var(--shadow-inset-box);
  }

  .pc-chip b {
    color: var(--color-primary);
    font-size: .92rem;
    font-weight: 700;
  }

  .pc-signature {
    align-self: flex-end;
    margin: .2rem 0 0;
  }

  .pc-signature svg {
    width: 3.2rem;
    height: auto;
  }

  /* 每封信右上角的邮票日期 */
  .postcard-box {
    padding-right: 2.1rem;
  }

  .pc-stamp {
    position: absolute;
    top: .55rem;
    right: .55rem;
    padding: .18rem .3rem;
    color: var(--font-color-main-light);
    background: transparent;
    border: 1px dashed var(--font-color-main-light);
    border-radius: 2px;
    font-size: .58rem;
    line-height: 1.2;
    letter-spacing: .04em;
    opacity: .75;
    transform: rotate(3deg);
    pointer-events: none;
  }

  .postcard-box {
    position: relative;
  }

  /* 区块标题 */
  .pc-section {
    display: flex;
    align-items: center;
    gap: .6rem;
    margin: 1.6rem .25rem .6rem;
  }

  .pc-section-icon {
    display: grid;
    flex: 0 0 auto;
    place-items: center;
    width: 1.9rem;
    height: 1.9rem;
    color: var(--color-primary);
    border-radius: .55rem;
    background: var(--bg-color-primary);
    box-shadow: var(--shadow-inset-box);
  }

  .pc-section-title {
    margin: 0;
    color: var(--font-color-main);
    font-size: 1.05rem;
    font-weight: 700;
  }

  .pc-section-icon .iconfont {
    color: var(--color-primary);
  }

  :root[data-theme="dark"] .pc-section-icon {
    background: rgba(106, 161, 255, 0.16);
  }

  .pc-section-sub {
    margin: 0;
    color: var(--font-color-main-light);
    font-size: .72rem;
  }

  /* 页面内容自带的「留言须知」标题与区块标题重复，隐藏 */
  .pc-section+.post .markdown-body>h1:first-child,
  .pc-section+.post .markdown-body>h2:first-child,
  .pc-section+.post .markdown-body>h3:first-child {
    display: none;
  }

  @media (max-width: 991.98px) {
    .pc-hero {
      grid-template-columns: 1fr;
    }

    .pc-hero-media {
      min-height: 13rem;
    }

    .pc-hero-body {
      padding: 1.4rem 1.3rem;
    }

    .pc-title {
      font-size: 1.5rem;
    }

    .pc-postmark {
      width: 4.6rem;
      height: 4.6rem;
      font-size: .52rem;
      letter-spacing: .04em;
    }

    .pc-postmark b {
      font-size: .62rem;
    }
  }

  /* ===== 回信 / 信件 操作区 ===== */
  .postcard-from {
    margin-top: .45rem;
    padding-top: .5rem;
    border-top: 1px dashed var(--border-color-main);
  }

  .postcard-box {
    padding-bottom: .7rem;
  }

  .pc-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: .4rem;
  }

  .pc-action {
    display: inline-flex;
    align-items: center;
    gap: .28rem;
    padding: .28rem .62rem;
    color: var(--font-color-main-light);
    background: var(--bg-color-main);
    border: 1px solid var(--border-color-main);
    border-radius: 999px;
    font-size: .72rem;
    line-height: 1.1;
    cursor: pointer;
    transition: color .2s, border-color .2s, background-color .2s, transform .2s;
  }

  .pc-action .iconfont {
    font-size: .86rem;
  }

  .pc-action:hover {
    color: var(--color-primary);
    border-color: var(--color-primary-light-3);
    background: var(--color-primary-light-5);
    transform: translateY(-1px);
  }

  .pc-action:focus-visible {
    outline: 2px solid var(--color-primary-light-3);
    outline-offset: 2px;
  }

  .pc-action-caret {
    width: .4rem;
    height: .4rem;
    margin-left: .1rem;
    border-right: 1.5px solid currentColor;
    border-bottom: 1.5px solid currentColor;
    transform: rotate(45deg) translateY(-1px);
    transition: transform .25s ease;
  }

  .pc-action--thread.is-active {
    color: var(--color-primary);
    border-color: var(--color-primary-light-3);
    background: var(--color-primary-light-5);
  }

  .pc-action--thread.is-active .pc-action-caret {
    transform: rotate(-135deg) translateY(-1px);
  }

  .postcard-sign {
    color: var(--font-color-main-light);
    font-family: "瑞美加张清平硬笔行书";
    font-size: .82rem;
    letter-spacing: .04em;
  }

  @media (max-width: 575.98px) {
    .pc-actions {
      gap: .32rem;
    }

    .pc-action {
      padding: .24rem .52rem;
      font-size: .68rem;
    }
  }

  /* ===== 弹窗（回信 / 往来信件） ===== */
  .pc-modal {
    position: fixed;
    inset: 0;
    z-index: 1080;
    display: grid;
    place-items: center;
    padding: 1rem;
    visibility: hidden;
    opacity: 0;
    transition: opacity .24s ease, visibility .24s;
  }

  .pc-modal.is-open {
    visibility: visible;
    opacity: 1;
  }

  /* 回信弹窗要压在「往来信件」之上，方便在信件列表里直接回信 */
  #pcThreadModal {
    z-index: 1080;
  }

  #pcReplyModal {
    z-index: 1090;
  }

  .pc-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(12, 16, 24, .46);
    backdrop-filter: blur(6px) saturate(120%);
    -webkit-backdrop-filter: blur(6px) saturate(120%);
  }

  .pc-modal-panel {
    position: relative;
    display: flex;
    flex-direction: column;
    width: min(42rem, 100%);
    max-height: min(84vh, 46rem);
    background: var(--card-surface);
    border: 1px solid var(--border-color-main);
    border-radius: 1rem;
    box-shadow: 0 28px 60px -26px rgba(0, 0, 0, .5);
    opacity: 0;
    transform: translateY(16px) scale(.97);
    transition: transform .3s cubic-bezier(.22, 1, .36, 1), opacity .22s ease;
  }

  .pc-modal.is-open .pc-modal-panel {
    opacity: 1;
    transform: none;
  }

  .pc-modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: .95rem 1.1rem .75rem;
    border-bottom: 1px dashed var(--border-color-main);
  }

  .pc-modal-title {
    margin: 0;
    color: var(--font-color-main);
    font-size: 1rem;
    font-weight: 700;
  }

  .pc-modal-body {
    color: var(--font-color-main);
  }

  .pc-modal-sub {
    margin: .15rem 0 0;
    color: var(--font-color-main-light);
    font-size: .72rem;
  }

  .pc-modal-close {
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    width: 2rem;
    height: 2rem;
    color: var(--font-color-main-light);
    background: var(--bg-color-primary);
    border: 0;
    border-radius: 50%;
    cursor: pointer;
    transition: color .2s, background-color .2s, transform .2s;
  }

  .pc-modal-close:hover {
    color: var(--color-primary);
    background: var(--color-primary-light-5);
    transform: rotate(90deg);
  }

  .pc-modal-body {
    padding: .9rem 1.1rem 1.2rem;
    overflow: auto;
    overscroll-behavior: contain;
  }

  .pc-modal-body .respond {
    margin: 0;
  }

  .pc-modal-body .comment-form-info {
    margin-bottom: .5rem;
  }

  .pc-modal-body .postcard-box {
    margin: .6rem 0 !important;
  }

  body.pc-modal-open {
    overflow: hidden;
  }

  @media (prefers-reduced-motion: reduce) {

    .pc-modal,
    .pc-modal-panel,
    .pc-modal-close {
      transition: none;
    }
  }

  @media (max-width: 575.98px) {
    .pc-modal-panel {
      max-height: 88vh;
      border-radius: .85rem;
    }
  }
</style>
<?php
function threadedComments($comments, $options)
{
  global $authorName;
  $isParentOrChild = $comments->levels > 0 ? false : true; //判断是否为父评论false=》子评论 true=》父评论
  $commentLevelClass = $isParentOrChild ? ' col-lg-6 postcard-comment-parent' : ' postcard-comment-child';
  $postcardLevelClass = $isParentOrChild ? ' postcard-parent-box' : ' postcard-child-box';
?>
  <li id="li-<?php $comments->theId(); ?>"
    class="p-0 d-flex flex-column flex-grow-1 postcard-item<?php echo $commentLevelClass; ?>">
    <div id="<?php $comments->theId(); ?>" class="d-flex flex-column flex-grow-1<?php echo $postcardLevelClass; ?>">
      <div class="postcard-box m-1 mt-3 p-2 w-auto">
        <span class="pc-stamp"><?php $comments->date('m.d'); ?></span>
        <div class="postcard-code">
          <?php $commentId = $comments->coid;
          $paddedId = str_pad($commentId, 6, '0', STR_PAD_LEFT);
          $digits = str_split($paddedId);
          echo '<div class="postcard-code">';
          foreach ($digits as $digit) {
            echo '<span class="postcard-code-item mr-1">' . htmlspecialchars($digit ?? '') . '</span>';
          }
          echo '</div>';
          ?>
        </div>
        <div class="postcard-to mt-3">
          To：<?php echo htmlspecialchars(pcGetToName($comments->coid) ?: $authorName, ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="postcard-content postcard-text">
          <?php echo formatEmoji($comments->content); ?>
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center postcard-from">
          <div class="pc-actions">
            <span class="pc-action pc-action--reply" role="button" tabindex="0"
              data-cid="<?php echo (int) $comments->coid; ?>"
              data-author="<?php echo htmlspecialchars($comments->author, ENT_QUOTES, 'UTF-8'); ?>"
              onclick="postcardCommentReply(this)">
              <i class="iconfont icon-huixin"></i><span class="pc-action-label">回信</span>
            </span>
            <?php if ($isParentOrChild && $comments->children): ?>
              <button type="button" class="pc-action pc-action--thread" aria-expanded="false"
                onclick="postcardCommentChildList('<?php $comments->theId(); ?>');">
                <i class="iconfont icon-xinjian"></i><span class="pc-action-label">信件</span>
                <i class="pc-action-caret"></i>
              </button>
            <?php endif; ?>
          </div>
          <div class="postcard-sign">From：<?php $comments->author(); ?></div>
        </div>
      </div>
      <?php if ($comments->children): ?>
        <?php $comments->threadedComments($options); ?>
      <?php endif ?>
    </div>
  </li>
<?php
}

?>
<!--主体st-->
<main>
  <div class="container p-0 p-md-1">
    <div class="row no-gutters">
      <!-- home -->
      <div class="col-lg-12 p-1">
        <div class="card pc-hero">
          <div class="pc-hero-media">
            <img class="lazy"
              data-original="<?php echo $this->fields->thumb ?: getAssets('assets/images/pages/mxp.webp', false) ?>"
              alt="<?php $this->title() ?>" src="<?php getLazyload(); ?>" />
            <div class="pc-postmark">
              <span>POST<br>明信片<br><b><?php echo date("Y.m.d"); ?></b></span>
            </div>
            <p class="pc-hero-caption">生活 · 梦想</p>
          </div>
          <div class="pc-hero-body">
            <p class="pc-eyebrow">POSTCARD · 明信片留言</p>
            <h1 class="pc-title"><?php $this->title(); ?></h1>
            <p class="pc-lead">把想说的话写在这里，像寄出一张明信片那样——不必很长，但会被认真收下。</p>
            <div class="pc-stats">
              <span class="pc-chip"><b><?php echo (int) $this->commentsNum; ?></b> 封来信</span>
              <span class="pc-chip">邮编 520438</span>
              <span class="pc-chip">寄给 未来</span>
            </div>
            <p class="pc-signature svg-name"><?php $this->options->svgName(); ?></p>
          </div>
        </div>
      </div>
      <!-- 留言列表st -->
      <?php $this->comments()->to($comments); ?>
      <?php if ($comments->have()): ?>
        <div class="postcard-comments-list w-100">
          <?php $comments->listComments(); ?>
          <div class="comment-pagination mt-4">
            <?php $comments->pageNav('<i class="iconfont icon-shangyiye"></i>', '<i class="iconfont icon-xiayiye"></i>', '2', '...', array('wrapClass' => 'd-flex justify-content-center')); ?>
          </div>
        <?php else: ?>
          <div class="col-lg-12">
            <div class="card m-1 my-3 p-4 w-auto">
              <p>暂无留言，在下方给我留言吧！</p>
            </div>
          </div>
        <?php endif; ?>
        </div>
        <!-- 留言列表end -->
        <div class="col-lg-12 postcard-animation">
          <!-- 页面文章内容 -->
          <div class="pc-section">
            <span class="pc-section-icon"><i class="iconfont icon-info"></i></span>
            <div>
              <p class="pc-section-title">留言须知</p>
              <p class="pc-section-sub">先看看这些，再动笔</p>
            </div>
          </div>
          <div class="post card m-1 mt-3 w-auto">
            <div class="card-body">
              <div class="post-content markdown-body" itemprop="articleBody"><?php $this->content(); ?></div>
            </div>
          </div>
          <!-- 文章en -->
          <!--评论st -->
          <div class="pc-section">
            <span class="pc-section-icon"><i class="iconfont icon-youxiang"></i></span>
            <div>
              <p class="pc-section-title">写信给我</p>
              <p class="pc-section-sub">写完后点「寄出」，我会尽快回信</p>
            </div>
          </div>
          <div class="card m-1 my-3 p-2 p-md-4 w-auto">
            <div id="comments">
              <?php if ($this->allow('comment')): ?>
                <div id="<?php $this->respondId(); ?>" class="respond flex-grow-1 w-100">
                  <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form">
                    <?php if ($this->user->hasLogin()): // ===> 已登录用户
                    ?>
                      <div class="mb-3"><?php _e('当前用户: '); ?><a
                          href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>.
                        <a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('退出'); ?> &raquo;</a>
                      </div>
                    <?php else: // ===> 未登录用户
                    ?>
                      <ul class="row comment-form-info">
                        <li class="col-12 col-md-4 col-xl-4 mb-2">
                          <input type="text" name="author" id="author" class="form-control" placeholder="昵称"
                            value="<?php $this->remember('author'); ?>" required />
                        </li>
                        <li class="col-12 col-md-4 col-xl-4 mb-2">
                          <input type="email" name="mail" id="mail" class="form-control" placeholder="Email"
                            value="<?php $this->remember('mail'); ?>" <?php if ($this->options->commentsRequireMail): ?>
                            required<?php endif; ?> />
                        </li>
                        <li class="col-12 col-md-4 col-xl-4 mb-2">
                          <input type="url" name="url" id="url" class="form-control" placeholder="http(s)://"
                            value="<?php $this->remember('url'); ?>" <?php if ($this->options->commentsRequireURL): ?>
                            required<?php endif; ?> />
                        </li>
                      </ul>
                    <?php endif; ?>
                    <div class="com-body comment-textarea">
                      <textarea name="text" id="textarea" class="form-control" rows="5"
                        placeholder="请填写真实邮箱方便站长联系，并输入有效的内容！" required><?php $this->remember('text'); ?></textarea>
                    </div>
                    <div class="com-footer d-flex flex-wrap justify-content-between align-items-center my-2">
                      <div class="com-tool-list d-flex flex-row align-items-center">
                        <div class="px-2 py-1 com-tool-item com-emoji">
                          <a id="emoji-btn" class="" href="javascript:void(0);" role="button"><i
                              class="iconfont icon-biaoqing"></i></a>
                          <div class="emoji-box"></div>
                        </div>
                      </div>
                      <div class="form-submit text-right">
                        <button id="cancel-comment-reply-link" class="btn btn-card mr-2" style="display:none"
                          onclick="return TypechoComment.cancelReply();">取消回复</button>
                        <button type="submit" class="btn btn-card">提交评论</button>
                      </div>
                    </div>
                  </form>
                </div>
              <?php else: ?>
                <h4>评论已关闭</h4>
              <?php endif; ?>
            </div>
          </div>
          <!-- 评论end -->
        </div>
    </div>
  </div>
</main>

<div class="pc-modal" id="pcReplyModal" aria-hidden="true">
  <div class="pc-modal-backdrop" data-pc-close></div>
  <div class="pc-modal-panel" role="dialog" aria-modal="true" aria-labelledby="pcReplyTitle">
    <div class="pc-modal-head">
      <div>
        <p class="pc-modal-title" id="pcReplyTitle">写回信</p>
        <p class="pc-modal-sub">写完后点「寄出」，我会收到</p>
      </div>
      <button type="button" class="pc-modal-close" data-pc-close aria-label="关闭弹窗"><i
          class="iconfont icon-guanbi"></i></button>
    </div>
    <div class="pc-modal-body">
      <div id="pcReplyHost"></div>
    </div>
  </div>
</div>

<div class="pc-modal" id="pcThreadModal" aria-hidden="true">
  <div class="pc-modal-backdrop" data-pc-close></div>
  <div class="pc-modal-panel" role="dialog" aria-modal="true" aria-labelledby="pcThreadTitle">
    <div class="pc-modal-head">
      <div>
        <p class="pc-modal-title" id="pcThreadTitle">往来信件</p>
        <p class="pc-modal-sub">这段来信的全部回信</p>
      </div>
      <button type="button" class="pc-modal-close" data-pc-close aria-label="关闭弹窗"><i
          class="iconfont icon-guanbi"></i></button>
    </div>
    <div class="pc-modal-body">
      <div id="pcThreadHost"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    let pcRespondHome = null; // 评论表单原始位置
    let pcThreadHome = null; // 回信列表原始位置
    let pcLastFocus = null;

    const pcModal = (id) => document.getElementById(id);

    function pcOpenModal(id, focusSel) {
      const el = pcModal(id);
      if (!el) return;
      pcLastFocus = document.activeElement;
      el.classList.add('is-open');
      el.setAttribute('aria-hidden', 'false');
      document.body.classList.add('pc-modal-open');
      const focusEl = focusSel ? el.querySelector(focusSel) : el.querySelector('[data-pc-close]');
      if (focusEl) setTimeout(function() {
        focusEl.focus({
          preventScroll: true
        });
      }, 120);
    }

    function pcRestore(id) {
      const el = pcModal(id);
      if (!el) return;
      el.classList.remove('is-open');
      el.setAttribute('aria-hidden', 'true');

      if (id === 'pcReplyModal') {
        const respond = document.querySelector('.respond');
        if (respond && pcRespondHome && pcRespondHome.parent) {
          pcRespondHome.parent.insertBefore(respond, pcRespondHome.next);
        }
        const form = document.querySelector('#comment-form');
        const parent = form && form.querySelector('input[name="parent"]');
        if (parent) parent.value = 0;
        $('#cancel-comment-reply-link').hide();
      }

      if (id === 'pcThreadModal') {
        const list = document.querySelector('#pcThreadHost > .comment-list');
        if (list && pcThreadHome && pcThreadHome.parent) {
          pcThreadHome.parent.insertBefore(list, pcThreadHome.next);
        }
      }

      if (!document.querySelector('.pc-modal.is-open')) {
        document.body.classList.remove('pc-modal-open');
      }
      if (pcLastFocus && pcLastFocus.focus) {
        pcLastFocus.focus({
          preventScroll: true
        });
      }
    }

    // 关闭：按钮 / 遮罩 / ESC
    $(document).on('click', '[data-pc-close]', function() {
      const modal = this.closest('.pc-modal');
      if (modal) pcRestore(modal.id);
    });

    $(document).on('keydown', function(e) {
      if (e.key !== 'Escape') return;
      $('.pc-modal.is-open').each(function() {
        pcRestore(this.id);
      });
    });

    // 回信：把评论表单搬进弹窗
    postcardCommentReply = (btn) => {
      const cid = parseInt(btn.dataset.cid || '0', 10);
      const author = btn.dataset.author || '';
      const form = document.querySelector('#comment-form');
      const respond = document.querySelector('.respond');
      const host = document.getElementById('pcReplyHost');
      if (!form || !respond || !host) return;

      let parent = form.querySelector('input[name="parent"]');
      if (!parent) {
        parent = document.createElement('input');
        parent.type = 'hidden';
        parent.name = 'parent';
        form.appendChild(parent);
      }
      parent.value = cid;

      if (!pcRespondHome) {
        pcRespondHome = {
          parent: respond.parentNode,
          next: respond.nextSibling
        };
      }
      host.appendChild(respond);
      $('#cancel-comment-reply-link').show();

      const title = document.getElementById('pcReplyTitle');
      if (title) title.textContent = author ? '回信给 ' + author : '写回信';

      pcOpenModal('pcReplyModal', '#textarea');
    };

    // 信件：把这条来信的回信列表搬进弹窗
    postcardCommentChildList = (cid) => {
      const origin = document.getElementById(cid);
      if (!origin) return;
      const list = origin.querySelector(':scope > .comment-list');
      const host = document.getElementById('pcThreadHost');
      if (!list || !host) return;

      pcThreadHome = {
        parent: list.parentNode,
        next: list.nextSibling
      };
      host.appendChild(list);

      $(host).find('img.lazy').lazyload({
        effect: 'fadeIn',
        threshold: 200,
        load: function() {
          $(this).addClass('loaded').removeClass('lazy');
        }
      });

      pcOpenModal('pcThreadModal');
    };

    // 弹窗内「取消回复」也关闭弹窗
    $(document).on('click', '#pcReplyHost #cancel-comment-reply-link', function() {
      setTimeout(function() {
        pcRestore('pcReplyModal');
      }, 0);
    });

    // 回信按钮键盘触发
    $(document).on('keydown', '.pc-action--reply', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        this.click();
      }
    });
  });
</script>
<!--主体end-->
<?php $this->need('components/footer.php'); ?>