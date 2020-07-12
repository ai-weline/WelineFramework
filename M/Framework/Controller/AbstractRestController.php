<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/8
 * 时间：15:59
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Controller;


abstract class AbstractRestController extends Core
{
    const fetch_JSON = 'json';
    const fetch_XML = 'xml';
    const fetch_STRING = 'string';

    private array $_data;

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string|array $key
     * @param $value
     */
    function assign($key, $value = null)
    {
        if (is_string($key)) {
            $this->_data[$key] = $value;
        } elseif (is_array($key)) {
            $this->_data = $key;
        }
    }

    function fetch(string $type = self::fetch_JSON)
    {
        $data = null;
        switch ($type) {
            case self::fetch_STRING:
                foreach ($this->_data as $key => $datum) {
                    $data .= $key . ':' . $datum . ',';
                }
                $data = trim($data, ',');
                break;
            case self::fetch_XML:
                header("Content-type: text/xml; charset=UTF-8");
                $data = $this->setXml($this->_data);
                break;
            case self::fetch_JSON:
            default:
                header('Content-Type:application/json');
                $data = json_encode($this->_data);
                break;
        }
        return $data;
    }

    private function setXml(array $data)
    {
        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<$key>$val</$key>";
            } elseif (is_array($val)) {
                // TODO 多维数组 $val可能是数组
                $xml_ = str_replace('<xml>', '', $this->setXml($val));
                $xml_ = str_replace('</xml>', '', $xml_);
                $xml .= "<$key>{$xml_}</$key>";
            } else {
                $xml .= "<$key><![CDATA[$val]]></$key>";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
}