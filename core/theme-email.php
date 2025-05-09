<?php
/* ------------------------------------
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2025-2-25 23:16:37
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/* 邮件通知插件优化版 */
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$options = Helper::options();

if (
    $options->commentMail === 'on' &&
    $options->commentMailHost &&
    $options->commentMailPort &&
    $options->commentMailFromName &&
    $options->commentMailAccount &&
    $options->commentMailPassword &&
    $options->commentSMTPSecure
) {
    Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('Email', 'send');
}

class Email {
    public static function send($comment) {
        try {
            // 公共参数预处理
            $params = array(
                'title' => htmlspecialchars($comment->title),
                'postlink' => preg_replace('/\/comment-page-\d+#comment-\d+/', '', $comment->permalink),
                'permalink' => htmlspecialchars($comment->permalink),
                'author' => htmlspecialchars($comment->author),
                'text' => self::processCommentText($comment->text),
            );

            if ($comment->authorId == $comment->ownerId) {
                self::handleAuthorComment($comment, $params);
            } else {
                self::handleGuestComment($comment, $params);
            }
        } catch (Exception $e) {
            error_log('[Email Plugin] Error: '.$e->getMessage());
        }
    }

    private static function initMailer() {
        try {
            $options = Helper::options();
            $mail = new PHPMailer(false); // 启用异常

            // SMTP配置
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPSecure = $options->commentSMTPSecure;
            $mail->Host = $options->commentMailHost;
            $mail->Port = $options->commentMailPort;
            $mail->FromName = $options->commentMailFromName;
            $mail->Username = $options->commentMailAccount;
            $mail->From = $options->commentMailAccount;
            $mail->Password = $options->commentMailPassword;
            $mail->isHTML(true);

            return $mail;
        } catch (Exception $e) {
            $str = "\nerror time: ".date('Y-m-d H:i:s')."\n";
            echo $str.$e."\n";
        }
    }

    private static function processCommentText($content) {
        $content = preg_replace_callback(
            '/!\[(.*?)\]\((.*?)\)/',
            function ($matches) {
                $alt = $matches[1];
                $siteUrl = Helper::options()->siteUrl;
                $siteUrl = rtrim($siteUrl, '/');
                $src = $siteUrl.$matches[2];
                return '<img style="display:block;width:auto;height:20rem;" src="'.$src.'" alt="'.$alt.'" />';
            },
            $content
        );

        $content = formatEmoji($content, false);

        return $content;
    }

    private static function buildEmailBody($title, $subtitle, $content, $permalink) {
        $options = Helper::options();
        $siteUrl = rtrim($options->siteUrl, '/'); // 确保站点URL末尾没有斜杠
        $favicon = $options->favicon; // 获取favicon
        $parsed = parse_url($favicon);
        if ( ! isset($parsed['scheme']) && strpos($favicon, '//') !== 0) {
            // 智能拼接路径（自动处理斜杠）
            $favicon = $siteUrl.'/'.ltrim($favicon, '/');
        }
        return '
<div style="margin:5px;border-radius:10px;font-size:14px;color:#333;max-width:100%;box-shadow:0 1px 5px rgba(0,0,0,.15);border:2px solid #fff;overflow:hidden;word-break:break-all;">
  <div style="padding:10px;font-size:16px;background-color:#e2ebf0;">'.$title.'</div>
  <div style="background-color: #fff;padding:20px;font-size: 14px;color: #555;">
    <div style="margin-bottom:10px;">'.$subtitle.'</div>
    <div style="padding:10px;margin-bottom:10px;background-image:radial-gradient(transparent 2px,#eee 1px);background-size:4px 4px;border-radius:3px;">'.$content.'</div>
    <div style="margin-bottom:10px;"><a style="display: inline-block;color: #333;text-decoration: none;background: #f3f3f3;border-radius: 8px;margin-top: 10px;padding: 5px 20px;" href="'.$permalink.'" target="_blank">查看详情</a></div>
    <hr style="margin:15px 0;border:1px dashed #eee;">
    <div style="display:flex;color:#999;flex-wrap:wrap;flex-direction:row;justify-content:space-between;align-items:flex-end;"><span>注：此邮件由系统自动发送，请勿直接回复。<br>若此邮件不是您请求的，请忽略并删除！</span><a href="'.$siteUrl.'" target="_blank"><img style="width: auto;max-width:35px;height:35px;" src="'.$favicon.'" /></a></div>
  </div>
</div>';
    }

    // 评论有了新的回复
    private static function handleAuthorComment($comment, $params) {
        if ($comment->parent == 0) {
            return;
        }

        $parentMail = self::getParentMail($comment->parent);
        if ( ! $parentMail || $parentMail == $comment->mail) {
            return;
        }

        $mail = self::initMailer();
        $mail->addAddress($parentMail);
        $mail->Subject = "您在[{$params['title']}]的评论有了新的回复！";
        $mail->Body = self::buildEmailBody(
            $mail->Subject,
            "{$params['author']}同学在《<a style=\"color:#0077FF;text-decoration:none;\"
              href=\"{$params['postlink']}\" target=\"_blank\">{$params['title']}</a>》上回复了您:",
            $params['text'],
            $params['permalink']
        );
        $mail->send();
    }

    private static function handleGuestComment($comment, $params) {
        if ($comment->parent == 0) {
            self::notifyAuthor($comment, $params);
        } else {
            self::notifyParent($comment, $params);
        }
    }

    // 收到一条新的评论
    private static function notifyAuthor($comment, $params) {
        $authorMail = self::getAuthorMail($comment->ownerId);
        if ( ! $authorMail) {
            return;
        }

        $mail = self::initMailer();
        $mail->addAddress($authorMail);
        $mail->Subject = "您的文章[{$params['title']}]收到一条新的评论！";
        $mail->Body = self::buildEmailBody(
            $mail->Subject,
            "{$params['author']}同学在您的《<a style=\"color:#0077FF;text-decoration:none;\"
              href=\"{$params['postlink']}\" target=\"_blank\">{$params['title']}</a>》上发表评论:",
            $params['text'],
            $params['permalink']
        );
        $mail->send();
    }

    // 通知父评论
    private static function notifyParent($comment, $params) {
        $parentMail = self::getParentMail($comment->parent);
        if ( ! $parentMail || $parentMail == $comment->mail) {
            return;
        }

        $mail = self::initMailer();
        $mail->addAddress($parentMail);
        $mail->Subject = "您在[{$params['title']}]的评论有了新的回复！";
        $mail->Body = self::buildEmailBody(
            $mail->Subject,
            "{$params['author']}同学在《<a style=\"color:#0077FF;text-decoration:none;\"
              href=\"{$params['postlink']}\" target=\"_blank\">{$params['title']}</a>》上回复了您:",
            $params['text'],
            $params['permalink']
        );
        $mail->send();
    }

    private static function getParentMail($parentId) {
        $db = Typecho_Db::get();
        $result = $db->fetchRow(
            $db->select('mail')
                ->from('table.comments')
                ->where('coid = ?', $parentId)
        );
        return $result['mail'] ?? null;
    }

    private static function getAuthorMail($ownerId) {
        $db = Typecho_Db::get();
        $result = $db->fetchRow(
            $db->select('mail')
                ->from('table.users')
                ->where('uid = ?', $ownerId)
        );
        return $result['mail'] ?? null;
    }
}