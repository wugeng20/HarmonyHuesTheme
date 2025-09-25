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
?>
<?php

//后台编辑器添加功能
function themeFields($layout)
{

    $showSidebar = new Typecho_Widget_Helper_Form_Element_Radio(
        'showSidebar',
        array(
            '0' => _t('单栏'),
            '1' => _t('双栏')
        ),
        '1',
        _t('侧边栏显示'),
        _t('选择当前文章页是否显示侧边栏，默认显示侧边栏，也推荐显示')
    );
    $layout->addItem($showSidebar);

    $keywords = new Typecho_Widget_Helper_Form_Element_Text(
        'keywords',
        NULL,
        NULL,
        'SEO关键词（非常重要！）',
        '介绍：用于设置当前页SEO关键词 <br />
注意：多个关键词使用英文逗号进行隔开 <br />
例如：Typecho,Typecho主题,Typecho模板 <br />
其他：如果不填写此项，则默认取文章标签'
    );
    $layout->addItem($keywords);

    $description = new Typecho_Widget_Helper_Form_Element_Textarea(
        'description',
        NULL,
        NULL,
        'SEO描述语（非常重要！）',
        '介绍：用于设置当前页SEO描述语 <br />
注意：SEO描述语不应当过长也不应当过少 <br />
其他：如果不填写此项，则默认截取文章片段'
    );
    $layout->addItem($description);

    $abstract = new Typecho_Widget_Helper_Form_Element_Textarea(
        'abstract',
        NULL,
        NULL,
        '自定义摘要（非必填）',
        '填写时：将会显示填写的摘要 <br>
不填写时：默认取文章里的内容'
    );
    $layout->addItem($abstract);

    $thumb = new Typecho_Widget_Helper_Form_Element_Textarea(
        'thumb',
        NULL,
        NULL,
        '自定义缩略图（非必填）',
        '填写时：将会显示填写的文章缩略图 <br>
不填写时：<br>
1、若文章有图片则取文章内图片 <br>
2、若文章无图片，并且外观设置里未填写·自定义缩略图·选项，则取模板自带图片 <br>
3、若文章无图片，并且外观设置里填写了·自定义缩略图·选项，则取自定义缩略图图片'
    );
    $layout->addItem($thumb);
}

/* 编辑器添加按钮及功能 */
Typecho_Plugin::factory('admin/write-post.php')->bottom = array('Editor', 'edit');
Typecho_Plugin::factory('admin/write-page.php')->bottom = array('Editor', 'edit');
class Editor
{
    public static function edit()
    {
        echo '
<link rel="stylesheet" href="' . getAssets('assets/typecho/editor/css/editor.min.css?v=' . getVersion(), false) . '"
type="text/css" media="all">';
        echo '<script src="' . getAssets('assets/typecho/editor/js/editor.min.js?v=' . getVersion(), false) . '"></script>';
    }
}
?>