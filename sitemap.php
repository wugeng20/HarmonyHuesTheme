<?php
/**
 * sitemap网站地图
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-12-31 00:08:21
 */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

// 获取数据库实例和站点配置
$db = Typecho_Db::get();
$options = Typecho_Widget::widget('Widget_Options');

// 获取所有已发布的文章
$articles = $db->fetchAll($db->select()->from('table.contents')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.created < ?', $options->gmtTime)
        ->where('table.contents.type = ?', 'post')
        ->order('table.contents.created', Typecho_Db::SORT_DESC));

// 获取所有已发布的页面
$pages = $db->fetchAll($db->select()->from('table.contents')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.created < ?', $options->gmtTime)
        ->where('table.contents.type = ?', 'page')
        ->order('table.contents.created', Typecho_Db::SORT_DESC));

// 获取所有分类
$categories = $db->fetchAll($db->select()->from('table.metas')
        ->where('table.metas.type = ?', 'category')
        ->order('table.metas.mid', Typecho_Db::SORT_DESC));

// 获取所有标签
$tags = $db->fetchAll($db->select()->from('table.metas')
        ->where('table.metas.type = ?', 'tag')
        ->order('table.metas.mid', Typecho_Db::SORT_DESC));

// 输出 XML 头部
header('Content-Type: application/xml');
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

// 添加首页
echo "\t<url>\n";
echo "\t\t<loc>".$options->siteUrl."</loc>\n";
echo "\t\t<changefreq>daily</changefreq>\n";
echo "\t\t<priority>1.0</priority>\n";
echo "\t</url>\n";

// 添加文章
foreach ($articles as $article) {
    $permalink = Typecho_Router::url('post', $article);
    $permalink = Typecho_Common::url($permalink, $options->index);
    echo "\t<url>\n";
    echo "\t\t<loc>".$permalink."</loc>\n";
    echo "\t\t<lastmod>".date('Y-m-d', $article['modified'])."</lastmod>\n";
    echo "\t\t<changefreq>always</changefreq>\n";
    echo "\t\t<priority>0.8</priority>\n";
    echo "\t</url>\n";
}

// 添加页面
foreach ($pages as $page) {
    $permalink = Typecho_Router::url('page', $page);
    $permalink = Typecho_Common::url($permalink, $options->index);
    echo "\t<url>\n";
    echo "\t\t<loc>".$permalink."</loc>\n";
    echo "\t\t<lastmod>".date('Y-m-d', $page['modified'])."</lastmod>\n";
    echo "\t\t<changefreq>always</changefreq>\n";
    echo "\t\t<priority>0.6</priority>\n";
    echo "\t</url>\n";
}

// 添加分类
foreach ($categories as $category) {
    $latestArticle = $db->fetchRow($db->select()->from('table.contents')
            ->join('table.relationships', 'table.contents.cid = table.relationships.cid')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.relationships.mid = ?', $category['mid'])
            ->order('table.contents.created', Typecho_Db::SORT_DESC)
            ->limit(1));
    if ($latestArticle) {
        $permalink = $options->siteUrl.'category/'.rawurlencode($category['slug']);
        echo "\t<url>\n";
        echo "\t\t<loc>".$permalink."</loc>\n";
        echo "\t\t<lastmod>".date('Y-m-d', $latestArticle['modified'])."</lastmod>\n";
        echo "\t\t<changefreq>daily</changefreq>\n";
        echo "\t\t<priority>0.5</priority>\n";
        echo "\t</url>\n";
    }
}

// 添加标签
foreach ($tags as $tag) {
    $latestArticle = $db->fetchRow($db->select()->from('table.contents')
            ->join('table.relationships', 'table.contents.cid = table.relationships.cid')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.relationships.mid = ?', $tag['mid'])
            ->order('table.contents.created', Typecho_Db::SORT_DESC)
            ->limit(1));
    if ($latestArticle) {
        $permalink = $options->siteUrl.'tag/'.rawurlencode($tag['slug']);
        echo "\t<url>\n";
        echo "\t\t<loc>".$permalink."</loc>\n";
        echo "\t\t<lastmod>".date('Y-m-d', $latestArticle['modified'])."</lastmod>\n";
        echo "\t\t<changefreq>daily</changefreq>\n";
        echo "\t\t<priority>0.5</priority>\n";
        echo "\t</url>\n";
    }
}

echo '</urlset>';
?>