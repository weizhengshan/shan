<?php
/**
 * Created by PhpStorm.
 * User: Mr.Wei
 * Date: 2018/1/9
 * Time: 16:32
 */

/*url拼接函数 支持多模块*/
function url($url, $vars = '', $suffix = true, $domain = false)
{
    return U(strtolower(MODULE_NAME) . $url, $vars, $suffix, $domain);
}

function tableFix($tableName)
{
    return C('DB_PREFIX') . $tableName;
}

/*
 * 处理分页方法 实现对limitd的组合
 * @param object $page [分页对象]
 */
function page($page)
{
    $limit = $page->firstRow . ',' . $page->listRows;
    return $limit;
}

/**
 * 生成二维码
 * @param  string $url url连接
 * @param  integer $size 尺寸 纯数字
 */
function qrcode($url, $size = 8)
{
    Vendor('Phpqrcode.phpqrcode');
    QRcode::png($url, false, QR_ECLEVEL_L, $size, 2, false, 0xFFFFFF, 0x000000);
}

/**
 * 七牛云上传
 */
function Qiniu_Encode($str) // URLSafeBase64Encode
{
    $find = array('+', '/');
    $replace = array('-', '_');
    return str_replace($find, $replace, base64_encode($str));
}

function Qiniu_Sign($url)
{
    //$info里面的url
    $setting = C('UPLOAD_SITEIMG_QINIU');
    $duetime = NOW_TIME + 86400;
    //下载凭证有效时间
    $DownloadUrl = $url . '?e=' . $duetime;
    $Sign = hash_hmac('sha1', $DownloadUrl, $setting ["driverConfig"] ["secrectKey"], true);
    $EncodedSign = Qiniu_Encode($Sign);
    $Token = $setting ["driverConfig"] ["accessKey"] . ':' . $EncodedSign;
    $RealDownloadUrl = $DownloadUrl . '&token=' . $Token;
    return $RealDownloadUrl;
}

/**
 * 搜索条件空值过滤 以及时间范围组合
 * @param Array $where [过滤条件]
 * @param Array [$rangeTimeField] [需要处理的时间范围]
 */
function filterWhere(&$where, $rangeTimeField = null)
{
    if (!empty($where)) {
        foreach ($where as $k => $v) {
            if (empty($v)) {
                unset($where[$k]);
            } else {
                if (in_array($k, $rangeTimeField)) {
                    $whereTime = explode('--', $where[$k]);
                    $where[$k] = array(
                        array('gt', strtotime($whereTime[0])),
                        array('lt', strtotime($whereTime[1]))
                    );
                }
            }
        }
    }
}

/*
 * 获取当前url
 */
function getUrl()
{
    return $_SERVER["REQUEST_URI"];
}

/*
 * 求数组最小值 以及最小值对应的键
 * @param Array $array [数组]
 * @return Array
 */
function array_min($array)
{
    $key = 0;
    $min = $array[$key];
    foreach ($array as $k => $v) {
        if ($v < $min) {
            $key = $k;
            $min = $v;
        }
    }
    return array('min' => $min, 'key' => $key);
}

/*
 * 生成随机字符串
 * @param Integer $length [字符创长度]
 * @pram Boolean $numeric [是否属数字]
 * @return String
 */
function random($length = 6, $numeric = 0)
{
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

/*
 * 随机码
 * @param Integer $length [字符串长度]
 * @param boolean [$number] ['是否是纯数字']
 * @return String
 */
function randomkeys($length, $number = false)
{
    $key = null;
    if ($number) {
        $pattern = '1234567890';
    } else {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    }
    for ($i = 0; $i < $length; $i++) {
        if ($number) {
            $key .= $pattern{mt_rand(0, 9)};
        } else {
            $key .= $pattern{mt_rand(0, 35)};
        }
    }
    return $key;
}

/*
 *         利用百度地图接口获取经纬度
 */
function getPointer()
{
    $url = 'http://api.map.baidu.com/location/ip?ak=C93b5178d7a8ebdb830b9b557abce78b&coor=bd09ll';
    $data = file_get_contents($url);
    $dataContent = json_decode($data, true);
    $point = $dataContent['content']['point'];
    return $point;
}

/*
 * 通过经纬度获取当前位置  百度接口
 * @param Float $lat [经度]
 * @param Float $lgn [纬度]
 * @return Array
 */
function getlocationByPoint($lat, $lng)
{

    $url = 'http://api.map.baidu.com/geocoder/v2/?ak=C93b5178d7a8ebdb830b9b557abce78b&location=' . $lng . ',' . $lat . '&output=json&pois=0';
    $data = file_get_contents($url);
    $result = json_decode($data, true);
    $addressInfo['address'] = $result['result']['formatted_address'];   //详细地址
    $addressInfo['province'] = $result['result']['addressComponent']['province'];
    $addressInfo['city'] = $result['result']['addressComponent']['city'];
    $addressInfo['area'] = $result['result']['addressComponent']['district'];

    return $addressInfo;
}

/*
 * 获取当前ip地址获取相应的信息 百度api
 * @param $ip
 * @return Array
 */
function getLocation($ip)
{
    $url = 'http://api.map.baidu.com/location/ip?ak=C93b5178d7a8ebdb830b9b557abce78b&coor=bd09ll';
    $data = file_get_contents($url);
    return json_decode($data);
}


function str_split_unicode($str, $l = 0) {
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, "UTF-8");
        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, "UTF-8");
        }
        return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}
/*
 * 不想die的话，第二个参数传0
 */
function dd($data,$id_die = 1){
    // 定义样式
    $str = '<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;
        line-height: 1.42857;color: #333;word-break: break-all;
        word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    // 如果是boolean或者null直接显示文字；否则print
    if (is_bool($data)) {
        $show_data=$data ? 'true' : 'false';
    }elseif (is_null($data)) {
        $show_data='null';
    }else{
        $show_data=print_r($data,true);
    }
    $str.=$show_data;
    $str.='</pre>';
    echo $str;
    if ($id_die) {
        die;
    }
}
/**
 * Thinkphp默认分页样式转Bootstrap分页样式
 * @author H.W.H
 * @param string $page_html tp默认输出的分页html代码
 * @return string 新的分页html代码
 */
function bootstrap_page_style($page_html){
    if ($page_html) {
        $page_show = str_replace('<div>','<nav><ul class="pagination">',$page_html);
        $page_show = str_replace('</div>','</ul></nav>',$page_show);
        $page_show = str_replace('<span class="current">','<li class="active"><a>',$page_show);
        $page_show = str_replace('</span>','</a></li>',$page_show);
        $page_show = str_replace(array('<a class="num"','<a class="prev"','<a class="next"','<a class="end"','<a class="first"'),'<li><a',$page_show);
        $page_show = str_replace('</a>','</a></li>',$page_show);
    }
    return $page_show;
}

/**
 * ThinkPHP 基础分页的相同代码封装，使前台的代码更少
 * @param $totalRows 要分页的总记录数
 * @return \Think\Page
 */
function getPage($totalRows) {
    $page = new \Think\Page($totalRows, C('PAGESIZE'));
    $page -> rollPage = 5;
    $page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
    $page->setConfig('prev','上一页');
    $page->setConfig('next','下一页');
    $page->setConfig('last','末页');
    $page->setConfig('first','首页');
    $page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
    $page->lastSuffix = false;//最后一页不显示为总页数
    return $page;
}
