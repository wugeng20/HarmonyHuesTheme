<?php

/**
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 */
if (! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

use Utils\Helper;

?>
<?php
/**
 * 获取主题当前版本号
 * @return string
 */
function getVersion()
{
    try {
        $themeInfo = Typecho_Plugin::parseInfo(dirname(__DIR__) . '/index.php');
        return $themeInfo['version'];
    } catch (Exception $e) {
        return '获取主题版本失败' . $e;
    }
};

/**
 * 获取资源路径
 * @param  string $assetPath
 * @param  bool $echo
 * @return string
 */
function getAssets($assetPath, $echo = true)
{
    $options = Helper::options();
    $baseUrl = $options->AssetsURL ? $options->AssetsURL : $options->themeUrl;
    $fullUrl = rtrim($baseUrl, '/') . '/' . ltrim($assetPath, '/');
    if ($echo) {
        echo $fullUrl;
    } else {
        return $fullUrl;
    }
}

/**
 * 初始化主题
 * @param  Typecho_Widget_Helper_Layout $self
 * @return void
 */
function themeInit($self)
{
    $options = Helper::options();

    // 设置评论排序为最新优先
    $options->commentsOrder = 'DESC';

    // 定义路由规则
    $routes = array(
        '/sitemap.xml' => 'sitemap.php',
        '/golink' => 'golink.php',
    );

    // PC端首页单栏模式布局的文章数量
    if ($options->indexLayout && !isMobile() && $self->parameter->pageSize < 10) {
        $self->parameter->pageSize += 3;
    }

    // 手机文章数量
    if (isMobile()) {
        $self->parameter->pageSize += 1;
    }


    // 获取当前请求路径
    $pathInfo = $self->request->getPathInfo();

    // 匹配路由并设置主题文件
    if (array_key_exists($pathInfo, $routes)) {
        $self->setThemeFile($routes[$pathInfo]);
        $self->response->setStatus(200);
    }

    // 随机一篇文章
    if ($self->request->isGet() && $self->request->get('random')) {
        header('Location: ' . randomPost('return'));
        exit;
    }

    // 添加文章锚点功能
    if ($self->is('single')) {
        $self->content = parseContens($self->content); // 解析内容
    }
}

/**
 * 建站天数
 * @param  string $launchDate
 * @return int
 */
function getWebsiteAgeInDays($launchDate)
{
    // 获取当前日期
    $currentDate = date('Y-m-d');

    // 将日期转换为时间戳
    $launchTimestamp = strtotime($launchDate);
    $currentTimestamp = strtotime($currentDate);

    // 计算两个日期之间的时间差（以秒为单位）
    $diffInSeconds = $currentTimestamp - $launchTimestamp;

    // 将秒转换为天
    $diffInDays = floor($diffInSeconds / (60 * 60 * 24));

    return $diffInDays;
}

/**
 * 获取管理员或站长是否在线
 * @return bool
 */
function isAdminOnline()
{
    // 查询数据库
    $db = Typecho_Db::get();
    $row = $db->fetchRow($db->select('activated')
        ->from('table.users')
        ->where('uid = ?', 1));

    if ($row) {
        $loggedTime = $row['activated']; // 获取登录时间戳

        // 获取今天的开始和结束时间戳
        $todayStart = strtotime('today');
        $todayEnd = strtotime('tomorrow') - 1;

        // 判断登录时间是否在今天范围内
        if ($loggedTime >= $todayStart && $loggedTime <= $todayEnd) {
            return true;
        }
    }

    return false;
}

/**
 * 获取主题模式
 * @return string
 */
function getThemeMode()
{
    // 获取 Cookie 中的 theme 值
    $theme = $_COOKIE['theme'] ?? 'light'; // 默认值为 'light'
    $systemTheme = $_COOKIE['system_theme'] ?? '';
    $theme = $theme === 'system' ? $systemTheme : $theme;

    return htmlspecialchars($theme);
}

/**
 * 根据主题筛选LOGO
 * @param  string $class
 * @return string
 */
function getLogoImg($class = '')
{
    $options = Helper::options();
    $whiteSrc = $options->logoUrl;
    $darkSrc = $options->logoUrlDark;
    $alt = $options->title ?? $options->description;

    if (empty($whiteSrc)) {
        $whiteSrc = $darkSrc;
    }

    if (empty($darkSrc)) {
        $darkSrc = $whiteSrc;
    }

    $logoImg = getThemeMode() == 'light' ? '<img class="' . $class . '" src="' . $whiteSrc . '" dark-src="' . $darkSrc . '" alt="' . $alt . '">' : '<img class="' . $class . '" src="' . $darkSrc . '" white-src="' . $whiteSrc . '" alt="' . $alt . '">';

    return $logoImg;
}

/**
 * 获取文章浏览量
 * @param  object $archive
 * @return string
 */
function PostViewCount($archive)
{
    $postId = $archive->cid;
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    // 检查是否存在 views 字段
    $columns = $db->fetchRow($db->select()->from('table.contents'));
    if (! array_key_exists('views', $columns)) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0;');
        echo 0;
        return;
    }

    // 获取当前文章的 views 值
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $postId));
    $views = $row['views'];

    if ($archive->is('single')) {
        $cookieViews = Typecho_Cookie::get('extend_contents_views');
        $viewedPosts = empty($cookieViews) ? array() : explode(',', $cookieViews);

        // 如果当前文章未被查看过，则增加 views 值
        if (! in_array($postId, $viewedPosts)) {
            $db->query($db->update('table.contents')->rows(array('views' => (int) $views + 1))->where('cid = ?', $postId));
            $views++;
            array_push($viewedPosts, $postId);
            Typecho_Cookie::set('extend_contents_views', implode(',', $viewedPosts)); // 记录查看 cookie
        }
    }

    echo $views;
}

/**
 * 随机文章
 * @type  echo | return
 * @return string
 */
function randomPost($type = 'echo')
{
    $db = Typecho_Db::get();
    $result = $db->fetchRow($db->select()->from('table.contents')->where('type=?', 'post')->where('status=?', 'publish')->limit(1)->order('RAND()'));
    if ($result) {
        $f = Helper::widgetById('Contents', $result['cid']);
        $permalink = $f->permalink;
        if ($type == 'return') {
            return $permalink;
        } else {
            echo $permalink;
        }
    } else {
        if ($type == 'return') {
            return false;
        } else {
            echo '没有文章可随机';
        }
    }
}

/**
 * 时间格式化
 * @param  object $date
 * @return string
 */
function ueTime($date)
{
    $timestamp = strtotime($date->format('Y-m-d H:i:s'));
    $current_time = time();
    $time_diff = $current_time - $timestamp;

    // 几秒前
    if ($time_diff < 60) {
        return $time_diff . '秒前';
    }

    // 几分钟前
    if ($time_diff < 3600) {
        return floor($time_diff / 60) . '分钟前';
    }

    // 几小时前
    if ($time_diff < 86400) {
        return floor($time_diff / 3600) . '小时前';
    }

    // 几天前-》30天以内
    if ($time_diff < 2592000) {
        return floor($time_diff / 86400) . '天前';
    }

    // 几个月前-》1年内
    if ($time_diff < 31536000) {
        return floor($time_diff / 2592000) . '个月前';
    }

    // 超过3年显示具体日期
    return $date->format('Y-m-d');
}

/**
 * 时间格式化Mini
 * @param  object $date
 * @return string
 */
function ueTimeMini($date)
{
    $current_year = date('Y');
    $date_year = $date->format('Y');

    // 如果是今年，返回 "月-日"
    if ($date_year == $current_year) {
        return $date->format('m-d');
    }

    // 否则返回 "年-月-日"
    return $date->format('Y-m-d');
}

/**
 * 个人社交信息
 * @return string
 */
function socialInfo()
{
    $socialInfo = Helper::options()->socialInfo;
    $socialArray = json_decode('[' . trim($socialInfo) . ']', true);
    $html = '';
    foreach ($socialArray as $social) {
        $html .= '<a href="' . htmlspecialchars($social['link']) . '" title="' . htmlspecialchars($social['name']) . '" target="_blank"><i class="iconfont ' . htmlspecialchars($social['icon']) . '"></i></a>';
    }

    return $html;
}

/**
 * 获取全局懒加载图
 * @param  boolean $type
 * @return string
 */
function getLazyload($type = true)
{
    $Lazyload = Helper::options()->lazyload;
    if (! $Lazyload) {
        $Lazyload = 'data:image/webp;base64,UklGRtQEAABXRUJQVlA4TMcEAAAvEsf/AEdw+H6BbOJPG8EbmbSNf7n7mzH/oY0ovygRHLVt2zDS/3enbt4iQmHaNkx2e4oZEKVyA+BU39we23cGlM617W206JXHXyhNtEuxucy7U3qzSjKUGVySUTvGWM//ZpR83u7ZENH/CWAcyTagLyYlE2KR//2dsNsR/Z+A4n/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+53/+PxVtnyvkn59fJI9biPqzIQ67QtmpoQ27Qt3JsMYKhe8bzngIla9ShoXOvmGMD0phTxgWahu+OOjV00UX9PKGLUYo3rLFQbOBLVbNHFlYpO6QIQGGK64njFYh7ZMQO+GKQ+xqBX0VG7hijuwl6hL5hiuWSKvLGHFcgeNelA3HJqroIt9osxwDZZxp8yJimMJGTgno20irzfVIwxRjpNHm28juf//xn/P8/i74vy5S0FscvUJAC6JX6GdG4o58LFId+SxJaKnHIt1RzyEDDfOEnIF4RuQ64nmRBcM7S17LO2veKe10yB9oxxbY085Y4D4RTbRznYgeFPC0c73ARET3aefbAo52bIE97UjIO+edj3lnvDPn7XjnepYX3rEhZ088suScMs+LDC/M04W0bxjF3nr+7C+zCXmZ1hDKdXzqr2xC1pSrwic3Eb28iTHE7gufPEDibgtyIxy73/CJXVOc2YL8tn5yrxE+WZB8dRMi39/+8ZJkcsgDpPtmGyU5ZM2AI54HyL7MO2veZFjnAQr2rLOU8IZzLIr2nHMog4Zy1kJ7xnmA0oZwlmID33Qob+jmQYWBbuYKMGTToeZQrbtmqGSs4k2dWwHwfxkimaugr2E/4ujU8Eio4005uyI6GRYZUflqMbsisWeRQy1nSi1IbklkqYW20COkOw7pUN2VGZHbUsiDetgVWbMchcwbGEo8RH7LIGED3uR1KOgI5Ddssc+bS6Dlj1ebcCbHoqjjj3UTaHPmMtjRx0ZdhkXhgWNg0uZSMBzTJ1kU7znGm5RDOW8oBm1Ch4otx7iEBzUcx8DE1hrYcUwfGVF14JjJHPtQxxuKQXvEonLLMe7IoZbjGJhP1lr4jmN6ERlR/W+OcSIy14uTC1rpwEKDPKAhbxYawhfgoU3/7z/+4z/+O+Kv/yvR+L//3k7f8lzDFFY7wxSddkKVynmuCES06Oa4YtZt4IoXup1xxahbyxVd0MwLWS6aObZ4odkZW4yaNWwhi15O6PKFXqd80QWtvBDmS61OGUNWnSahzFGnljPklUZfCWu+1udr4c3HQZmfhDntu6DI3Z2w57XPlLwg//uf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//mf//+VAgA=';
    }

    if ($type) {
        echo $Lazyload;
    } else {
        return $Lazyload;
    }
}

/**
 * 获取头像懒加载图
 * @param  boolean $type
 * @return string
 */
function getAvatarLazyload($type = true)
{
    $avatarBase64 = 'data:image/png;base64,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';
    if ($type) {
        echo $avatarBase64;
    } else {
        return $avatarBase64;
    }
}

/**
 * 获取随机图
 * @param bool $type 类型
 * @return string
 */
function getRandImg($type = true)
{
    $randImg = getAssets('assets/images/scenery/' . rand(1, 20) . '.webp', false);
    if ($type) {
        echo $randImg;
    } else {
        return $randImg;
    }
}

/**
 * 页面开始计时
 * @return bool
 */
function startCountTime()
{
    global $timeStart;
    $mTime = explode(' ', microtime());
    $timeStart = $mTime[1] + $mTime[0];
    return true;
}

/**
 * 页面结束计时
 * @param int $precision 精确度
 * @return string
 */
function endCountTime($precision = 3)
{
    global $timeStart, $timeEnd;
    $mTime = explode(' ', microtime());
    $timeEnd = $mTime[1] + $mTime[0];
    $timeTotal = number_format($timeEnd - $timeStart, $precision);
    echo $timeTotal < 1 ? $timeTotal * 1000 . 'ms' : $timeTotal . 's';
}

/**
 * 评论添加@
 * @param int $coid 评论ID
 * @param string $type 类型
 */
function getCommentAt($coid, $type = 'html')
{
    $db = Typecho_Db::get();
    $prow = $db->fetchRow($db->select('parent')->from('table.comments')->where('coid = ? AND status = ?', $coid, 'approved')); // 获取父评论信息
    $parent = @$prow['parent'];
    if ($prow && $parent != '0') {
        try {
            $arow = $db->fetchRow($db->select('author,url')->from('table.comments')->where('coid = ? AND status = ?', $parent, 'approved'));
            if ($type == 'html' && $arow) {
                echo '<span class="repy-to-author mr-1"><a href="' . $arow['url'] . '" title="' . $arow['author'] . '">@' . $arow['author'] . '</a>: </span>';
            } elseif ($type == 'a' && $arow) {
                echo '<a href="' . $arow['url'] . '" title="' . $arow['author'] . '">' . $arow['author'] . '</a>';
            }
        } catch (Exception $e) {
            echo ''; // 防止报错
        }
    }
}

/**
 * 解析头像
 * @param bool $email邮箱
 * @return string 头像URL
 */
function getGravatar($email)
{
    $defaultAvatar = Helper::options()->Gravatars; // 默认头像
    $lowercaseEmail = strtolower($email); // 转为小写
    $emailHash = md5($lowercaseEmail);
    $cleanedEmail = str_replace('@qq.com', '', $lowercaseEmail);

    if (strstr($lowercaseEmail, 'qq.com') && is_numeric($cleanedEmail) && strlen($cleanedEmail) < 11 && strlen($cleanedEmail) > 4) {
        $avatarUrl = '//thirdqq.qlogo.cn/g?b=qq&nk=' . $cleanedEmail . '&s=100';
    } elseif (! empty($defaultAvatar)) {
        $avatarUrl = 'https://' . $defaultAvatar . '/' . $emailHash . '?d=mm';
    } else {
        $avatarUrl = getAvatarLazyload(false); // 默认头像
    }

    return $avatarUrl;
}

/**
 * 生成goLins外链
 * @param string $url 链接
 * @return string URL链接
 */
function getGoLink($url)
{
    // 判断是否开启
    if (empty(Helper::options()->isGoLink)) {
        return $url;
    }

    $siteUrl = Helper::options()->siteUrl; // 获取本站URL
    $siteUrl = rtrim($siteUrl, '/'); // 去除末尾的斜杠

    // 判断是否为本站链接
    if (strpos($url, $siteUrl) === 0) {
        return $url;
    }

    // 判断是否为 http 或 https 链接
    if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
        return $url;
    }

    // 对url进行Base64加密
    $encodedUrl = base64_encode($url);

    // 构造新的href
    $newUrl = $siteUrl . '/golink?target=' . urlencode($encodedUrl);

    return $newUrl;
}

/**
 * 评论者主页链接新窗口打开
 * @param object $obj 评论对象
 * @param bool $autoLink 是否自动输出链接
 * @param bool $noFollow 是否自动添加nofollow
 * @return void 输出HTML
 */
function CommentAuthor($obj, $autoLink = NULL, $noFollow = NULL)
{
    $options = Helper::options();
    $autoLink = $autoLink ? $autoLink : $options->commentsShowUrl;
    $noFollow = $noFollow ? $noFollow : $options->commentsUrlNofollow;
    if ($obj->url && $autoLink) {
        echo '<a href="' . $obj->url . '"' . ($noFollow ? '
  rel="external nofollow"' : NULL) . (strstr($obj->url, $options->index) == $obj->url ? NULL : ' target="_blank"') . '
  title="' . $obj->author . '">'
            . $obj->author . '</a>';
    } else {
        echo $obj->author;
    }
}

/**
 * 过滤表情包
 * @param string $text 文本内容
 * @param bool $type 是否输出
 * @return string
 */
function formatEmoji($text, $type = true)
{
    $text = preg_replace_callback(
        '/\:\(owo=(3dyanjing|lpl|aoye|baozha|buhaoyisi|qinqin|sanbing|yiqiangxiao|toutoukan|zaijian|chujiaren|jiaban|mianqiangxiao|weixian|fahongbao|chishou|chigua|tuxie|chaojia|youhou|ziyaxiao|hashiqi|hashiqishiquyishi|hashiqishiwang|kuqi|changge|xihuan|heiha|daxiao|shiwang|toutu|fendou|haoqi|haode|haixiu|xiaochou|xiaotou|ganga|yingyuan|kaixin|yinqibushi|weixiao|sikao|exin|jingxia|jingya|gandong|fennu|wokanhaoni|shoujixiangji|damie|dapai|tuosai|fue|koubi|taiyanjing|wuzuixiao|wulian|cahan|doujiyan|zhihuideyanshen|yuebing|youmeiyougaocuo|leiben|shensi|huaji|huajiheshui|huajinaicha|huajiningmeng|huajikuanghan|huajibeizi|fannao|xiongmao|xiongmaochangge|xiongmaoxihuan|xiongmaoshiwang|niunianjinbao|goutou|goutouweibo|goutoushiwang|goutoupangci|goutouhua|goutoucao|zhutou|shengbing|dianhua|yiwen|tengtong|kanchuanyiqie|xuanyun|shuijiao|jinyan|xiaoku|jiujie|lvmao|shuaku|huzi|caigou|caigouhua|beida|liekai|songfu|songhua|yinxian|nanyizhixin|guilian|heixian|guzhang)\)/is',
        function ($match) use ($type) {
            if ($type) {
                return '<img class="emoji-image lazy" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
  data-original="' . getAssets('assets/emoji/Heo/', false) . $match[1] . '.webp" alt="' . $match[1] . '" no-view />';
            } else {
                return '<img class="emoji-image" src="' . getAssets('assets/emoji/Heo/', false) . $match[1] . '.webp"
  alt="' . $match[1] . '" style="width:20px;height:auto;" no-view />';
            }
        },
        $text
    );

    return $text;
}

/**
 * 判断当前页面是否属于某个分类（包括子分类）
 * @param array $children 子分类数组
 * @return bool 是否属于某个分类
 */
function isCategoryOrChildActive($children = array())
{
    try {
        // 检查是否是子分类
        foreach ($children as $child) {
            if (Typecho_Widget::widget('Widget_Archive')->is('category', $child['slug'])) {
                return true;
            }
        }

        return false;
    } catch (Exception $e) {
        return false;
    }
}

/** 
 * 判断是否为同一路径
 * @param string $url1 第一个URL
 * @param string $url2 第二个URL
 * @return bool 是否为同一路径
 */
function isSamePath($url1, $url2)
{
    $path1 = parse_url($url1, PHP_URL_PATH);
    $path2 = parse_url($url2, PHP_URL_PATH);
    return $path1 === $path2;
}

/** 
 * 自定义菜单导航栏
 * @param string $currentUrl 当前页面URL
 * @return string 导航栏HTML
 */
function getCustomMenu($currentUrl = '')
{
    $navbarInfo = Helper::options()->navbarInfo;
    if (empty($navbarInfo)) {
        return '';
    }

    $cnavbarArray = json_decode('[' . trim($navbarInfo) . ']', true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return '导航栏菜单 JSON 解析失败: ' . json_last_error_msg();
    }

    $navhtml = '';
    foreach ($cnavbarArray as $item) {
        $isActive = isSamePath($currentUrl, $item['link']);
        if (! $isActive && isset($item['sub'])) {
            foreach ($item['sub'] as $subItem) {
                if (isSamePath($currentUrl, $subItem['link'])) {
                    $isActive = true;
                    break;
                }
            }
        }

        $navhtml .= '<li class="nav-item ' . ($item['class'] ?? '') . '">';
        $navhtml .= '<a class="nav-a ' . ($isActive ? 'active' : '') . '" href="' . $item['link'] . '"
    target="' . $item['target'] . '" title="' . $item['name'] . '">' . $item['name'] . (isset($item['sub'])
            ? '<i class="iconfont nav-icon icon-xiala"></i>' :
            '') . '</a>';

        if (isset($item['sub']) && is_array($item['sub'])) {
            $navhtml .= '<div class="pt-md-4 sub-menu">';
            $navhtml .= '<ul class="d-md-flex flex-md-wrap p-md-2">';
            foreach ($item['sub'] as $subItem) {
                $subIsActive = isSamePath($currentUrl, $subItem['link']);
                $navhtml .= '<li class="p-2 ' . ($subIsActive ? 'active' : '') . ' ' . ($subItem['class'] ?? '') . '"><a
          href="' . $subItem['link'] . '" target="' . $subItem['target'] . '" title="' . $subItem['name'] . '">' .
                    $subItem['name'] . '</a></li>';
            }
            $navhtml .= '</ul>
  </div>';
        }
        $navhtml .= '</li>';
    }

    return $navhtml;
}

/**
 * 获取文章图片链接（支持单张或多张）
 *
 * @param object $archive  文章对象，需包含 fields->thumb 和 content 属性
 * @param int    $imgnum   图片索引（0-based）。0 表示只取第一张，返回字符串；
 *                         正数表示取前 $imgnum+1 张，返回数组。
 * @param bool   $israndom 是否随机抽取图片（传递给 extractImageLinks）
 *
 * @return string|array 当 $imgnum == 0 时返回单个图片URL（字符串），
 *                       当 $imgnum > 0 时返回包含图片URL的数组。
 *                       若无图片则返回空字符串或空数组。
 */
function getImgLink($archive, $imgnum = 0, $israndom = true)
{
    // 1. 获取自定义图片列表（优先使用）
    $customImageUrls = [];
    if (isset($archive->fields->thumb) && !empty($archive->fields->thumb)) {
        $thumb = $archive->fields->thumb;
        // 按行分割，清理空白行
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $thumb));
        $customImageUrls = array_filter(array_map('trim', $lines));
        $customImageUrls = array_values($customImageUrls);
    }

    // 当 $imgnum == 0 | 1 时，直接返回第一张图片
    if (!empty($customImageUrls) && $imgnum <= 1) {
        // 获取第一张图片
        $firstImageUrl = $customImageUrls[0] ?? '';
        return $firstImageUrl;
    }

    // 4. 如果自定义图片不足，从文章内容中补充
    $remainingNeeded = $imgnum - count($customImageUrls);
    if ($remainingNeeded >= 0 && (count($customImageUrls) == 0 || count($customImageUrls) != $imgnum)) {
        // 准备文章内容
        $content = $archive->content ?? '';
        $content = $archive->is('post') ? $content : parseContens($archive->content);

        // 从内容中提取图片或者随机缩略图图片
        $contentImages = extractImageLinks($content, $remainingNeeded, $israndom);

        // 合并结果
        $customImageUrls = array_merge($customImageUrls, $contentImages);
    }

    // 返回图片数组,0则为字符串
    return $imgnum > 1 ? $customImageUrls : $customImageUrls[0];
}
/**
 * 判断评论敏感词是否在字符串内
 * @param string $words_str 敏感词字符串，多个敏感词用|分隔
 * @param string $str 待检查的字符串
 * @return bool 是否包含敏感词
 * */
function checkSensitiveWords($words_str, $str)
{
    $words = explode('|', $words_str);
    if (empty($words)) {
        return false;
    }
    foreach ($words as $word) {
        if (
            false
            !== strpos($str, trim($word))
        ) {
            return true;
        }
    }
    return false;
}

/**
 * 显示文章目录
 * @param string $content 文章内容
 * @return string 文章目录HTML
 */
function generateToc($content)
{
    // 匹配h1-h4标签，捕获：级别、id、标题内容
    $pattern = '/<h([1-4])(?:\s+[^>]*?id=["\']([^"\']*)["\'])?[^>]*>(.*?)<\/h\1>/is';
    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

    if (empty($matches)) {
        return '';
    }

    $stack = [];      // 栈，记录当前打开的标题级别（每个打开的<li>对应一个级别）
    $tocHtml = '';     // 内部列表HTML

    foreach ($matches as $match) {
        $level = (int)$match[1];
        $id    = $match[2] ?? '';
        $rawTitle = $match[3];

        // 跳过没有id或id为空的标题
        if (empty($id)) {
            continue;
        }

        // 清理标题，去除内部HTML标签并trim
        $title = trim(strip_tags($rawTitle));
        if ($title === '') {
            continue; // 空标题也跳过
        }

        // 构建a标签
        $aTag = sprintf(
            '<a class="atoc-link" href="#%s" data-target="%s" title="%s">%s</a>',
            htmlspecialchars($id),
            htmlspecialchars($id),
            htmlspecialchars($title),
            htmlspecialchars($title)
        );

        // 关闭所有级别大于当前级别的<li>，并适当关闭<ul>
        while (!empty($stack) && end($stack) > $level) {
            $popped = array_pop($stack);          // 弹出栈顶级别
            $tocHtml .= '</li>';                   // 关闭弹出的<li>
            // 如果弹出后栈非空且新栈顶 < 弹出的级别，说明需要关闭对应的<ul>
            if (!empty($stack) && end($stack) < $popped) {
                $tocHtml .= '</ul>';
            }
        }

        // 插入当前标题
        if (empty($stack)) {
            // 栈空：当前是顶层标题（直接在外层<ul>下）
            $tocHtml .= '<li class="atoc-item">' . $aTag;
        } else {
            $top = end($stack);
            if ($top < $level) {
                // 需要开启新的子列表
                $tocHtml .= '<ul class="sub-list ml-2"><li class="atoc-item">' . $aTag;
            } else {
                // 同级标题：关闭上一个<li>，开启新的<li>
                $tocHtml .= '</li><li class="atoc-item">' . $aTag;
            }
        }

        // 将当前级别压入栈
        array_push($stack, $level);
    }

    // 处理剩余的栈元素（关闭所有未闭合的标签）
    while (!empty($stack)) {
        $popped = array_pop($stack);
        $tocHtml .= '</li>';
        if (!empty($stack) && end($stack) < $popped) {
            $tocHtml .= '</ul>';
        }
    }

    return '<ul class="atoc-list">' . $tocHtml . '</ul>';
}

/**
 * 从HTML/Markdown内容中提取图片链接，不足时补充自定义/本地图片
 * 
 * @param string $content HTML/Markdown内容
 * @param int $imgnum 需要返回的图片数量
 * @param bool $israndom 是否补充随机图片
 * @return array 指定数量的图片链接数组
 */
function extractImageLinks($content, $imgnum, $israndom = true)
{
    // 初始化变量，提前处理参数合法性
    $targetImageCount = max((int)$imgnum, 1); // 确保数量为正整数
    $extractedImages = [];
    $content = trim($content);

    // 仅当内容非空时才进行提取
    if (!empty($content)) {
        // 使用DOM解析优先提取图片（处理HTML）
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        // 处理UTF-8编码，添加基础HTML结构避免解析错误
        $dom->loadHTML('<!DOCTYPE html><meta charset="UTF-8">' . $content);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $imgNodes = $xpath->query('//img');

        // 遍历图片节点提取链接（仅过滤空值）
        foreach ($imgNodes as $imgNode) {
            if (count($extractedImages) >= $targetImageCount) break;

            // 优先懒加载属性，其次src，仅过滤空值
            $imageUrl = trim($imgNode->getAttribute('data-original') ?: $imgNode->getAttribute('src'));

            if (!empty($imageUrl)) {
                $extractedImages[] = $imageUrl;
            }
        }

        // 正则补充提取（处理Markdown或DOM解析遗漏的情况）
        $currentCount = count($extractedImages);
        if ($currentCount < $targetImageCount) {
            // 正则匹配规则（按优先级排序）
            $regexPatterns = [
                // 1. IMG标签：优先data-original，其次src
                '/<img.*?(?:data-original=["\'](.*?)["\']|src=["\'](.*?)["\'])[^>]*>/i',
                // 2. 内联Markdown图片 ![描述](链接)
                '/!\[.*?\]\((.*?)\)/i',
                // 3. 脚注式Markdown图片 [描述]: 链接
                '/\[.*?\]:\s*(.*?)/i'
            ];

            foreach ($regexPatterns as $patternIndex => $pattern) {
                if (count($extractedImages) >= $targetImageCount) break;

                if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        if (count($extractedImages) >= $targetImageCount) break;

                        // 处理不同正则的匹配结果，仅过滤空值
                        if ($patternIndex === 0) {
                            // IMG标签正则：优先取data-original组，无则取src组
                            $url = !empty($match[1]) ? trim($match[1]) : trim($match[2] ?? '');
                        } else {
                            // Markdown正则：直接取第一个捕获组
                            $url = trim($match[1] ?? '');
                        }

                        if (!empty($url)) {
                            $extractedImages[] = $url;
                        }
                    }
                }
            }
        }

        // 去重（避免DOM和正则提取到重复图片）
        $extractedImages = array_values(array_unique($extractedImages));
    }

    // 补充图片（仅当需要随机补充时）
    if ($israndom) {
        $currentCount = count($extractedImages);
        $needSupplementCount = $targetImageCount - $currentCount;

        if ($needSupplementCount > 0) {
            // 生成带随机参数的URL（防缓存）
            $addRandomParam = function (string $url): string {
                if (empty($url)) return $url;
                $separator = str_contains($url, '?') ? '&' : '?';
                return $url . $separator . 'rand=' . random_int(10000, 99999);
            };

            // 1. 补充自定义图片
            $customImages = [];
            $customThumbnailConfig = Helper::options()->customThumbnail ?? '';
            if (!empty($customThumbnailConfig)) {
                // 分割并清理自定义图片列表
                $customImages = explode("\n", str_replace(["\r\n", "\r"], "\n", $customThumbnailConfig));
                $customImages = array_filter(array_map('trim', $customImages));
                $customImages = array_values($customImages);
            }

            // 循环补充自定义图片
            if (!empty($customImages)) {
                $customImageCount = count($customImages);
                for ($i = 0; $i < $needSupplementCount; $i++) {
                    $extractedImages[] = $addRandomParam($customImages[$i % $customImageCount]);
                    if (count($extractedImages) >= $targetImageCount) break;
                }
                $needSupplementCount = $targetImageCount - count($extractedImages);
            }

            // 2. 补充本地兜底图片
            if ($needSupplementCount > 0) {
                for ($i = 0; $i < $needSupplementCount; $i++) {
                    $localImageNum = mt_rand(1, 20);
                    $localImageUrl = getAssets("assets/images/thumb/{$localImageNum}.webp", false);
                    $extractedImages[] = $addRandomParam($localImageUrl);
                }
            }
        }
    }

    // 最终返回指定数量的图片链接
    return array_slice($extractedImages, 0, $targetImageCount);
}

/**
 * 获取相邻文章
 *
 * @param object $widget 当前文章对象
 * @param string $direction 方向：'next' 下一篇，'prev' 上一篇
 * @param bool $sameCategory 是否限制在同一分类，默认 false（所有文章）
 * @return mixed 相邻文章的数据数组或 false
 */
function getAdjacentPost($widget, $direction, $sameCategory = false)
{
    $db = Typecho_Db::get();
    $select = $db->select()
        ->from('table.contents')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', $widget->type)
        ->where('table.contents.password IS NULL');

    // 限制同一分类（可选）
    if ($sameCategory && isset($widget->categories[0]['mid'])) {
        $select->join('table.relationships', 'table.contents.cid = table.relationships.cid')
            ->where('table.relationships.mid = ?', intval($widget->categories[0]['mid']))
            ->group('table.contents.cid'); // 避免多分类重复
    }

    // 根据方向设置比较和排序
    if ($direction === 'next') {
        $select->where('table.contents.created > ?', $widget->created)
            ->order('table.contents.created', Typecho_Db::SORT_ASC);
    } else {
        $select->where('table.contents.created < ?', $widget->created)
            ->order('table.contents.created', Typecho_Db::SORT_DESC);
    }

    $select->limit(1);
    $row = $db->fetchRow($select);

    return $row ?: false;
}

/**
 * 显示下一篇
 *
 * @param object $widget 当前文章对象
 * @param string|null $default 无下一篇时显示的内容
 * @param bool $sameCategory 是否限制在同一分类
 */
function theNext($widget, $default = null, $sameCategory = false)
{
    $next = getAdjacentPost($widget, 'next', $sameCategory);
    if ($next) {
        $post = Helper::widgetById('Contents', $next['cid']);
        $html = '<div class="next-content">';
        $html .=  '<a href="' . $post->permalink . '" title="' . $next['title'] . '">' . $next['title'] . '</a>';
        $html .= '</div>';
        echo $html;
    } else {
        echo $default;
    }
}

/**
 * 显示上一篇
 *
 * @param object $widget 当前文章对象
 * @param string|null $default 无上一篇时显示的内容
 * @param bool $sameCategory 是否限制在同一分类
 */
function thePrev($widget, $default = null, $sameCategory = false)
{
    $prev = getAdjacentPost($widget, 'prev', $sameCategory);
    if ($prev) {
        $post = Helper::widgetById('Contents', $prev['cid']);
        $html = '<div class="prev-content">';
        $html .=  '<a href="' . $post->permalink . '" title="' . $prev['title'] . '">' . $prev['title'] . '</a>';
        $html .= '</div>';
        echo $html;
    } else {
        echo $default;
    }
}
?>