<?php
/* ------------------------------------
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

use Utils\Helper;

?>
<?php
/* 获取主题当前版本号 */
function getVersion() {
    try {
        $themeInfo = Typecho_Plugin::parseInfo(dirname(__DIR__).'/index.php');
        return $themeInfo['version'];
    } catch (Exception $e) {
        return '获取主题版本失败'.$e;
    }
};

/* 获取资源路径 */
function getAssets($assets, $type = true) {
    $assetsURL = '';
    // 是否本地化资源
    if (Helper::options()->AssetsURL) {
        $assetsURL = Helper::options()->AssetsURL.'/'.$assets;
    } else {
        $assetsURL = Helper::options()->themeUrl.'/'.$assets;
    }
    if ($type) {
        echo $assetsURL;
    } else {
        return $assetsURL;
    }
}

/**
 * 初始化主题
 */
function themeInit($self) {
    // 设置评论排序为最新优先
    Helper::options()->commentsOrder = 'DESC';

    // 定义路由规则
    $routes = array(
        '/sitemap.xml' => 'sitemap.php',
        '/golink' => 'golink.php',
    );

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
        header('Location: '.randomPost('return'));exit;
    }

    // 添加文章锚点功能
    if ($self->is('single')) {
        $self->content = parseContens($self->content); // 解析内容
    }
}

/* 建站天数 */
function getWebsiteAgeInDays($launchDate) {
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

/* 获取管理员或站长是否在线 */
function isAdminOnline() {
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

/* 获取主题模式 */
function getThemeMode() {
    // 获取 Cookie 中的 theme 值
    $theme = $_COOKIE['theme'] ?? 'light'; // 默认值为 'light'
    $systemTheme = $_COOKIE['system_theme'] ?? '';
    $theme = $theme === 'system' ? $systemTheme : $theme;

    return htmlspecialchars($theme);
}

/* 获取文章浏览量 */
function PostViewCount($archive) {
    $postId = $archive->cid;
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    // 检查是否存在 views 字段
    $columns = $db->fetchRow($db->select()->from('table.contents'));
    if ( ! array_key_exists('views', $columns)) {
        $db->query('ALTER TABLE `'.$prefix.'contents` ADD `views` INT(10) DEFAULT 0;');
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
        if ( ! in_array($postId, $viewedPosts)) {
            $db->query($db->update('table.contents')->rows(array('views' => (int) $views + 1))->where('cid = ?', $postId));
            $views++;
            array_push($viewedPosts, $postId);
            Typecho_Cookie::set('extend_contents_views', implode(',', $viewedPosts)); // 记录查看 cookie
        }
    }

    echo $views;
}

/* 随机文章 */
function randomPost($type = 'echo') {
    $db = Typecho_Db::get();
    $result = $db->fetchRow($db->select()->from('table.contents')->where('type=?', 'post')->where('status=?', 'publish')->limit(1)->order('RAND()'));
    if ($result) {
        $f = Helper::widgetById('Contents', $result['cid']);
        $permalink = $f->permalink;
        if ($type == 'return') {return $permalink;} else {echo $permalink;}
    } else {
        if ($type == 'return') {return false;} else {echo '没有文章可随机';}
    }
}

/* 时间格式化 */
function ueTime($date) {
    $timestamp = strtotime($date->format('Y-m-d H:i:s'));
    $current_time = time();
    $time_diff = $current_time - $timestamp;

    // 几秒前
    if ($time_diff < 60) {
        return $time_diff.'秒前';
    }

    // 几分钟前
    if ($time_diff < 3600) {
        return floor($time_diff / 60).'分钟前';
    }

    // 几小时前
    if ($time_diff < 86400) {
        return floor($time_diff / 3600).'小时前';
    }

    // 几天前-》30天以内
    if ($time_diff < 2592000) {
        return floor($time_diff / 86400).'天前';
    }

    // 几个月前-》1年内
    if ($time_diff < 31536000) {
        return floor($time_diff / 2592000).'个月前';
    }

    // 超过3年显示具体日期
    return $date->format('Y-m-d');
}

/* 时间格式化Mini */
function ueTimeMini($date) {
    $current_year = date('Y');
    $date_year = $date->format('Y');

    // 如果是今年，返回 "月-日"
    if ($date_year == $current_year) {
        return $date->format('m-d');
    }

    // 否则返回 "年-月-日"
    return $date->format('Y-m-d');
}

/* 个人社交信息 */
function socialInfo() {
    $socialInfo = Helper::options()->socialInfo;
    $socialArray = json_decode('['.trim($socialInfo).']', true);
    $html = '';
    foreach ($socialArray as $social) {
        $html .= '<a href="'.htmlspecialchars($social['link']).'" title="'.htmlspecialchars($social['name']).'" target="_blank"><i class="iconfont '.htmlspecialchars($social['icon']).'"></i></a>';
    }

    return $html;
}

/* 获取全局懒加载图 */
function getLazyload($type = true) {
    $Lazyload = Helper::options()->lazyload;
    if ( ! $Lazyload) {
        $Lazyload = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    }
    if ($type) {
        echo $Lazyload;
    } else {
        return $Lazyload;
    }

}

/* 获取头像懒加载图 */
function getAvatarLazyload($type = true) {
    $avatarBase64 = 'data:image/jpeg;base64,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gQPmzjvvNN4Asj4wgKykirXDAEpmAJ3h3HDDDebmm282jUaj2MqWrNcTTzxh7r77brN///5cI8MAcuHK3RgDKKkB+GENDg76O+HM1VdfnXthy9LhxRdfNFu2bDEvvODvgs7/wADyM8vTAwMosQF0hnbBBReY6667zqxduzbP2va17auvvjp9ae/ZZ58VjQMDEOHr2RkDqIABdIZ43nnnmWuvvdZcc801PRe2Xw127NhhHn/8cfPcc88FGQIGEATjvCIYQIUMoDPUk08+2Vx11VVm3bp1pbh/4L333jNPPfWUefrpp80bb7wRtGIxgKA4jxDDACpoAN1DPvfcc83ll19u/IeMzj//6P1a9ltvvWW2bdtmnn/+eePf9bUeGIAW2UO6GEDFDaB7+EuXLjWrV682K1euNP68gTeHUI/du3cbf1z/0ksvGX9iL/Q7/XzjxABCreDcOhhAjQxg9lSOOeYYc+aZZ5rh4eHpKwreIE455RSzZMkSc+KJJ5oTTjjh8y7+ppyPPvrIfPjhh9OX6t59912zZ88e/yMnZufOneaDDz7QrcTyrQMfBurLiod90VJ/GCjsVOupRgLQXVcSQPneeXRXvGLqGIDugmEAGIBuhQnVMQAhwB7dMQAMQLfChOoYgBAgBlDsRzn6WHi6K14x9T6uAycBK1Yrcw2Xk4AVX0QMQHcBOQTgEEC3woTqGIAQIIcAHALolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCIAHolpCuOgagy5cEQALQrTChOgYgBEgCMCcUQbhp06blZ5xxxk8X6dvpMzIy8k+S/vQ1ps/r8Fjd16DuCaDu68f8ICAigAGI8NEZAtUmgAFUe/0YPQREBDAAET46Q6DaBDCAaq8fo4eAiAAGIMJHZwhUmwAGUO31Y/QQEBHAAET46AyBahPAAKq9foweAiICGIAIH50hUG0CGEC114/RQ0BEAAMQ4aMzBKpNAAOo9voxegiICGAAInx0hkC1CWAA1V4/Rg8BEQEMQISPzhCoNoH/B7lEP3mCxdXNAAAAAElFTkSuQmCC';
    if ($type) {
        echo $avatarBase64;
    } else {
        return $avatarBase64;
    }

}

/* 获取随机图 */
function getRandImg($type = true) {
    $randImg = getAssets('assets/images/scenery/'.rand(1, 20).'.webp', false);
    if ($type) {
        echo $randImg;
    } else {
        return $randImg;
    }
}

/* 页面开始计时 */
function startCountTime() {
    global $timeStart;
    $mTime = explode(' ', microtime());
    $timeStart = $mTime[1] + $mTime[0];
    return true;
}

/* 页面结束计时 */
function endCountTime($precision = 3) {
    global $timeStart, $timeEnd;
    $mTime = explode(' ', microtime());
    $timeEnd = $mTime[1] + $mTime[0];
    $timeTotal = number_format($timeEnd - $timeStart, $precision);
    echo $timeTotal < 1 ? $timeTotal * 1000 .'ms' : $timeTotal.'s';
}

/**
 * 评论添加 @
 * @param $coid
 * @return void
 */
function getCommentAt($coid, $type = 'html') {
    $db = Typecho_Db::get();
    $prow = $db->fetchRow(
        $db
            ->select('parent')
            ->from('table.comments')
            ->where('coid = ? AND status = ?', $coid, 'approved')
    );
    $parent = @$prow['parent'];
    if ($prow && $parent != '0') {
        $arow = $db->fetchRow(
            $db
                ->select('author,url')
                ->from('table.comments')
                ->where('coid = ? AND status = ?', $parent, 'approved')
        );
        if ($type == 'html') {
            echo '<span class="repy-to-author mr-1"><a href="'.$arow['url'].'" title="'.$arow['author'].'">@'.$arow['author'].'</a>: </span>';
        } elseif ($type == 'a') {
            echo '<a href="'.$arow['url'].'" title="'.$arow['author'].'">'.$arow['author'].'</a>';
        }
    }
}

/* 解析头像 */
function getGravatar($email) {
    $defaultAvatar = Helper::options()->Gravatars; // 默认头像
    $gravatarBaseUrl = 'https://'.$defaultAvatar.'/';
    $lowercaseEmail = strtolower($email); // 转为小写
    $emailHash = md5($lowercaseEmail);
    $cleanedEmail = str_replace('@qq.com', '', $lowercaseEmail);

    if (strstr($lowercaseEmail, 'qq.com') && is_numeric($cleanedEmail) && strlen($cleanedEmail) < 11 && strlen($cleanedEmail) > 4) {
        $avatarUrl = '//thirdqq.qlogo.cn/g?b=qq&nk='.$cleanedEmail.'&s=100';
    } else {
        $avatarUrl = $gravatarBaseUrl.$emailHash.'?d=mm';
    }

    return $avatarUrl;
}

/* 生成goLins外链 */
function getGoLink($url) {
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
    $newUrl = $siteUrl.'/golink?target='.urlencode($encodedUrl);

    return $newUrl;
}

/**
 * 评论者主页链接新窗口打开
 * 调用<?php CommentAuthor($comments); ?>
 */
function CommentAuthor($obj, $autoLink = NULL, $noFollow = NULL) {
    $options = Helper::options();
    $autoLink = $autoLink ? $autoLink : $options->commentsShowUrl;
    $noFollow = $noFollow ? $noFollow : $options->commentsUrlNofollow;
    if ($obj->url && $autoLink) {
        echo '<a href="'.$obj->url.'"'.($noFollow ? '
  rel="external nofollow"' : NULL).(strstr($obj->url, $options->index) == $obj->url ? NULL : ' target="_blank"').'
  title="'.$obj->author.'">'
        .$obj->author.'</a>';
    } else {
        echo $obj->author;
    }
}

/* 过滤表情包 */
function formatEmoji($text, $type = true) {
    $text = preg_replace_callback(
        '/\:\(owo=(3dyanjing|lpl|aoye|baozha|buhaoyisi|qinqin|sanbing|yiqiangxiao|toutoukan|zaijian|chujiaren|jiaban|mianqiangxiao|weixian|fahongbao|chishou|chigua|tuxie|chaojia|youhou|ziyaxiao|hashiqi|hashiqishiquyishi|hashiqishiwang|kuqi|changge|xihuan|heiha|daxiao|shiwang|toutu|fendou|haoqi|haode|haixiu|xiaochou|xiaotou|ganga|yingyuan|kaixin|yinqibushi|weixiao|sikao|exin|jingxia|jingya|gandong|fennu|wokanhaoni|shoujixiangji|damie|dapai|tuosai|fue|koubi|taiyanjing|wuzuixiao|wulian|cahan|doujiyan|zhihuideyanshen|yuebing|youmeiyougaocuo|leiben|shensi|huaji|huajiheshui|huajinaicha|huajiningmeng|huajikuanghan|huajibeizi|fannao|xiongmao|xiongmaochangge|xiongmaoxihuan|xiongmaoshiwang|niunianjinbao|goutou|goutouweibo|goutoushiwang|goutoupangci|goutouhua|goutoucao|zhutou|shengbing|dianhua|yiwen|tengtong|kanchuanyiqie|xuanyun|shuijiao|jinyan|xiaoku|jiujie|lvmao|shuaku|huzi|caigou|caigouhua|beida|liekai|songfu|songhua|yinxian|nanyizhixin|guilian|heixian|guzhang)\)/is',
        function ($match) use ($type) {
            if ($type) {
                return '<img class="emoji-image lazy" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
  data-original="'.getAssets('assets/emoji/Heo/', false).$match[1].'.webp" alt="'.$match[1].'" no-view />';
            } else {
                return '<img class="emoji-image" src="'.getAssets('assets/emoji/Heo/', false).$match[1].'.webp" alt="'.$match[1].'"
  style="width:20px;height:auto;" />';
            }
        },
        $text
    );

    return $text;
}

/**
 * @description: 父级菜单是否高亮
 * @param {*} $activeSlug 选中的菜单slug，也就是名称
 * @param {*} $category 父级分类信息
 * @param {*} $children 子级分类信息
 */
function isParentActive($activeSlug, $category, $children) {
    foreach ($children as $mid) {
        $child = $category->getCategory($mid);
        if ($child['slug'] === $activeSlug) {
            return true;
        }
    }
    return false;
}

// 判断是否为同一路径
function isSamePath($url1, $url2) {
    $path1 = parse_url($url1, PHP_URL_PATH);
    $path2 = parse_url($url2, PHP_URL_PATH);
    return $path1 === $path2;
}

/** 自定义菜单导航栏 */
function getCustomMenu($currentUrl = '') {
    $navbarInfo = Helper::options()->navbarInfo;
    if (empty($navbarInfo)) {
        return '';
    }

    $cnavbarArray = json_decode('['.trim($navbarInfo).']', true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return '导航栏菜单 JSON 解析失败: '.json_last_error_msg();
    }

    $navhtml = '';
    foreach ($cnavbarArray as $item) {
        $isActive = isSamePath($currentUrl, $item['link']);
        if ( ! $isActive && isset($item['sub'])) {
            foreach ($item['sub'] as $subItem) {
                if (isSamePath($currentUrl, $subItem['link'])) {
                    $isActive = true;
                    break;
                }
            }
        }

        $navhtml .= '<li class="nav-item">';
        $navhtml .= '<a class="nav-a '.($isActive ? 'active' : '').'" href="'.$item['link'].'" target="'.$item['target'].'"
    title="'.$item['name'].'">'.$item['name'].(isset($item['sub']) ? '<i class="iconfont nav-icon icon-xiala"></i>' :
            '').'</a>';

        if (isset($item['sub']) && is_array($item['sub'])) {
            $navhtml .= '<div class="sub-menu">
    <ul>';
            foreach ($item['sub'] as $subItem) {
                $subIsActive = isSamePath($currentUrl, $subItem['link']);
                $navhtml .= '<li class="'.($subIsActive ? 'active' : '').'"><a href="'.$subItem['link'].'"
          target="'.$subItem['target'].'" title="'.$subItem['name'].'">'.$subItem['name'].'</a></li>';
            }
            $navhtml .= '</ul>
  </div>';
        }
        $navhtml .= '</li>';
    }

    return $navhtml;
}

/** 处理文章缩略图 */
function getThumbnails($contx, $imgnum) {
    $rand = rand(1, 20);
    $pattern = '/\<img.*?src\=\"(.*?)\"[^>]*>/i';
    $patternMD = '/\!\[.*?\]\((http(s)?:\/\/.*?(jpg|jpeg|png))/i';
    $patternMDfoot = '/\[.*?\]:\s*(http(s)?:\/\/.*?(jpg|jpeg|png))/i';
    //如果文章内有插图，则调用插图
    if (preg_match_all($pattern, $contx, $thumbUrl)) {
        return $thumbUrl[1][$imgnum];
    }
    //如果是内联式markdown格式的图片
    elseif (preg_match_all($patternMD, $contx, $thumbUrl)) {
        return $thumbUrl[1][$imgnum];
    }
    //如果是脚注式markdown格式的图片
    elseif (preg_match_all($patternMDfoot, $contx, $thumbUrl)) {
        return $thumbUrl[1][$imgnum];
    }
    //如果真的没有图片，就调用一张随机图片
    else {
        $adimg = getAssets("assets/images/thumb/$rand.webp", false); // 缩略图路径
        return $adimg;
    }
}

function getImgLink($archive) {
    $thumb = $archive->fields->thumb;
    if ( ! $thumb) {
        $thumb = getThumbnails($archive->content, 0);
    }
    return $thumb;
}

// 显示文章目录
function getCatalog() {
    global $catalog;

    if ( ! $catalog) {
        return '暂无目录';
    }

    $str = '<ul class="atoc-list">'."\n";
    $prev_depth = '';
    $to_depth = 0;
    foreach ($catalog as $catalog_item) {
        $catalog_depth = $catalog_item['depth'];
        if ($prev_depth) {
            if ($catalog_depth == $prev_depth) {
                $str .= '</li>'."\n";
            } elseif ($catalog_depth > $prev_depth) {
                $to_depth++;
                $str .= '<ul class="sub-list ml-2">'."\n";
            } else {
                $to_depth2 = ($to_depth > ($prev_depth - $catalog_depth)) ? ($prev_depth - $catalog_depth) : $to_depth;
                if ($to_depth2) {
                    for ($i = 0; $i < $to_depth2; $i++) {$str .= '</li>'."\n".'</ul>'."\n";
                        $to_depth--;}
                }
                $str .= '</li>';
            }
        }
        $str .= '<li class="atoc-item"><a class="atoc-link" href="#cl-'.$catalog_item['count'].'"
            data-target="cl-'.$catalog_item['count'].'"
            title="'.$catalog_item['text'].'">'.$catalog_item['text'].'</a>';
        $prev_depth = $catalog_item['depth'];
    }
    for ($i = 0; $i <= $to_depth; $i++) {$str .= '</li>'."\n".'</ul>'."\n"
        ;}
    $str = '<section class="toc">'."\n"."\n".$str.'</section>'."\n";

    return $str;
}

/* 判断评论敏感词是否在字符串内 */
function checkSensitiveWords($words_str, $str) {
    $words = explode('|', $words_str);
    if (empty($words)) {
        return false;
    }
    foreach ($words as $word) {
        if (false !== strpos($str, trim($word))) {
            return true;
        }
    }
    return false;
}

?>