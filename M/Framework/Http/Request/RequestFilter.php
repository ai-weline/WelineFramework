<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/26
 * 时间：15:07
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Http\Request;


class RequestFilter
{
    private static $instance;
    const request_ENCODES = array(
        'UTF-8', "ASCII", "GB2312", "GBK", 'BIG5'
    );
    const default_ENCODE = 'UTF-8';

    private function __clone()
    {

    }

    private function __construct()
    {
    }

    final static function getInstance()
    {
        if (self::$instance == null) self::$instance = new self();
        return self::$instance;
    }

    /**
     * @DESC         |安全过滤
     *
     * 参数区：
     *
     * @param $param
     * @return String
     */
    function safeFilter($param)
    {
        // xss攻击过滤
        $param = $this->m_remove_xss($param);
        // 过滤不安全的控制字符
        return $this->m_trim_unsafe_control_chars($param);
    }

    /**
     * @DESC         |返回经htmlspecialchars处理过的字符串或数组
     *
     * 参数区：
     *
     * @param string|array $param 需要处理的字符串或数组
     * @return array|false|string|string[]|null
     */
    function m_html_special_chars($param)
    {
        // 安全过滤
        if (is_string($param)) {
            // xss攻击过滤
            $param = $this->m_remove_xss($param);
            // 过滤不安全的控制字符
            $param = $this->m_trim_unsafe_control_chars($param);
        };
        // 编码默认utf-8
        $encode = mb_detect_encoding($param, self::request_ENCODES);
        if ($encode != self::request_ENCODES) $param = mb_convert_encoding($param, self::default_ENCODE, $encode);
        // 非数组数组过滤
        if (!is_array($param)) return htmlspecialchars($param, ENT_QUOTES, self::default_ENCODE);
        // 数组过滤
        foreach ($param as $key => $val) $param[$key] = $this->m_html_special_chars($val);

        return $param;
    }

    /**
     * @DESC         |html实体解码
     *
     * 参数区：
     *
     * @param $param
     * @return string
     */
    function m_html_entity_decode($param)
    {
        $encode = mb_detect_encoding($param, self::request_ENCODES);
        if ($encode != self::default_ENCODE) $param = mb_convert_encoding($param, self::default_ENCODE, $encode);;
        return html_entity_decode($param, ENT_QUOTES, self::default_ENCODE);
    }

    /**
     * @DESC         |html代码转义
     *
     * 参数区：
     *
     * @param $param
     * @return string
     */
    function m_htmlentities($param)
    {
        $encode = mb_detect_encoding($param, self::request_ENCODES);
        if ($encode != self::default_ENCODE) $param = mb_convert_encoding($param, self::default_ENCODE, $encode);
        return htmlentities($param, ENT_QUOTES, self::default_ENCODE);
    }

    /**
     * 安全过滤函数
     *
     * @param $string
     * @return string
     */
    function m_safe_replace($string)
    {
        $string = str_replace('%20', '', $string);
        $string = str_replace('%27', '', $string);
        $string = str_replace('%2527', '', $string);
        $string = str_replace('*', '', $string);
        $string = str_replace('"', '"', $string);
        $string = str_replace("'", '', $string);
        $string = str_replace('"', '', $string);
        $string = str_replace(';', '', $string);
        $string = str_replace('<', '<', $string);
        $string = str_replace('>', '>', $string);
        $string = str_replace("{", '', $string);
        $string = str_replace('}', '', $string);
        $string = str_replace('\\', '', $string);
        return $string;
    }

    /**
     * xss过滤函数
     *
     * @param $string
     * @return string
     */
    function m_remove_xss($string)
    {
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

        $parm1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');

        $parm2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

        $parm = array_merge($parm1, $parm2);

        for ($i = 0; $i < sizeof($parm); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($parm[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                    $pattern .= '|(&#0([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $parm[$i][$j];
            }
            $pattern .= '/i';
            $string = preg_replace($pattern, ' ', $string);
        }
        return $string;
    }

    /**
     * 过滤ASCII码从0-28的控制字符
     * @return String
     */
    function m_trim_unsafe_control_chars($string)
    {
        $rule = '/[' . chr(1) . '-' . chr(8) . chr(11) . '-' . chr(12) . chr(14) . '-' . chr(31) . ']*/';
        return str_replace(chr(0), '', preg_replace($rule, '', $string));
    }

    /**
     * 格式化文本域内容
     *
     * @param string $string 文本域内容
     * @return string
     */
    function trim_textarea($string)
    {
        $string = nl2br(str_replace(' ', ' ', $string));
        return $string;
    }
}