<?php

/**
 * Harmony Hues主题 - 邮件通知
 *
 * @author  星语社长
 * @link    https://biibii.cn
 * @update  2025-2-25 23:16:37
 */

if (! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

// 引入PHPMailer组件
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$options = Helper::options();

// 检查邮件配置是否完整
if (
    $options->commentMail === 'on' &&
    $options->commentMailHost &&
    $options->commentMailPort &&
    $options->commentMailFromName &&
    $options->commentMailAccount &&
    $options->commentMailPassword &&
    $options->commentSMTPSecure
) {
    // 注册评论完成时的回调
    Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('Email', 'send');
}

class Email
{
    /**
     * 发送评论通知的主入口
     *
     * @param Widget_Feedback $comment 评论对象
     */
    public static function send($comment)
    {
        try {
            $params = array(
                'title' => htmlspecialchars($comment->title),
                'postlink' => preg_replace('/\/comment-page-\d+#comment-\d+/', '', $comment->permalink),
                'permalink' => htmlspecialchars($comment->permalink),
                'author' => htmlspecialchars($comment->author),
                'text' => self::processCommentText($comment->text),
                'mail' => $comment->mail,
            );
            // 作者评论处理（博主回复）
            if ($comment->authorId == $comment->ownerId) {
                self::handleAuthorComment($comment, $params);
            } else {
                self::handleGuestComment($comment, $params);
            }
        } catch (Exception $e) {
            error_log('[Email Plugin] Error: ' . $e->getMessage());
        }
    }

    /**
     * 初始化邮件发送器
     * 新增SMTP主机连通性检测
     */
    private static function initMailer()
    {
        try {
            $options = Helper::options();
            $host = $options->commentMailHost;
            $port = $options->commentMailPort;
            $timeout = 3;
            $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
            if (! $connection) {
                throw new Exception("SMTP主机不可达: {$host}:{$port} - {$errstr} ({$errno})");
            }
            fclose($connection);
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPSecure = $options->commentSMTPSecure;
            $mail->Host = $host;
            $mail->Port = $port;
            $mail->FromName = $options->commentMailFromName;
            $mail->Username = $options->commentMailAccount;
            $mail->From = $options->commentMailAccount;
            $mail->Password = $options->commentMailPassword;
            $mail->isHTML(true);
            return $mail;
        } catch (Exception $e) {
            error_log('[Email Plugin] SMTP初始化失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 处理评论内容
     * - 转换图片标签为响应式展示
     * - 处理表情符号
     *
     * @param string $content 原始评论内容
     * @return string 处理后的HTML内容
     */
    private static function processCommentText($content)
    {
        $content = preg_replace_callback(
            '/!\[(.*?)\]\((.*?)\)/',
            function ($matches) {
                $alt = htmlspecialchars($matches[1]);
                $siteUrl = Helper::options()->siteUrl;
                $src = rtrim($siteUrl, '/') . '/' . ltrim($matches[2], '/');
                return '<img style="display:block;width:auto;height:20rem;" src="' . $src . '" alt="' . $alt . '" />';
            },
            $content
        );
        if (function_exists('formatEmoji')) {
            $content = formatEmoji($content, false);
        }
        return $content;
    }

    /**
     * 构建邮件HTML正文
     * 新增原评论内容展示区域
     *
     * @param string $title 邮件标题
     * @param string $subtitle 邮件副标题
     * @param string $content 当前评论内容
     * @param string $originalContent 原评论内容（新增）
     * @param string $permalink 评论链接
     * @return string 完整的HTML邮件正文
     */
    private static function buildEmailBody($title, $subtitle, $content, $originalContent, $permalink)
    {
        $options = Helper::options();
        $siteUrl = rtrim($options->siteUrl, '/');
        $favicon = $options->favicon;
        if ($favicon && ! parse_url($favicon, PHP_URL_SCHEME)) {
            $favicon = $siteUrl . '/' . ltrim($favicon, '/');
        }
        $originalSection = '';
        if (! empty($originalContent)) {
            $originalSection = <<<HTML
<div style="margin:15px 0;padding: 10px;border-radius: 3px;border: 1px solid #ededed;border-left: 3px solid #ddd;">
    <div style="margin-bottom:5px;color:#666;">您原评论的内容：</div>
    <div style="padding: 10px;margin-top: 10px;background-image: radial-gradient(transparent 2px, #eee 1px);background-size: 4px 4px;border-radius: 3px;">
        {$originalContent}
    </div>
</div>
HTML;
        }
        return <<<HTML
<div style="margin:5px;border-radius:10px;font-size:14px;color:#333;max-width:100%;box-shadow:0 1px 5px rgba(0,0,0,.15);border:2px solid #fff;overflow:hidden;word-break:break-all;">
  <div style="padding:10px;font-size:16px;background-color:#e2ebf0;">{$title}</div>
  <div style="background-color: #fff;padding:20px;font-size: 14px;color: #555;">
    <div style="margin-bottom:10px;">{$subtitle}</div>
    <div style="padding:10px;margin-bottom:10px;background-image:radial-gradient(transparent 2px,#eee 1px);background-size:4px 4px;border-radius:3px;">{$content}</div>
    {$originalSection}
    <div style="margin-bottom:10px;"><a style="display: inline-block;color: #333;text-decoration: none;background: #f3f3f3;border-radius: 8px;margin-top: 10px;padding: 5px 20px;" href="{$permalink}" target="_blank">查看详情</a></div>
    <hr style="margin:15px 0;border:1px dashed #eee;">
    <div style="display:flex;color:#999;flex-wrap:wrap;flex-direction:row;justify-content:space-between;align-items:flex-end;">
        <span>注：此邮件由系统自动发送，请勿直接回复。<br>若此邮件不是您请求的，请忽略并删除！</span>
        <a href="{$siteUrl}" target="_blank"><img style="width: auto;max-width:35px;height:35px;" src="{$favicon}" /></a>
    </div>
  </div>
</div>
HTML;
    }

    /**
     * 处理博主回复（作者评论）
     * - 博主回复其他用户评论时触发
     * - 新增原评论内容展示
     */
    private static function handleAuthorComment($comment, $params)
    {
        if ($comment->parent == 0) {
            return;
        }
        $parentComment = self::getParentComment($comment->parent);
        if (! $parentComment || $parentComment['mail'] == $comment->mail) {
            return;
        }
        $originalText = self::processCommentText($parentComment['text']);
        $mail = self::initMailer();
        $mail->addAddress($parentComment['mail']);
        $mail->Subject = "您在[{$params['title']}]的评论有了新的回复！";
        $mail->Body = self::buildEmailBody(
            $mail->Subject,
            "{$params['author']}在《<a style=\"color:#0077FF;text-decoration:none;\" href=\"{$params['postlink']}\" target=\"_blank\">{$params['title']}</a>》上回复了您：",
            $params['text'],
            $originalText,
            $params['permalink']
        );
        $mail->send();
    }

    /**
     * 处理访客评论
     * - 区分新评论和回复评论
     */
    private static function handleGuestComment($comment, $params)
    {
        if ($comment->parent == 0) {
            self::notifyAuthor($comment, $params);
        } else {
            self::notifyParent($comment, $params);
        }
    }

    /**
     * 通知文章作者有新评论
     */
    private static function notifyAuthor($comment, $params)
    {
        $authorMail = self::getAuthorMail($comment->ownerId);
        if (! $authorMail) {
            return;
        }
        $mail = self::initMailer();
        $mail->addAddress($authorMail);
        $mail->Subject = "您的文章[{$params['title']}]收到一条新的评论！";
        $mail->Body = self::buildEmailBody(
            $mail->Subject,
            "{$params['author']}在您的《<a style=\"color:#0077FF;text-decoration:none;\" href=\"{$params['postlink']}\" target=\"_blank\">{$params['title']}</a>》上发表评论：",
            $params['text'],
            '',
            $params['permalink']
        );
        $mail->send();
    }

    /**
     * 通知被回复的评论者
     * - 新增原评论内容展示
     */
    private static function notifyParent($comment, $params)
    {
        $parentComment = self::getParentComment($comment->parent);
        if (! $parentComment || $parentComment['mail'] == $comment->mail) {
            return;
        }
        $originalText = self::processCommentText($parentComment['text']);
        $mail = self::initMailer();
        $mail->addAddress($parentComment['mail']);
        $mail->Subject = "您在[{$params['title']}]的评论有了新的回复！";
        $mail->Body = self::buildEmailBody(
            $mail->Subject,
            "{$params['author']}在《<a style=\"color:#0077FF;text-decoration:none;\" href=\"{$params['postlink']}\" target=\"_blank\">{$params['title']}</a>》上回复了您：",
            $params['text'],
            $originalText,
            $params['permalink']
        );
        $mail->send();
    }

    /**
     * 获取父评论信息（增强版）
     * 现在返回完整评论数据而不仅是邮箱
     *
     * @param int $parentId 父评论ID
     * @return array|null 包含邮件和文本的数组
     */
    private static function getParentComment($parentId)
    {
        $db = Typecho_Db::get();
        return $db->fetchRow($db->select('mail', 'text')
            ->from('table.comments')
            ->where('coid = ?', $parentId));
    }

    /**
     * 获取文章作者邮箱
     *
     * @param int $ownerId 用户ID
     * @return string|null 邮箱地址
     */
    private static function getAuthorMail($ownerId)
    {
        $db = Typecho_Db::get();
        $result = $db->fetchRow($db->select('mail')
            ->from('table.users')
            ->where('uid = ?', $ownerId));
        return $result['mail'] ?? null;
    }
}
