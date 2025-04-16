<?php
/* ------------------------------------
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-12-21 17:55:16
 * --------------------------------- */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

use Utils\Helper;
?>
<?php

// 添加文章标题锚点
function createAnchor($obj) {
    global $catalog;
    global $catalog_count;
    $catalog = array();
    $catalog_count = 0;
    $obj = preg_replace_callback('/<h([1-4])(.*?)>(.*?)<\/h\1>/i', function ($obj) {
        global $catalog;
        global $catalog_count;
        $catalog_count++;
        $catalog[] = array('text' => trim(strip_tags($obj[3])), 'depth' => $obj[1], 'count' => $catalog_count);
        return '<h'.$obj[1].$obj[2].' id="cl-'.$catalog_count.'">'.$obj[3].'</h'.$obj[1].'>';
    }, $obj);
    return $obj;
}

// 灯箱
function replaceImgSrc($content) {
    $pattern = '/<img(.*?)src="(.*?)"(.*?)title="(.*?)"(.*?)>/i';
    $replacement = '<img data-original="$2" $3title="$4"$5 src="'.getLazyload(false).'" show-img class="lazy" />';
    return preg_replace($pattern, $replacement, $content);
}

// 提示短代码
function ContentHint($content) {
    $patterns = array(
        '/\[(success)\]\s*(.*?)\s*\[\s*\/\1\s*\]/s',
        '/\[(info)\]\s*(.*?)\s*\[\s*\/\1\s*\]/s',
        '/\[(warning)\]\s*(.*?)\s*\[\s*\/\1\s*\]/s',
        '/\[(danger)\]\s*(.*?)\s*\[\s*\/\1\s*\]/s',
    );

    $replacements = array(
        'success' => '<div class="hint-content hint-success p-2"><i class="iconfont p-2 icon-success"></i>%s</div>',
        'info' => '<div class="hint-content hint-info p-2"><i class="iconfont p-2 icon-info"></i>%s</div>',
        'warning' => '<div class="hint-content hint-warning p-2"><i class="iconfont p-2 icon-warning"></i>%s</div>',
        'danger' => '<div class="hint-content hint-danger p-2"><i class="iconfont p-2 icon-danger"></i>%s</div>',
    );

    $callback = function ($matches) use ($replacements) {
        $type = $matches[1]; // 获取匹配的类型 (success, info, warning, danger)
        // 去除开头的 <br> 标签
        $text = preg_replace('/^(<br\s*\/?>)+/i', '', $matches[2]);
        $text = Markdown::convert($text); // 转换 Markdown
        return sprintf($replacements[$type], $text); // 替换内容
    };

    foreach ($patterns as $pattern) {
        $content = preg_replace_callback($pattern, $callback, $content);
    }

    return $content;
}

// 视频短代码
function ContentVideo($content) {
    $pattern = '/\[player\s+url="([^"]*)"(?:\s+pic="([^"]*)")?\s+\/\]/';

    $content = preg_replace_callback($pattern, function ($matches) {
        $videoSrc = @strip_tags($matches[1]); // 获取 url 的值
        $posterSrc = @strip_tags($matches[2]); // 获取 pic 的值

        if (empty($posterSrc)) {
            $posterSrc = getAssets('assets/images/thumb/'.rand(1, 20).'.webp', false);
        }

        return '<video class="video-content" src="'.$videoSrc.'" poster="'.$posterSrc.'" controls></video>';
    }, $content);

    return $content;
}

// 链接短代码
function extractToLinks($content) {
    $pattern = '/\[tolink\s+title="([^"]*)"\s+url="([^"]*)"(?:\s+favicon="([^"]*)")?\s+\/\]/';

    $content = preg_replace_callback($pattern, function ($matches) {
        $tohtml = '<div class="to-links-content">';
        if (count($matches) >= 2) {
            $name = @strip_tags($matches[1]);
            $url = @strip_tags($matches[2]);
            $favicon = @strip_tags($matches[3]);

            $tohtml .= '<a data-links href="'.$url.'" class="d-flex align-items-center p-2 to-links-item short-code-card"
    target="_blank">';
            if ( ! empty($favicon)) {
                $tohtml .= '<img src="'.getLazyload(false).'" data-original="'.$favicon.'" class="lazy" title="'.$name.'" />';
            }
            $tohtml .= '<div class="to-links-text">';
            $tohtml .= '<span>'.$name.'</span>';
            $tohtml .= '<span class="to-links-url"><i class="iconfont icon-lianjie mr-1"></i>'.$url.'</span>';
            $tohtml .= '</div></a>';
        } else {
            $tohtml .= 'ToLinks短代码格式不正确，请检查名称、URL是否填写。';
        }

        $tohtml .= '</div>';

        return $tohtml;
    }, $content);

    return $content;
}

// 过滤a标签链接
function ContentLink($content) {

    if (empty(Helper::options()->isGoLink)) {
        return $content;
    }

// 正则表达式匹配所有的<a>标签及其属性
    $pattern = '/<a\s+([^>]*?)>/i';

    // 替换回调函数
    $content = preg_replace_callback($pattern, function ($matches) {
        $attributes = $matches[1];

        // 检查是否包含需要排除的属性
        if (preg_match('/\b(data-fancybox|data-cloud|data-test2)\b/', $attributes)) {
            // 包含需要排除的属性，不处理
            return '<a '.$attributes.'>';
        }

        // 获取href属性
        if (preg_match('/\bhref=["\']([^"\']*)["\']/i', $attributes, $hrefMatches)) {
            $originalHref = $hrefMatches[1];

            // 构造新的href
            $newHref = getGoLink($originalHref);

            // 替换href属性
            $newAttributes = preg_replace('/\bhref=["\'][^"\']*["\']/i', 'href="'.$newHref.'"', $attributes);

            // 检查是否已存在rel或target属性
            if (strpos($attributes, 'rel=') === false) {
                $newAttributes .= ' rel="nofollow"';
            }

            if (strpos($attributes, 'target=') === false) {
                $newAttributes .= ' target="_blank"';
            }

            return '<a '.$newAttributes.'>';
        }

        // 没有href属性，保持不变
        return '<a '.$attributes.'>';

    }, $content);

    return $content;
}

// 过滤网盘下载
function ContentCloud($content) {
    $pattern = '/\[cloud\s+type="([^"]*)"\s+title="([^"]*)"\s+url="([^"]*)"\s+password="([^"]*)"\s+\/\]/';
    $cloudList = array(
        'default' => '默认网盘',
        'baidu' => '百度网盘',
        'quark' => '夸克网盘',
        'aliyun' => '阿里云网盘',
        'lanzou' => '蓝奏云网盘',
        '360' => '360网盘',
        'weiyun' => '腾讯微云',
        'ctfile' => '城通网盘',
        'github' => 'GitHub仓库',
    );

    $content = preg_replace_callback($pattern, function ($matches) use ($cloudList) {
        // 提取属性值
        $type = @strip_tags($matches[1]); // 获取 type 的值
        $title = @strip_tags($matches[2]); // 获取 title 的值
        $url = @strip_tags($matches[3]); // 获取 url 的值
        $password = @strip_tags($matches[4]); // 获取 password 的值

        // 根据 type 获取网盘名称
        $name = isset($cloudList[$type]) ? $cloudList[$type] : '未知网盘';
        $password = $password ? $password : '无';
        $icon = '<div data-svg="'.$type.'cloud" data-viewbox="0 0 1024 1024" data-class="cloud-icon"></div>';

        $cloudHtml = '<div class="cloud-download-box p-2 short-code-card d-flex align-items-center">';
        $cloudHtml .= '<div class="cloud-download-icon">'.$icon.'</div>';
        $cloudHtml .= '<div class="cloud-download-info">';
        $cloudHtml .= '<div class="cloud-download-title"><a data-cloud href="'.$url.'" target="_blank"
                  title="'.$title.'">'.$title.'</a></div>';
        $cloudHtml .= '<div class="cloud-download-password">提取码: '.$password.'</div>';
        $cloudHtml .= '<div class="cloud-download-btn d-flex justify-content-between align-items-center">
                <span>来源：'.$name.'</span><a class="px-2 py-1" data-cloud href="'.$url.'" target="_blank"
                  title="'.$title.'"><i class="iconfont icon-xiazai"></i></a>
              </div>';
        $cloudHtml .= '</div>';
        $cloudHtml .= '</div>';

        return $cloudHtml;
    }, $content);

    return $content;
}

// 过滤pre代码标签
function ContentCode($content) {
    // 正则表达式匹配
    $pattern = '#<pre([^>]*)>(.*?)</pre>#si';

    // 替换的 HTML 结构
    $replacement = '
            <div class="pre-container mb-3">
              <div class="py-1 px-2 d-flex justify-content-between align-items-center pre-header">
                <div class="pre-icon"></div>
                <button class="pre-copy">复制</button>
              </div>
              <pre$1>$2</pre>
            </div>
            ';

    $content = preg_replace($pattern, $replacement, $content);

    return $content;
}

// 过滤fold折叠框
function ContentFold($content) {
    $pattern = '#\[fold\s+title="([^"]*)"\s+type="(open|close)"\s*\](.*?)\[/fold\]#si';

    $content = preg_replace_callback($pattern, function ($matches) {
        $title = $matches[1];
        $type = $matches[2];
        $contentText = Markdown::convert($matches[3]);

        $openAttr = ($type === 'open') ? 'open' : '';

        return '
            <details class="fold-container mb-3" '.$openAttr.'>
              <summary class="fold-header py-2 px-3">
                '.$title.'
              </summary>
              <div class="fold-content py-2 px-3">
                '.$contentText.'
              </div>
            </details>
            ';
    }, $content);

    return $content;
}

// 过滤多余的html标签
function ContentHtml($content) {
    // 使用一个正则表达式同时匹配并删除空段落和仅包含两个换行符的段落
    $content = preg_replace('#<p></p>|<br><br></p>#si', '', $content);

    return $content;
}

// 运行所以函数
function parseContens($content) {
    // 添加文章标题锚点
    $content = createAnchor($content);
    // 添加图片懒加载
    $content = replaceImgSrc($content);
    // 提示短代码
    $content = ContentHint($content);
    // 视频短代码
    $content = ContentVideo($content);
    // 链接短代码
    $content = extractToLinks($content);
    // 网盘下载短代码
    $content = ContentCloud($content);
    // 过滤a标签链接添加golink
    $content = ContentLink($content);
    // 表情包
    $content = formatEmoji($content);
    // 过滤pre代码标签
    $content = ContentCode($content);
    // 过滤fold折叠框
    $content = ContentFold($content);
    // 过滤多余的html标签
    $content = ContentHtml($content);

    return $content;
}

?>