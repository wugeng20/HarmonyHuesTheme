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
    // 同一请求内只解析一次主题头（原先每页最多调用 14 次，每次都读文件+正则）
    static $version = null;

    if ($version !== null) {
        return $version;
    }

    try {
        $themeInfo = Typecho_Plugin::parseInfo(dirname(__DIR__) . '/index.php');
        $version = (string) ($themeInfo['version'] ?? '');
    } catch (Exception $e) {
        $version = '获取主题版本失败' . $e;
    }

    return $version;
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
    $assetPath = ltrim((string) $assetPath, '/');
    // 模板里手工拼接的 ?v=版本号在这里统一剥离，改用文件修改时间做缓存标记，
    // 这样重新生成 .min.css / .min.js 后不用改主题版本号，浏览器也会自动取新文件
    $assetPath = preg_replace('/[?&]v=[^&#]*/', '', $assetPath);
    $version = '';
    $file = dirname(__DIR__) . '/' . $assetPath;
    if (is_file($file)) {
        $version = (string) filemtime($file);
    }
    $fullUrl = rtrim($baseUrl, '/') . '/' . $assetPath;
    if ($version !== '') {
        $fullUrl .= (strpos($fullUrl, '?') === false ? '?' : '&') . 'v=' . $version;
    }
    if ($echo) {
        echo $fullUrl;
    } else {
        return $fullUrl;
    }
}

/**
 * 生成站内绝对路径，兼容子目录部署
 * @param  string $path
 * @return string
 */
function getSitePath($path = '')
{
    $siteUrl = rtrim((string) Helper::options()->siteUrl, '/');
    return $siteUrl . '/' . ltrim((string) $path, '/');
}

/**
 * 检测 contents 表 views 字段是否可用
 *
 * 同一请求内只探测一次，避免每次调用都取一整行来判断字段是否存在；
 * 字段缺失时仅在已登录用户访问时尝试建列，避免匿名流量触发 DDL。
 *
 * @return bool
 */
function ensureViewsColumn()
{
    static $state = null;

    if ($state !== null) {
        return $state;
    }

    $db = Typecho_Db::get();

    try {
        $db->fetchRow($db->select('views')->from('table.contents')->limit(1));
        return $state = true;
    } catch (Exception $e) {
        // views 字段不存在，继续尝试创建
    }

    try {
        if (! Typecho_Cookie::get('__typecho_uid')) {
            return $state = false;
        }

        $db->query('ALTER TABLE `' . $db->getPrefix() . 'contents` ADD `views` INT(10) DEFAULT 0;');
        return $state = true;
    } catch (Exception $e) {
        return $state = false;
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

    // PC端首页单栏模式布局的文章数量，不太友好，先注释掉
    // if ($options->indexLayout && !isMobile() && $self->parameter->pageSize < 10) {
    //     $self->parameter->pageSize += 3;
    // }

    // 手机文章数量
    if (isMobile() && $self->parameter->pageSize % 2 != 0) {
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

    // 文章页面
    if ($self->is('single')) {
        // 添加文章锚点功能
        $self->content = parseContens($self->content); // 解析内容

        // 优化文章页面的meta标签description内容
        if (!empty($self->fields->abstract || $self->fields->description)) {
            $archiveDescription = ($self->fields->abstract ?: $self->fields->description);
            $self->setArchiveDescription($archiveDescription);
        }
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

    return htmlspecialchars($theme ?? '');
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

    $logoImg = getThemeMode() == 'light' ? '<img class="' . $class . '" src="' . $whiteSrc . '" dark-src="' . $darkSrc . '" draggable="false" alt="' . $alt . '">' : '<img class="' . $class . '" src="' . $darkSrc . '" white-src="' . $whiteSrc . '" draggable="false" alt="' . $alt . '">';

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

    // views 字段不可用时直接输出 0，不在这里做建表操作
    if (! ensureViewsColumn()) {
        echo 0;
        return;
    }

    $db = Typecho_Db::get();

    // 获取当前文章的views值
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $postId));
    $views = (int) ($row['views'] ?? 0);

    // 文章正文页面，更新views值
    if ($archive->is('single')) {
        // 查看过的文章ID Cookie值：10,11,22,33
        $cookieViewIds = Typecho_Cookie::get('extend_contents_views');
        $viewedPosts = empty($cookieViewIds) ? array() : explode(',', $cookieViewIds);

        // 如果当前文章未被查看过，则更新views值
        if (! in_array($postId, $viewedPosts)) {
            $db->query($db->update('table.contents')->expression('views', 'views + 1')->where('cid = ?', $postId));
            $viewedPosts[] = $postId;
            // 限制 Cookie 长度，避免长期浏览后无限增长
            $viewedPosts = array_slice($viewedPosts, -200);
            Typecho_Cookie::set('extend_contents_views', implode(',', $viewedPosts)); // 更新cookie中的查看过的文章ID数组
            $views++; // 更新views值
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

    // 先取总数再用随机 offset 命中一行，避免 ORDER BY RAND() 对全表排序
    $totalRow = $db->fetchObject($db->select(array('COUNT(*)' => 'num'))
        ->from('table.contents')
        ->where('type = ?', 'post')
        ->where('status = ?', 'publish'));
    $total = (int) ($totalRow->num ?? 0);

    $permalink = false;
    if ($total > 0) {
        $row = $db->fetchRow($db->select('cid', 'title', 'slug', 'created', 'type')
            ->from('table.contents')
            ->where('type = ?', 'post')
            ->where('status = ?', 'publish')
            ->offset(random_int(0, $total - 1))
            ->limit(1));

        if ($row) {
            $permalink = getPermalinkFromRow($row);
        }
    }

    if ($permalink) {
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
        $html .= '<a href="' . htmlspecialchars($social['link'] ?? '') . '" title="' . htmlspecialchars($social['name'] ?? '') . '" class="d-flex align-items-center justify-content-center" target="_blank"><i class="iconfont ' . htmlspecialchars($social['icon'] ?? '') . '"></i></a>';
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
        $Lazyload = getAssets('assets/images/loadimg.webp', false);
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
    $avatarBase64 = getAssets('assets/images/avatarimg.webp', false);
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
 * 扫描 assets/emoji 下的表情包目录（一个子目录 = 一个表情包）
 *
 * 目录内可放 info.json（可选）：
 * {"label": "显示名", "mode": "emoji|image", "order": 0, "titles": {"文件名": "中文名"}}
 * - mode=emoji：点击后插入 :(owo=文件名)，由 formatEmoji() 渲染成行内表情
 * - mode=image：点击后插入 Markdown 图片，适合动图/大表情
 * 没有 info.json 时：label=目录名、mode=emoji、标题=文件名
 *
 * @return array
 */
function getEmojiPacks()
{
    static $packs = null;
    if ($packs !== null) {
        return $packs;
    }

    $packs = array();
    $baseDir = dirname(__DIR__) . '/assets/emoji/';
    if (!is_dir($baseDir)) {
        return $packs;
    }

    $allowExt = array('webp', 'gif', 'png', 'jpg', 'jpeg', 'svg', 'avif', 'bmp');
    $dirs = array();
    foreach ((array) scandir($baseDir) as $dir) {
        if ($dir === '.' || $dir === '..' || !is_dir($baseDir . $dir)) {
            continue;
        }
        $dirs[] = $dir;
    }
    sort($dirs, SORT_NATURAL | SORT_FLAG_CASE);

    $result = array();
    foreach ($dirs as $dir) {
        $info = array();
        $infoFile = $baseDir . $dir . '/info.json';
        if (is_file($infoFile)) {
            // 兼容带 BOM 的 UTF-8 文件，json_decode 遇到 BOM 会直接失败
            $decoded = json_decode(ltrim((string) file_get_contents($infoFile), "\xEF\xBB\xBF"), true);
            if (is_array($decoded)) {
                $info = $decoded;
            }
        }
        $titles = isset($info['titles']) && is_array($info['titles']) ? $info['titles'] : array();

        $files = array();
        foreach ((array) scandir($baseDir . $dir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $filePath = $baseDir . $dir . '/' . $file;
            if (!is_file($filePath)) {
                continue;
            }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowExt, true)) {
                continue;
            }
            $files[pathinfo($file, PATHINFO_FILENAME)] = $file;
        }
        if (!$files) {
            continue;
        }

        // 先按 info.json 里 titles 的顺序排，剩下的按文件名自然排序
        $items = array();
        foreach ($titles as $key => $title) {
            if (!isset($files[$key])) {
                continue;
            }
            $items[] = array('key' => (string) $key, 'title' => (string) $title, 'file' => $files[$key]);
            unset($files[$key]);
        }
        ksort($files, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($files as $key => $file) {
            $items[] = array('key' => (string) $key, 'title' => (string) $key, 'file' => $file);
        }

        $result[] = array(
            'name' => $dir,
            'label' => isset($info['label']) ? (string) $info['label'] : $dir,
            'mode' => (isset($info['mode']) && in_array($info['mode'], array('emoji', 'image'), true)) ? $info['mode'] : 'emoji',
            'order' => isset($info['order']) ? (int) $info['order'] : 999,
            'path' => getAssets('assets/emoji/' . $dir . '/', false),
            'items' => $items,
        );
    }

    usort($result, function ($a, $b) {
        if ($a['order'] === $b['order']) {
            return strcasecmp($a['name'], $b['name']);
        }
        return $a['order'] < $b['order'] ? -1 : 1;
    });

    $packs = $result;
    return $packs;
}

/**
 * 过滤表情包
 * @param string $text 文本内容
 * @param bool $type 是否延迟加载
 * @return string
 */
function formatEmoji($text, $type = true)
{
    static $map = null;
    if ($map === null) {
        $map = array();
        foreach (getEmojiPacks() as $pack) {
            if ($pack['mode'] !== 'emoji') {
                continue;
            }
            foreach ($pack['items'] as $item) {
                $key = strtolower($item['key']);
                if (!isset($map[$key])) {
                    $map[$key] = $pack['path'] . $item['file'];
                }
            }
        }
    }
    if (!$map) {
        return $text;
    }

    return preg_replace_callback(
        '/\:\(owo=([a-zA-Z0-9_\-]+)\)/i',
        function ($match) use ($type, $map) {
            $key = strtolower($match[1]);
            if (!isset($map[$key])) {
                return $match[0];
            }
            $url = htmlspecialchars($map[$key], ENT_QUOTES, 'UTF-8');
            $alt = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
            if ($type) {
                return '<img class="emoji-image lazy" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
  data-original="' . $url . '" alt="' . $alt . '" no-view />';
            }
            return '<img class="emoji-image" src="' . $url . '"
  alt="' . $alt . '" style="width:20px;height:auto;" no-view />';
        },
        $text
    );
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
function getPostCoverImage($archive)
{
  if (! $archive->is('post')) {
    return '';
  }
  $raw = trim((string) $archive->fields->thumb);
  if ($raw === '') {
    return '';
  }
  $lines = preg_split('/\r\n|\r|\n/', $raw);
  return trim((string) ($lines[0] ?? ''));
}

/**
 * 文章页是否启用封面（hero）布局
 *
 * 条件：文章有封面图，且主题选项“文章页面封面图”未关闭
 *
 * @param \Widget\Archive $archive
 * @return bool
 */
function usePostCover($archive)
{
  if (getPostCoverImage($archive) === '') {
    return false;
  }
  return (string) Helper::options()->postCover !== '0';
}

function getImgLink($archive, $imgnum = 0, $israndom = true)
{
    // 1. 获取自定义图片列表（优先使用）
    $customImageUrls = [];
    $thumb = $archive->fields->thumb ?? '';
    if ($thumb) {
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
        $content = (string) ($archive->content ?? '');

        if (! $archive->is('post')) {
            // 列表卡片场景：先用正则直接取首图，命中就跳过 parseContens() + DOMDocument
            // （首页 10 张卡片原先是 10 次 Markdown 渲染 + 10 次 DOM 解析）
            if ($imgnum <= 1 && preg_match('/<img[^>]*\bdata-original=["\']([^"\']+)["\']/i', $content, $m) === 1) {
                $firstImage = trim($m[1]);
                if ($firstImage !== '') {
                    return $firstImage;
                }
            }

            if ($imgnum <= 1 && preg_match('/<img[^>]*\bsrc=["\']([^"\']+)["\']/i', $content, $m) === 1) {
                $firstImage = trim($m[1]);
                if ($firstImage !== '') {
                    return $firstImage;
                }
            }

            if ($imgnum <= 1 && preg_match('/!\[[^\]]*\]\(\s*([^)\s]+)/i', $content, $m) === 1) {
                $firstImage = trim($m[1]);
                if ($firstImage !== '') {
                    return $firstImage;
                }
            }

            $content = parseContens($archive->content);
        }

        // 从内容中提取图片或者随机缩略图图片
        $contentImages = extractImageLinks($content, $remainingNeeded, $israndom);

        // 合并结果
        $customImageUrls = array_merge($customImageUrls, $contentImages);
    }

    // 返回图片数组,0则为字符串
    return $imgnum > 1 ? $customImageUrls : $customImageUrls[0];
}

/**
 * 判断当前页面是否需要加载 svg-icon.min.js（约 86KB）
 *
 * 只有「关于我」页面模板与含 [cloud] 网盘短代码的文章会输出 data-svg。
 * header.php 用它决定是否加载脚本，footer.php 用它在未加载时补一个 showSvg 空实现。
 *
 * @param  object $archive
 * @return bool
 */
function needsSvgIcon($archive)
{
    static $need = null;

    if ($need !== null) {
        return $need;
    }

    if ($archive->is('page') && ($archive->template ?? '') === 'aboutme.php') {
        return $need = true;
    }

    if ($archive->is('post') || $archive->is('page')) {
        return $need = (bool) preg_match('/\[cloud(?=[\s\]])/', (string) $archive->content);
    }

    return $need = false;
}

/**
 * 由数据库行直接拼出文章/页面永久链接
 * 用于替代「先查 cid 再 Helper::widgetById() 取 permalink」的 N+1 写法
 * @param  array|object $row 至少包含 cid、type、slug、created
 * @return string
 */
function getPermalinkFromRow($row)
{
    $row = (array) $row;

    if (empty($row['cid'])) {
        return '';
    }

    $type = $row['type'] ?? 'post';
    $created = (int) ($row['created'] ?? 0);

    // 日期型固定链接需要 year/month/day 占位参数
    if ($created > 0) {
        $row['year'] = date('Y', $created);
        $row['month'] = date('m', $created);
        $row['day'] = date('d', $created);
    }

    try {
        $path = Typecho_Router::url($type, $row);
    } catch (Exception $e) {
        return '';
    }

    if (empty($path) || $path === '#') {
        return '';
    }

    return Typecho_Common::url($path, Helper::options()->index);
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
            htmlspecialchars($id ?? ''),
            htmlspecialchars($id ?? ''),
            htmlspecialchars($title ?? ''),
            htmlspecialchars($title ?? '')
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