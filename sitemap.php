<?php

/**
 * sitemap网站地图
 * 
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-12-31 00:08:21
 */
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

// 获取数据库实例和站点配置
$db = Typecho_Db::get();
$options = Typecho_Widget::widget('Widget_Options');

// 输出单个url节点
function print_url($loc, $lastmod = null, $changefreq = 'daily', $priority = '0.5')
{
    echo "\t<url>\n";
    echo "\t\t<loc>" . htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
    if ($lastmod) {
        echo "\t\t<lastmod>" . htmlspecialchars($lastmod, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</lastmod>\n";
    }
    echo "\t\t<changefreq>" . $changefreq . "</changefreq>\n";
    echo "\t\t<priority>" . $priority . "</priority>\n";
    echo "\t</url>\n";
}

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

// 首页
print_url($options->siteUrl, date('Y-m-d', $options->gmtTime), 'daily', '1.0');

// 文章
foreach ($articles as $article) {
    $permalink = Typecho_Router::url('post', $article);
    $permalink = Typecho_Common::url($permalink, $options->index);
    print_url($permalink, date('Y-m-d', $article['modified']), 'always', '0.8');
}

// 页面
foreach ($pages as $page) {
    $permalink = Typecho_Router::url('page', $page);
    $permalink = Typecho_Common::url($permalink, $options->index);
    print_url($permalink, date('Y-m-d', $page['modified']), 'always', '0.6');
}

// 分类
foreach ($categories as $category) {
    $latestArticle = $db->fetchRow($db->select()->from('table.contents')
        ->join('table.relationships', 'table.contents.cid = table.relationships.cid')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.relationships.mid = ?', $category['mid'])
        ->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->limit(1));
    if ($latestArticle) {
        $permalink = $options->siteUrl . 'category/' . rawurlencode($category['slug']);
        print_url($permalink, date('Y-m-d', $latestArticle['modified']), 'daily', '0.5');
    }
}

// 标签
foreach ($tags as $tag) {
    $latestArticle = $db->fetchRow($db->select()->from('table.contents')
        ->join('table.relationships', 'table.contents.cid = table.relationships.cid')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.relationships.mid = ?', $tag['mid'])
        ->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->limit(1));
    if ($latestArticle) {
        $permalink = $options->siteUrl . 'tag/' . rawurlencode($tag['slug']);
        print_url($permalink, date('Y-m-d', $latestArticle['modified']), 'daily', '0.5');
    }
}

echo '</urlset>';
