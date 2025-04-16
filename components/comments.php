<?php
/**
 * 评论主体内容
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php function threadedComments($comments, $options) {
    // ====>评论层级样式
    $commentLevelClass = $comments->levels > 0 ? ' comment-child' : ' comment-parent';
    ?>
<li id="li-<?php $comments->theId(); ?>" class="comment<?php echo $commentLevelClass; ?> p-1">
  <div id="<?php $comments->theId(); ?>" class="comment-body d-flex flex-column p-1">
    <div class="comment-author d-flex flex-row">
      <?php
$email = $comments->mail;
    $imgUrl = getGravatar($email);
    echo '<img class="lazy" src="'.getAvatarLazyload(false).'" data-original="'.$imgUrl.'">';
    ?>
      <div class="comment-header">
        <div class="comment-name">
          <?php CommentAuthor($comments); ?>
          <?php if ($comments->authorId == 1): ?>
          <span class="comment-author-ua">博主</span>
          <?php endif; ?>
        </div>
        <time class="comment-time" title="<?php $comments->date('c'); ?>"><?php echo ueTime($comments->date); ?></time>
        <span class="comment-ua"><?php echo getOs($comments->agent); ?></span>
        <span class="comment-ua"><?php echo getBrowser($comments->agent); ?></span>
        <?php if ($comments->status === 'waiting'): ?>
        <em class="comment-ua">评论审核中...</em>
        <?php endif; ?>
      </div>
      <div class="reply comment-footer">
        <span class="comment-reply-link">
          <?php $comments->reply('<i class="iconfont icon-pinglun"></i>'); ?>
        </span>
      </div>
    </div>
    <div class="comment-card flex-grow-1">
      <div class="comment-content ml-3 ml-md-5">
        <?php echo getCommentAt($comments->coid); ?> <?php echo formatEmoji($comments->content); ?>
      </div>
    </div>
  </div>
  <?php if ($comments->children): ?>
  <?php $comments->threadedComments($options); ?>
  <?php endif?>
</li>
<?php
}

?>
<div class="card my-2 p-2 p-md-3 my-md-3">
  <div id="comments">
    <?php $this->comments()->to($comments); ?>
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
          <textarea name="text" id="textarea" class="form-control" rows="5" placeholder="请填写真实邮箱方便站长联系，并回复有效的内容！"
            required><?php $this->remember('text'); ?></textarea>
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
            <button type="submit" class="btn btn-card px-2 py-1">提交评论</button>
          </div>
        </div>
      </form>
    </div>
    <?php else: ?>
    <h4>评论已关闭</h4>
    <?php endif; ?>
    <?php if ($comments->have()): // ===>评论列表 ?>
    <p><?php $this->commentsNum(_t('暂无评论'), _t('仅有一条评论'), _t('全部 %d 条评论')); ?></p>
    <?php $comments->listComments(); ?>
    <div class="comment-pagination">
      <?php $comments->pageNav('<', '>', '2', '...', array('wrapClass' => 'd-flex justify-content-center mt-3')); ?>
    </div>
    <?php else: ?>
    <div class="no-comments d-flex flex-column align-items-center">
      <svg viewBox="0 0 1462 1024" height="7rem">
        <path d="M673.192229 403.426743v241.7664l300.163657 129.316571V485.902629l-300.163657-82.475886z"
          fill="#ACACAD"></path>
        <path d="M428.383086 472.210286v303.264914l245.160228-131.262171V403.704686L428.383086 472.210286z"
          fill="#C2C1C1"></path>
        <path
          d="M823.881143 124.679314a29.257143 29.257143 0 0 0-41.252572 41.311086c11.395657 11.381029 42.993371 24.488229 54.403658 13.165714S835.291429 136.045714 823.881143 124.679314zM911.36 209.393371c-11.395657-11.395657-42.993371-24.502857-54.403657-13.092571s1.696914 43.008 13.092571 54.403657A29.257143 29.257143 0 0 0 911.36 209.393371z"
          fill="#CDD6E3" opacity=".4"></path>
        <path
          d="M846.687086 159.758629a23.669029 23.669029 0 0 0-23.698286 23.698285v26.682515h26.755657a23.669029 23.669029 0 0 0 23.698286-23.698286v-26.682514z"
          fill="#A6B5C3"></path>
        <path
          d="M814.160457 173.780114l5.637852-5.636388 41.240868 41.240868-5.637851 5.637852zM830.071954 160.506149l5.637852-5.636389 41.240868 41.240869-5.637851 5.637851z"
          fill="#DCE4F1"></path>
        <path d="M428.383086 472.751543v302.299428l300.163657 129.316572V568.0128l-300.163657-95.261257z"
          fill="#E6E7E8"></path>
        <path d="M724.289829 568.0128v336.354743l250.5728-129.316572V485.536914l-250.5728 82.475886z" fill="#D8D8D8">
        </path>
        <path
          d="M974.160457 485.332114l-248.992914 82.037029 88.7808 148.918857 257.082514-101.317486-96.8704-129.6384z"
          fill="#E5E5E5"></path>
        <path
          d="M428.017371 472.049371l-83.909485 130.896458 295.716571 113.342171 86.396343-149.0944-298.203429-95.144229z"
          fill="#D3D3D3"></path>
        <path
          d="M894.1568 140.434286a3.657143 3.657143 0 0 1-0.687543 0 2.925714 2.925714 0 0 1-2.165028-3.525486L895.268571 120.246857a2.925714 2.925714 0 1 1 5.690515 1.360457l-3.949715 16.559543a2.925714 2.925714 0 0 1-2.852571 2.267429zM899.466971 146.826971a2.925714 2.925714 0 0 1-0.980114-5.675885l16.091429-5.705143a2.925714 2.925714 0 0 1 1.960228 5.514971l-16.091428 5.705143a2.925714 2.925714 0 0 1-0.980115 0.160914z"
          fill="#CCCCCC"></path>
        <path
          d="M881.415314 133.251657a21.211429 21.211429 0 1 0 21.240686 21.211429 21.226057 21.226057 0 0 0-21.240686-21.211429z"
          fill="#8897A5"></path>
        <path
          d="M923.062857 390.948571a7.489829 7.489829 0 1 0 14.965029 0v-17.451885H923.062857zM319.093029 453.485714a39.906743 39.906743 0 1 0 39.906742 39.906743A40.0384 40.0384 0 0 0 319.093029 453.485714z m0 64.848457a24.941714 24.941714 0 1 1 24.941714-24.941714 24.693029 24.693029 0 0 1-24.941714 25.000229zM937.998629 341.065143a7.489829 7.489829 0 0 0-14.965029 0v17.466514h14.965029zM1087.648914 403.426743a34.9184 34.9184 0 1 0 34.9184 34.903771 34.698971 34.698971 0 0 0-34.9184-34.903771z m0 59.860114a24.941714 24.941714 0 1 1 24.868572-24.956343 24.707657 24.707657 0 0 1-24.868572 24.956343z"
          fill="#D8D8D8"></path>
        <path
          d="M473.717029 343.771429h-29.915429v19.953371h29.915429a9.976686 9.976686 0 1 0 0-19.953371zM393.918171 343.771429a9.976686 9.976686 0 0 0 0 19.953371h29.930058V343.771429zM955.465143 358.531657h-49.883429a7.489829 7.489829 0 1 0 0 14.965029h49.883429a7.489829 7.489829 0 0 0 0-14.965029zM326.7584 204.0832a24.941714 24.941714 0 1 0 24.868571 24.941714 24.693029 24.693029 0 0 0-24.868571-24.941714z m0 39.906743a14.965029 14.965029 0 1 1 14.965029-14.965029 14.628571 14.628571 0 0 1-14.965029 14.965029z"
          fill="#E5E5E5"></path>
        <path
          d="M443.8016 313.885257a9.976686 9.976686 0 1 0-19.953371 0v79.798857a9.976686 9.976686 0 1 0 19.953371 0v-79.798857z"
          fill="#D8D8D8"></path>
      </svg>
      <p style="color: var(--main-muted-color1);">暂无评论</p>
    </div>
    <?php endif; ?>
  </div>
</div>