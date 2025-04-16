<?php

/**
 * 明信片留言
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-1-20 12:48:15
 * @package custom
 */
?>
<?php if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php
global $authorName;
$authorName = $this->author->screenName;
$this->need('components/header.php');
?>

<style>
@import url('https://chinese-fonts-cdn.deno.dev/packages/rmjzqpybxs/dist/瑞美加张清平硬笔行书/result.css');

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
</style>
<?php
function threadedComments($comments, $options) {
    global $authorName;
    $isParentOrChild = $comments->levels > 0 ? false : true; //判断是否为父评论false=》子评论 true=》父评论
    $commentLevelClass = $isParentOrChild ? ' col-lg-6 postcard-comment-parent' : ' postcard-comment-child';
    $postcardLevelClass = $isParentOrChild ? ' postcard-parent-box' : ' postcard-child-box';
    ?>
<li id="li-<?php $comments->theId(); ?>"
  class="p-0 d-flex flex-column flex-grow-1 postcard-item<?php echo $commentLevelClass; ?>">
  <div id="<?php $comments->theId(); ?>" class="d-flex flex-column flex-grow-1<?php echo $postcardLevelClass; ?>">
    <div class="postcard-box m-1 mt-3 p-2 w-auto">
      <div class="postcard-stamp p-2">
        <?php $email = $comments->mail;
    $imgUrl = getGravatar($email);
    echo '<img class="lazy" src="'.getAvatarLazyload(false).'" data-original="'.$imgUrl.'" title="邮票头像">'; ?>
      </div>
      <div class="postcard-code">
        <?php $commentId = $comments->coid;
    $paddedId = str_pad($commentId, 6, '0', STR_PAD_LEFT);
    $digits = str_split($paddedId);
    echo '<div class="postcard-code">';
    foreach ($digits as $digit) {
        echo '<span class="postcard-code-item mr-1">'.htmlspecialchars($digit).'</span>';
    }
    echo '</div>';
    ?>
      </div>
      <div class="postcard-to mt-3">To：<?php echo getCommentAt($comments->coid, 'a') ?: $authorName; ?></div>
      <div class="postcard-content postcard-text">
        <?php echo formatEmoji($comments->content); ?>
      </div>
      <div class="d-flex justify-content-between align-items-center postcard-from">
        <div class="reply comment-footer">
          <span class="comment-reply-link" onclick="postcardCommentReply();">
            <?php $comments->reply('<i class="iconfont icon-huixin"></i>回信'); ?>
          </span>
          <?php if ($isParentOrChild && $comments->children): ?>
          <button class="comment-reply-list" onclick="postcardCommentChildList('<?php $comments->theId(); ?>');"><i
              class="iconfont icon-xinjian"></i><span>信件</span></button>
          <?php endif; ?>
        </div>
        <div>From：<?php $comments->author(); ?></div>
      </div>
    </div>
    <?php if ($comments->children): ?>
    <?php $comments->threadedComments($options); ?>
    <?php endif?>
  </div>
</li>
<?php
}

?>
<!--主体st-->
<main>
  <div class="container p-2">
    <div class="row no-gutters">
      <!-- home -->
      <div class="col-lg-12">
        <div class="card postcard-cover m-1 mt-2 flex-row w-auto">
          <div class="w-50 postcard-cover-bg">
            <img src="https://bu.dusays.com/2025/01/20/678de9907fe2a.webp" />
            <div class="postcard-cover-name d-flex flex-column align-items-center justify-content-center p-4">
              <p class="text-shadow-style postcard-cover-text">生活·梦想</p>
            </div>
          </div>
          <div
            class="w-50 h-100 postcard-cover-content d-flex flex-column align-items-center justify-content-center p-4">
            <p class="postcard-cover-text m-0">“人世间太多的情感与归宿我们不能把握构成了命运的不确定与爱情的不可求。”</p>
            <p class="svg-name text-right w-100 m-0"><?php $this->options->svgName(); ?></p>
          </div>
        </div>
      </div>
      <!-- 留言列表st -->
      <?php $this->comments()->to($comments); ?>
      <?php if ($comments->have()): ?>
      <div class="postcard-comments-list w-100">
        <?php $comments->listComments(); ?>
        <div class="comment-pagination mt-4">
          <?php $comments->pageNav('<', '>', '2', '...', array('wrapClass' => 'd-flex justify-content-center')); ?>
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
      <div class="col-lg-12">
        <!-- 页面文章内容 -->
        <div class="col-lg-12 links-title font-weight-bold postcard-title mt-3">
          <p class="text-shadow-style m-0">留言须知</p>
        </div>
        <div class="post card m-1 mt-3 w-auto">
          <div class="card-body">
            <div class="post-content markdown-body" itemprop="articleBody"><?php $this->content(); ?></div>
          </div>
        </div>
        <!-- 文章en -->
        <!--评论st -->
        <div class="col-lg-12 links-title font-weight-bold postcard-title mt-3">
          <p class="text-shadow-style m-0">给我留言</p>
        </div>
        <div class="card m-1 my-3 p-2 p-md-4 w-auto">
          <div id="comments">
            <?php if ($this->allow('comment')): ?>
            <div id="<?php $this->respondId(); ?>" class="respond flex-grow-1 w-100">
              <form method="post" action="<?php $this->commentUrl()?>" id="comment-form" role="form">
                <?php if ($this->user->hasLogin()): // ===> 已登录用户 ?>
	                <div class="mb-3"><?php _e('当前用户: '); ?><a
	                    href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>.
	                  <a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('退出'); ?> &raquo;</a>
	                </div>
	                <?php else: // ===> 未登录用户 ?>
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
<script type="text/javascript">
$(document).ready(function() {
  postcardCommentReply = () => {
    const $pageClass = $('#respond-page-<?php $this->cid(); ?>')

    $pageClass.append('<div class="header-overlay-card"></div>');
    $pageClass.addClass('open');

    $pageClass.find('#cancel-comment-reply-link').off('click').on('click', function() {
      $pageClass.removeClass('open');
      setTimeout(function() {
        $pageClass.find('.header-overlay-card').remove();
      }, 500);
    });

    $pageClass.on('click', function(e) {
      e.stopPropagation();
    });
  };

  postcardCommentChildList = (cid) => {
    const $commentListClass = $(`#${cid}`);
    const $commentList = $commentListClass.find('.comment-list');

    // 元素不存在时提前退出
    if ($commentList.length === 0) return;

    // 切换展开状态
    const $commentListBtn = $commentListClass.find('.comment-reply-list span');
    $commentListClass.toggleClass('active');
    $commentList.toggle();

    // 触发当前容器内的图片懒加载
    $commentListClass.find("img.lazy").lazyload({
      effect: "fadeIn",
      threshold: 200,
      load: function() {
        $(this).addClass("loaded").removeClass('lazy');
      }
    });

    // 更新按钮文本
    const isActive = $commentListClass.hasClass('active');
    $commentListBtn.text(isActive ? '收起' : '信件');
  };
});
</script>
<!--主体end-->
<?php $this->need('components/footer.php'); ?>