<?php

/**
 * Harmony Hues主题 - 主题设置备份
 *
 * @author  星语社长
 * @link    https://biibii.cn
 * @update  2024-7-6 18:00:04
 */
if ( ! defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

define('THEME_URL', str_replace('//usr', '/usr', str_replace(Helper::options()->siteUrl, Helper::options()->rootUrl.'/', Helper::options()->themeUrl)));
$str1 = explode('/themes/', (THEME_URL.'/'));
$str2 = explode('/', $str1[1]);
define('THEME_NAME', $str2[0]);

$name = THEME_NAME;
$db = Typecho_Db::get();
$sjdq = $db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:'.$name));
$ysj = $sjdq['value'];

if (isset($_POST['type'])) {
    switch ($_POST['type']) {
        case '备份模板':
            if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:'.$name.'bf'))) {
                $update = $db->update('table.options')->rows(array('value' => $ysj))->where('name = ?', 'theme:'.$name.'bf');
                $db->query($update);
                echo '<script>let flag = confirm("备份更新成功!"); if (flag || !flag) window.location.href = "'.Helper::options()->adminUrl('options-theme.php').'";</script>';
            } else {
                if ($ysj) {
                    $insert = $db->insert('table.options')->rows(array('name' => 'theme:'.$name.'bf', 'user' => '0', 'value' => $ysj));
                    $db->query($insert);
                    echo '<script>let flag = confirm("备份成功!"); if (flag || !flag) window.location.href = "'.Helper::options()->adminUrl('options-theme.php').'";</script>';
                }
            }
            break;
        case '还原备份':
            if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:'.$name.'bf'))) {
                $sjdub = $db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:'.$name.'bf'));
                $bsj = $sjdub['value'];
                $update = $db->update('table.options')->rows(array('value' => $bsj))->where('name = ?', 'theme:'.$name);
                $db->query($update);
                echo '<script>let flag = confirm("恢复成功！"); if (flag || !flag) window.location.href = "'.Helper::options()->adminUrl('options-theme.php').'";</script>';
            } else {
                echo '<script>let flag = confirm("未备份过数据，无法恢复！"); if (flag || !flag) window.location.href = "'.Helper::options()->adminUrl('options-theme.php').'";</script>';
            }
            break;
        case '删除备份':
            if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:'.$name.'bf'))) {
                $delete = $db->delete('table.options')->where('name = ?', 'theme:'.$name.'bf');
                $db->query($delete);
                echo '<script>let flag = confirm("删除成功！"); if (flag || !flag) window.location.href = "'.Helper::options()->adminUrl('options-theme.php').'";</script>';
            } else {
                echo '<script>let flag = confirm("没有备份内容，无法删除！"); if (flag || !flag) window.location.href = "'.Helper::options()->adminUrl('options-theme.php').'";</script>';
            }
            break;
    }
}

echo '<form class="d-flex harmonyhues-backup mb-3" action="?'.$name.'bf" method="post">
        <input type="submit" name="type" value="备份模板" />
        <input type="submit" name="type" value="还原备份" />
        <input type="submit" name="type" value="删除备份" />
      </form>';