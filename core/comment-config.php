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
?>
<?php

/* 判断是否是手机 */
function isMobile() {
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }

    if (isset($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    }
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        if (preg_match('/('.implode('|', $clientkeywords).')/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }

    }
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/** 获取浏览器信息 */
function getBrowser($agent) {
    $outputer = false;
    if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
        $outputer = 'IE';
    } elseif (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('Firefox/', $regs[0]);
        $FireFox_vern = explode('.', $str1[1]);
        $outputer = '火狐'.$FireFox_vern[0];
    } elseif (preg_match('/Maxthon([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('Maxthon/', $agent);
        $Maxthon_vern = explode('.', $str1[1]);
        $outputer = '傲游'.$Maxthon_vern[0];
    } elseif (preg_match('#SE 2([a-zA-Z0-9.]+)#i', $agent, $regs)) {
        $outputer = '搜狗';
    } elseif (preg_match('#360([a-zA-Z0-9.]+)#i', $agent, $regs)) {
        $outputer = '360';
    } elseif (preg_match('/Edge([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('Edge/', $regs[0]);
        $Edge_vern = explode('.', $str1[1]);
        $outputer = 'Edge'.$Edge_vern[0];
    } elseif (preg_match('/EdgiOS([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('EdgiOS/', $regs[0]);
        $outputer = 'Edge';
    } elseif (preg_match('/UC/i', $agent)) {
        $str1 = explode('rowser/', $agent);
        $UCBrowser_vern = explode('.', $str1[1]);
        $outputer = 'UC'.$UCBrowser_vern[0];
    } elseif (preg_match('/OPR/i', $agent)) {
        $str1 = explode('OPR/', $agent);
        $opr_vern = explode('.', $str1[1]);
        $outputer = '欧朋 '.$opr_vern[0];
    } elseif (preg_match('/MicroMesseng/i', $agent, $regs)) {
        $outputer = '微信';
    } elseif (preg_match('/WeiBo/i', $agent, $regs)) {
        $outputer = '微博';
    } elseif (preg_match('/QQ/i', $agent, $regs) || preg_match('/QQBrowser\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('rowser/', $agent);
        $QQ_vern = explode('.', $str1[1]);
        $outputer = 'QQ '.$QQ_vern[0];
    } elseif (preg_match('/MQBHD/i', $agent, $regs)) {
        $str1 = explode('MQBHD/', $agent);
        $QQ_vern = explode('.', $str1[1]);
        $outputer = 'QQ '.$QQ_vern[0];
    } elseif (preg_match('/BIDU/i', $agent, $regs)) {
        $outputer = '百度';
    } elseif (preg_match('/LBBROWSER/i', $agent, $regs)) {
        $outputer = '猎豹';
    } elseif (preg_match('/TheWorld/i', $agent, $regs)) {
        $outputer = '世界之窗';
    } elseif (preg_match('/XiaoMi/i', $agent, $regs)) {
        $outputer = '小米';
    } elseif (preg_match('/UBrowser/i', $agent, $regs)) {
        $str1 = explode('rowser/', $agent);
        $UCBrowser_vern = explode('.', $str1[1]);
        $outputer = 'UC'.$UCBrowser_vern[0];
    } elseif (preg_match('/mailapp/i', $agent, $regs)) {
        $outputer = 'email';
    } elseif (preg_match('/2345Explorer/i', $agent, $regs)) {
        $outputer = '2345';
    } elseif (preg_match('/Sleipnir/i', $agent, $regs)) {
        $outputer = '神马';
    } elseif (preg_match('/YaBrowser/i', $agent, $regs)) {
        $outputer = 'Yandex';
    } elseif (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
        $outputer = 'Opera';
    } elseif (preg_match('/MZBrowser/i', $agent, $regs)) {
        $outputer = '魅族';
    } elseif (preg_match('/VivoBrowser/i', $agent, $regs)) {
        $outputer = 'vivo';
    } elseif (preg_match('/Quark/i', $agent, $regs)) {
        $outputer = '夸克';
    } elseif (preg_match('/mixia/i', $agent, $regs)) {
        $outputer = '米侠';
    } elseif (preg_match('/fusion/i', $agent, $regs)) {
        $outputer = '客户端';
    } elseif (preg_match('/CoolMarket/i', $agent, $regs)) {
        $outputer = '基安';
    } elseif (preg_match('/Thunder/i', $agent, $regs)) {
        $outputer = '迅雷';
    } elseif (preg_match('/Chrome([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('Chrome/', $agent);
        $chrome_vern = explode('.', $str1[1]);
        $outputer = 'Chrome'.$chrome_vern[0];
    } elseif (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('Version/', $agent);
        $safari_vern = @explode('.', $str1[1]);
        $outputer = 'Safari'.$safari_vern[0];
    } else {
        return false;
    }
    return $outputer;
}

/** 获取操作系统信息 */
function getOs($agent) {
    $os = false;
    if (preg_match('/win/i', $agent)) {
        if (preg_match('/nt 6.0/i', $agent)) {
            $os = 'WIN Vista';
        } elseif (preg_match('/nt 6.1/i', $agent)) {
            $os = 'WIN7';
        } elseif (preg_match('/nt 6.2/i', $agent)) {
            $os = 'WIN8';
        } elseif (preg_match('/nt 6.3/i', $agent)) {
            $os = 'WIN8.1';
        } elseif (preg_match('/nt 5.1/i', $agent)) {
            $os = 'WINXP';
        } elseif (preg_match('/nt 10.0/i', $agent)) {
            $os = 'WIN10';
        } else {
            $os = 'WIN';
        }
    } elseif (preg_match('/android/i', $agent)) {
        if (preg_match('/android 9/i', $agent)) {
            $os = '安卓P';
        } elseif (preg_match('/android 8/i', $agent)) {
            $os = '安卓O';
        } elseif (preg_match('/android 7/i', $agent)) {
            $os = '安卓N';
        } elseif (preg_match('/android 6/i', $agent)) {
            $os = '安卓M';
        } elseif (preg_match('/android 5/i', $agent)) {
            $os = '安卓L';
        } else {
            $os = '安卓';
        }
    } elseif (preg_match('/ubuntu/i', $agent)) {
        $os = 'Linux';
    } elseif (preg_match('/linux/i', $agent)) {
        $os = 'Linux';
    } elseif (preg_match('/iPhone/i', $agent)) {
        $os = 'iPhone';
    } elseif (preg_match('/iPad/i', $agent)) {
        $os = 'iPad';
    } elseif (preg_match('/mac/i', $agent)) {
        $os = 'OSX';
    } elseif (preg_match('/cros/i', $agent)) {
        $os = 'chrome os';
    } else {
        return false;
    }
    return $os;
}

/* 加强评论拦截功能 */
Typecho_Plugin::factory('Widget_Feedback')->comment = array('Intercept', 'message');
class Intercept {
    public static function message($comment) {
        // 判断用户输入是否大于字符
        if (Helper::options()->TextLimit && strlen($comment['text']) > Helper::options()->TextLimit) {
            $comment['status'] = 'waiting';
        } else {
            // 判断评论内容是否包含敏感词
            if (Helper::options()->SensitiveWords) {
                if (checkSensitiveWords(Helper::options()->SensitiveWords, $comment['text'])) {
                    $comment['status'] = 'waiting';
                }
            }
            // 判断评论是否至少包含一个中文
            if (Helper::options()->LimitOneChinese === 'on') {
                if (preg_match("/[\x{4e00}-\x{9fa5}]/u", $comment['text']) == 0) {
                    $comment['status'] = 'waiting';
                }
            }
        }
        Typecho_Cookie::delete('__typecho_remember_text');
        return $comment;
    }
}
?>