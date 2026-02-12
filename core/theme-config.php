<?php

/**
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link    https://biibii.cn
 * @update  2024-7-6 18:00:04
 */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
?>
<?php
// 主题外观设置
function themeConfig($form) {
    // 导入备份
    require_once 'theme-backup.php';
    ?>
<link rel="stylesheet" href="<?php getAssets('assets/lib/bootstrap4/bootstrap.min.css')?>" type="text/css" media="all">
<link rel="stylesheet" href="<?php getAssets('assets/css/style.min.css')?>" type="text/css" media="all">
<link rel="stylesheet" href="<?php getAssets('assets/typecho/config/css/config.min.css')?>">
<div class="harmonyhues-config card mt-2 mb-4 p-2">
  <?php

    //---------------------------- 基础设置 ----------------------------//
    // 网站favicon图标
    $favicon = new Typecho_Widget_Helper_Form_Element_Text(
        'favicon',
        NULL,
        '/usr/themes/HarmonyHues/assets/images/favicon.webp',
        'favicon地址（必填）',
        '介绍：一般为http://www.yourblog.com/image.png,支持 https:// 或 //,留空则不设置favicon'
    );
    $form->addInput($favicon);
    $favicon->setAttribute('class', 'setting-content my-3');

    // 网站logo
    $logoUrl = new Typecho_Widget_Helper_Form_Element_Text(
        'logoUrl',
        NULL,
        '/usr/themes/HarmonyHues/assets/images/logo.webp',
        '站点LOGO地址（必填）',
        '介绍：在这里填入一个图片URL地址, 以在网站标题前加上一个LOGO'
    );
    $form->addInput($logoUrl);
    $logoUrl->setAttribute('class', 'setting-content my-3');

    // 网站个人简介
    $blogmeabout = new Typecho_Widget_Helper_Form_Element_Text(
        'blogmeabout',
        NULL,
        '欢迎来到我的typecho博客。',
        '个人简介（非必填）',
        '介绍：在这里填入你的个人简介，例如：欢迎来到我的typecho博客。'
    );
    $form->addInput($blogmeabout);
    $blogmeabout->setAttribute('class', 'setting-content my-3');

    // ---------------------------- 主题设置 ----------------------------//
    // 侧边栏博主信息社交
    $socialInfo = new Typecho_Widget_Helper_Form_Element_Textarea(
        'socialInfo',
        NULL,
        NULL,
        '个人社交（非必填）',
        '介绍：在这里填入你的JSON格式社交图标（www.iconfont.cn）和URL，JSON格式模板：{"name":"bilibili","icon":"icon-logo-github","link":"https://bilibili.com/xxxx"}'
    );
    $form->addInput($socialInfo);
    $socialInfo->setAttribute('class', 'setting-content my-3');

    // 微信赞赏二维码
    $wechatQrCode = new Typecho_Widget_Helper_Form_Element_Text(
        'wechatQrCode',
        NULL,
        '/usr/themes/HarmonyHues/assets/images/wechatQr.webp',
        '微信二维码（必填）',
        '介绍：在这里填入你的微信收款二维码URL地址'
    );
    $form->addInput($wechatQrCode);
    $wechatQrCode->setAttribute('class', 'setting-content my-3');

    // 支付宝赞赏二维码
    $alipayQrCode = new Typecho_Widget_Helper_Form_Element_Text(
        'alipayQrCode',
        NULL,
        '/usr/themes/HarmonyHues/assets/images/alipayQr.webp',
        '支付宝二维码（必填）',
        '介绍：在这里填入你的支付宝收款二维码URL地址'
    );
    $form->addInput($alipayQrCode);
    $alipayQrCode->setAttribute('class', 'setting-content my-3');

    // 导航栏样式
    $navStyle = new Typecho_Widget_Helper_Form_Element_Select(
        'navStyle',
        array(
            'default' => '默认导航栏',
            'mini' => 'MINI导航栏',
        ),
        'default',
        '导航栏样式（非必填）',
        '介绍：默认导航栏宽度为100%，MINI导航栏为container居中。'
    );
    $form->addInput($navStyle->multiMode());
    $navStyle->setAttribute('class', 'setting-content my-3');

    // 自定义缩略图
    $customThumbnail = new Typecho_Widget_Helper_Form_Element_Textarea(
        'customThumbnail',
        NULL,
        NULL,
        '自定义缩略图（非必填）',
        '介绍：在这里填入一个图片URL地址，图片地址一行一个，用于修改主题默认缩略图，不填则使用默认缩略图'
    );
    $form->addInput($customThumbnail);
    $customThumbnail->setAttribute('class', 'setting-content my-3');

    // 自定义懒加载IMG
    $lazyload = new Typecho_Widget_Helper_Form_Element_Text(
        'lazyload',
        NULL,
        NULL,
        '自定义懒加载图片（非必填）',
        '介绍：在这里填入一个图片URL地址, 以在图片加载失败时显示'
    );
    $form->addInput($lazyload);
    $lazyload->setAttribute('class', 'setting-content my-3');

    // SVG签名
    $svgName = new Typecho_Widget_Helper_Form_Element_Textarea(
        'svgName',
        NULL,
        NULL,
        'SVG名字签名（非必填）',
        '介绍：在这里填入一个SVG代码,SVG生成地址：https://danmarshall.github.io/google-font-to-svg-path/,注意：SVG的width和height属性需要自行调整'
    );
    $form->addInput($svgName);
    $svgName->setAttribute('class', 'setting-content my-3');

    // 自定义导航栏
    $navbarInfo = new Typecho_Widget_Helper_Form_Element_Textarea(
        'navbarInfo',
        NULL,
        '{"name":"自定义","link":"","target":"_self","sub":[{"name":"子菜单名称","link":"https://www.example.com/xxxx.html","target":"_self"}]}',
        '导航栏菜单配置（非必填）',
        '介绍：支持自定义菜单,不支持三级目录，优先分类和独立页面里面找，没有才匹配link<br/>
    在这里填入JSON格式模板：{name":"菜单名称","link":"https://www.example.com/xxxx.html","target":"_self","sub":[{"name":"子菜单名称","link":"https://www.example.com/xxxx.html","target":"_self"}]}'
    );
    $form->addInput($navbarInfo);
    $navbarInfo->setAttribute('class', 'setting-content my-3');

    // 文章置顶
    $sticky = new Typecho_Widget_Helper_Form_Element_Text(
        'sticky',
        NULL,
        NULL,
        '文章置顶（非必填）',
        '介绍：置顶的文章cid，按照排序输入, 请以半角逗号或空格分隔'
    );
    $form->addInput($sticky);
    $sticky->setAttribute('class', 'setting-content my-3');

    // 是否开启goLink外链
    $isGoLink = new Typecho_Widget_Helper_Form_Element_Select(
        'isGoLink',
        array(
            '0' => '关闭GoLink外链',
            '1' => '开启GoLink外链',
        ),
        '0',
        'GoLink外链页面（非必填）',
        '介绍：开启后，文章页面的外链会自动跳转到GoLink页面'
    );
    $form->addInput($isGoLink->multiMode());
    $isGoLink->setAttribute('class', 'setting-content my-3');

    // 头像源
    $Gravatars = new Typecho_Widget_Helper_Form_Element_Select(
        'Gravatars',
        array(
            'gravatar.helingqi.com/wavatar' => '禾令奇（默认）',
            'www.gravatar.com/avatar' => 'gravatar的www源',
            'cn.gravatar.com/avatar' => 'gravatar的cn源',
            'secure.gravatar.com/avatar' => 'gravatar的secure源',
            'sdn.geekzu.org/avatar' => '极客族',
            'cdn.v2ex.com/gravatar' => 'v2ex源',
            'dn-qiniu-avatar.qbox.me/avatar' => '七牛源[不建议]',
            'gravatar.loli.net/avatar' => 'loli.net源',
        ),
        'gravatar.helingqi.com/wavatar',
        '选择头像源（非必填）',
        '介绍：不同的源响应速度不同，头像也不同'
    );
    $Gravatars->setAttribute('class', 'setting-content my-3');
    $form->addInput($Gravatars->multiMode());

    // CSP策略
    $isCSP = new Typecho_Widget_Helper_Form_Element_Select(
        'isCSP',
        array(
            '0' => '关闭CSP',
            '1' => '开启CSP',
        ),
        '0',
        'CSP策略（非必填）',
        '介绍：CSP策略可以防止XSS攻击，但是会降低页面加载速度，如果页面加载速度变慢，请关闭CSP'
    );
    $form->addInput($isCSP->multiMode());
    $isCSP->setAttribute('class', 'setting-content my-3');

    // CSP内容
    $contentCSP = new Typecho_Widget_Helper_Form_Element_Textarea(
        'contentCSP',
        NULL,
        NULL,
        'CSP内容（非必填）',
        '介绍：在这里填入CSP内容，例如：script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' https://cdn.jsdelivr.net/; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net/; img-src \'self\' data:; font-src \'self\' https://cdn.jsdelivr.net/; connect-src \'self\' https://cdn.jsdelivr.net/;'
    );
    $form->addInput($contentCSP);
    $contentCSP->setAttribute('class', 'setting-content my-3');

    // 侧边栏设置
    $sidebarBlock = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'sidebarBlock',
        array(
            'ShowAboutMe' => '博主信息',
            'ShowSidebarYiyan' => '一言',
            'ShowSidebarComments' => '最新评论',
            'ShowHotPosts' => '热门文章',
            'ShowBlogSignage' => '博客路牌',
            'ShowDevilEyes' => '恶魔之眼',
        ),
        array('ShowAboutMe', 'ShowSidebarYiyan', 'ShowHotPosts'),
        '侧边栏显示（非必填）',
        '介绍：侧边栏模块，默认显示博主信息和最新评论'
    );
    $form->addInput($sidebarBlock->multiMode());
    $sidebarBlock->setAttribute('class', 'setting-content my-3');

    // 侧边栏-博客路牌
    $blogSignageText = new Typecho_Widget_Helper_Form_Element_Text(
        'blogSignageText',
        NULL,
        '我在贵州很想你',
        '侧边栏-博客路牌（非必填）',
        '介绍：在这里填入你的博客路牌内容,例如：我在贵州很想你'
    );
    $form->addInput($blogSignageText);
    $blogSignageText->setAttribute('class', 'setting-content my-3');

    // 首页内容显示
    $indexBlock = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'indexBlock',
        array(
            'ShowTravelling' => '顶部-开往友链接力',
            'ShowSwiper' => '首页-轮播幻灯片',
            'ShowHello' => '底部-Hello信息',
            'ShowTimeJourney' => '底部-时间之旅',
            'ShowLinks' => '友情链接（未开发）',
        ),
        array('ShowHello'),
        '首页内容显示（非必填）',
        '介绍：首页模块，默认显示Hello信息'
    );
    $form->addInput($indexBlock->multiMode());
    $indexBlock->setAttribute('class', 'setting-content my-3');

    // 首页顶部-轮播幻灯片
    $swiperText = new Typecho_Widget_Helper_Form_Element_Textarea(
        'swiperText',
        NULL,
        'HarmonyHues主题|欢迎使用HarmonyHues主题|2月29日 今日标题|https://www.biibii.cn/usr/themes/HarmonyHues/assets/images/themeImg.webp|https://www.biibii.cn/harmonyhues.html',
        '首页顶部-轮播幻灯片内容（非必填）',
        '介绍：在这里填入你的轮播幻灯片内容，格式：标题|描述|标签|图片URL或者MP4格式|链接URL（用|号隔开，内容没有的用NULL，内容一行一个，建议最多5个），例如：HarmonyHues主题|欢迎使用HarmonyHues主题|2月28日 今日标题|https://www.biibii.cn/usr/themes/HarmonyHues/assets/images/themeImg.webp|https://www.biibii.cn/harmonyhues.html'
    );
    $form->addInput($swiperText);
    $swiperText->setAttribute('class', 'setting-content my-3');

    // 首页底部-Hello文字
    $helloText = new Typecho_Widget_Helper_Form_Element_Text(
        'helloText',
        NULL,
        'Welcome to BIIBII.CN',
        '首页底部-Hello内容（非必填）',
        '介绍：在这里填入你的Hello内容,例如：Welcome to BIIBII.CN'
    );
    $form->addInput($helloText);
    $helloText->setAttribute('class', 'setting-content my-3');

    // 首页底部-时间之旅内容
    $timeJourneyText = new Typecho_Widget_Helper_Form_Element_Text(
        'timeJourneyText',
        NULL,
        '2025-03-26|天|星际旅行|本站服务器燃料剩余{remainingPercentage}%',
        '首页底部-时间之旅内容（非必填）',
        '介绍：在这里填入你的时间之旅内容，格式：时间|单位|标题|{剩余百分比：remainingPercentage|已过去百分比：progressPercentage},例如：2025-03-26|天|星际旅行|本站服务器燃料剩余{remainingPercentage}%'
    );
    $form->addInput($timeJourneyText);
    $timeJourneyText->setAttribute('class', 'setting-content my-3');

    // 网站icon
    $iconfont = new Typecho_Widget_Helper_Form_Element_Text(
        'iconfont',
        NULL,
        NULL,
        'iconfont图标（非必填）',
        '介绍：在这里填入你自定义的iconfont图标外链，前往iconfont图标：https://www.iconfont.cn，<br/>
    例如：//at.alicdn.com/t/c/font_4612620_nzptu6bs4cb.css'
    );
    $form->addInput($iconfont);
    $iconfont->setAttribute('class', 'setting-content my-3');

    // 评论敏感词
    $SensitiveWords = new Typecho_Widget_Helper_Form_Element_Textarea(
        'SensitiveWords',
        NULL,
        '你妈死了|傻逼|操你妈|我是你爹',
        '评论敏感词（非必填）',
        '介绍：用于设置评论敏感词汇，如果用户评论包含这些词汇，则将会把评论置为审核状态 <br />
             例如：你妈死了|你妈炸了|我是你爹（使用|分隔开）'
    );
    $SensitiveWords->setAttribute('class', 'setting-content my-3');
    $form->addInput($SensitiveWords);

    // 评论限制开关
    $LimitOneChinese = new Typecho_Widget_Helper_Form_Element_Select(
        'LimitOneChinese',
        array('off' => '关闭（默认）', 'on' => '开启'),
        'off',
        '是否开启评论至少包含一个中文（非必填）',
        '介绍：开启后如果评论内容未包含一个中文，则将会把评论置为审核状态 <br />
             其他：用于屏蔽国外机器人刷的全英文垃圾广告信息'
    );
    $LimitOneChinese->setAttribute('class', 'setting-content my-3');
    $form->addInput($LimitOneChinese->multiMode());

    // 评论限制
    $TextLimit = new Typecho_Widget_Helper_Form_Element_Text(
        'TextLimit',
        NULL,
        NULL,
        '限制用户评论最大字符（非必填）',
        '介绍：如果用户评论的内容超出字符限制，则将会把评论置为审核状态 <br />
             其他：请输入数字格式，不填写则不限制'
    );
    $form->addInput($TextLimit->multiMode());
    $TextLimit->setAttribute('class', 'setting-content my-3');

    // 评论邮件通知
    $commentMail = new Typecho_Widget_Helper_Form_Element_Select(
        'commentMail',
        array('off' => '关闭（默认）', 'on' => '开启'),
        'off',
        '是否开启评论邮件通知（非必填）',
        '介绍：开启后评论内容将会进行邮箱通知 <br />
         注意：此项需要您完整无错的填写下方的邮箱设置！！ <br />
         其他：下方例子以QQ邮箱为例，推荐使用QQ邮箱'
    );
    $commentMail->setAttribute('class', 'setting-content my-3');
    $form->addInput($commentMail->multiMode());

    $commentMailHost = new Typecho_Widget_Helper_Form_Element_Text(
        'commentMailHost',
        NULL,
        NULL,
        '邮箱服务器地址',
        '例如：smtp.qq.com'
    );
    $commentMailHost->setAttribute('class', 'setting-content my-3');
    $form->addInput($commentMailHost->multiMode());

    $commentSMTPSecure = new Typecho_Widget_Helper_Form_Element_Select(
        'commentSMTPSecure',
        array('ssl' => 'ssl（默认）', 'tsl' => 'tsl'),
        'ssl',
        '加密方式',
        '介绍：用于选择登录鉴权加密方式'
    );
    $commentSMTPSecure->setAttribute('class', 'setting-content my-3');
    $form->addInput($commentSMTPSecure->multiMode());

    $commentMailPort = new Typecho_Widget_Helper_Form_Element_Text(
        'commentMailPort',
        NULL,
        NULL,
        '邮箱服务器端口号',
        '例如：465'
    );
    $commentMailPort->setAttribute('class', 'setting-content my-3');
    $form->addInput($commentMailPort->multiMode());

    $commentMailFromName = new Typecho_Widget_Helper_Form_Element_Text(
        'commentMailFromName',
        NULL,
        NULL,
        '发件人昵称',
        '例如：蔡徐坤博客评论回复通知'
    );
    $commentMailFromName->setAttribute('class', 'setting-content my-3');
    $form->addInput($commentMailFromName->multiMode());

    $commentMailAccount = new Typecho_Widget_Helper_Form_Element_Text(
        'commentMailAccount',
        NULL,
        NULL,
        '发件人邮箱',
        '例如：123456@qq.com'
    );
    $commentMailAccount->setAttribute('class', 'setting-content my-3');
    $form->addInput($commentMailAccount->multiMode());

    $commentMailPassword = new Typecho_Widget_Helper_Form_Element_Text(
        'commentMailPassword',
        NULL,
        NULL,
        '邮箱授权码',
        '介绍：这里填写的是邮箱生成的授权码 <br>
         获取方式（以QQ邮箱为例）：<br>
         QQ邮箱 > 设置 > 账户 > IMAP/SMTP服务 > 开启 <br>
         其他：这个可以百度一下开启教程，有图文教程'
    );
    $commentMailPassword->setAttribute('class', 'setting-content my-3');
    $form->addInput($commentMailPassword->multiMode());

    // 底部设置
    $icp = new Typecho_Widget_Helper_Form_Element_Text(
        'icp',
        NULL,
        NULL,
        '备案HTML内容（非必填）',
        '介绍：在这里填入你的网站备案HTML内容， 注意：底部footer内容会覆盖此内容，只适合默认的底部内容，示例：  &Iota; <a href="https://beian.miit.gov.cn/" target="_blank">ICP备xxxx号-1</a>'
    );
    $form->addInput($icp);
    $icp->setAttribute('class', 'setting-content my-3');

    // 网站建站日期
    $sitedate = new Typecho_Widget_Helper_Form_Element_Text(
        'sitedate',
        NULL,
        '2024-08-06',
        '网站建站日期（必填）',
        '介绍：在这里填入你的网站建站日期， 例如：2024-05-20'
    );
    $form->addInput($sitedate);
    $sitedate->setAttribute('class', 'setting-content my-3');

    // 底部footer内容
    $footerContent = new Typecho_Widget_Helper_Form_Element_Textarea(
        'footerContent',
        NULL,
        NULL,
        '底部footer内容（非必填）',
        '介绍：在这里填入底部footer HTML内容，注意：此处填写会覆盖主题默认底部footer内容'
    );
    $form->addInput($footerContent);
    $footerContent->setAttribute('class', 'setting-content my-3');

    // 网站统计代码（非必填）
    $zztj = new Typecho_Widget_Helper_Form_Element_Textarea(
        'zztj',
        NULL,
        NULL,
        '网站统计代码（非必填）',
        '介绍：在这里填入你的网站统计代码，这个可以到百度统计或者cnzz上申请。'
    );
    $form->addInput($zztj);
    $zztj->setAttribute('class', 'setting-content my-3');

    // 自定义CSS
    $customStyle = new Typecho_Widget_Helper_Form_Element_Textarea(
        'customStyle',
        null,
        null,
        '自定义CSS（非必填）',
        '介绍：不需要添加 &lt;style&gt; 标签'
    );
    $form->addInput($customStyle);
    $customStyle->setAttribute('class', 'setting-content my-3');

    // 自定义JS
    $customScript = new Typecho_Widget_Helper_Form_Element_Textarea(
        'customScript',
        null,
        null,
        '自定义JS（非必填）',
        '介绍：不需要添加 &lt;script&gt; 标签'
    );
    $form->addInput($customScript);
    $customScript->setAttribute('class', 'setting-content my-3');

    // 自定义增加head里内容（非必填）
    $customHeadEnd = new Typecho_Widget_Helper_Form_Element_Textarea(
        'customHeadEnd',
        NULL,
        NULL,
        '自定义增加&lt;head&gt;&lt;/head&gt;里内容（非必填）',
        '介绍：此处用于在&lt;head&gt;&lt;/head&gt;标签里增加自定义内容，可以填写引入第三方css、js等等'
    );
    $customHeadEnd->setAttribute('class', 'setting-content my-3');
    $form->addInput($customHeadEnd);

    // 自定义增加body里内容（非必填）
    $customBodyEnd = new Typecho_Widget_Helper_Form_Element_Textarea(
        'customBodyEnd',
        NULL,
        NULL,
        '自定义&lt;body&gt;&lt;/body&gt;末尾位置内容（非必填）',
        '介绍：此处用于填写在&lt;body&gt;&lt;/body&gt;标签末尾位置的内容，例如：可以填写引入第三方js脚本等等'
    );
    $customBodyEnd->setAttribute('class', 'setting-content my-3');
    $form->addInput($customBodyEnd);
}

?>