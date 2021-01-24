<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/1/20
 * 时间：23:45
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Config;


use Magento\Framework\Config\Dom\UrnResolver;
use Magento\Framework\Config\Dom\ValidationSchemaException;
use Magento\Framework\Phrase;
use Weline\Framework\Exception\Core;
use Weline\Framework\System\File\Scanner;
use Weline\Framework\System\FileReader;
use Weline\Framework\Xml\Parser;

class Reader extends FileReader
{
    const ROOT_NAMESPACE_PREFIX = 'x';
    private static $urnResolver;
    /**
     * Format of items in errors array to be used by default. Available placeholders - fields of \LibXMLError.
     */
    const ERROR_FORMAT_DEFAULT = "%message%\nLine: %line%\n";
    private static $resolvedSchemaPaths;
    /**
     * @var Parser
     */
    private Parser $parser;

    function __construct(
        Scanner $scanner,
        Parser $parser,
        $path = 'module.xml')
    {
        parent::__construct($scanner, 'etc' . DIRECTORY_SEPARATOR . $path);
        $this->parser = $parser;
    }

    /**
     * @DESC         |读取配置信息
     *
     * 参数区：
     * @param string $path
     */
    function read($path = 'config/event/')
    {
        $pathArr = array_filter(explode('/', $path));
        $data = [];
        foreach ($this->getFileList() as $vendor => $module_files) {
            foreach ($module_files as $module_name => $module_file) {
                $xmlArray = $this->parser->load($module_file)->xmlToArray();
                $data = array_merge($data, $this->getByPath($xmlArray, $pathArr));
            }
        }
        return $data[array_pop($pathArr)];
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param $xmlArray
     * @param $pathArr
     * @return array
     */
    function getByPath($xmlArray, $pathArr)
    {
        $data = [];
        if (empty($pathArr)) return $xmlArray;
        foreach ($pathArr as $key => $levelPath) {
            unset($pathArr[$key]);
            // 只有单个值时其键名是_value
            if (array_key_exists('_value', $xmlArray[$levelPath])) {
                $xmlArray = $xmlArray[$levelPath]['_value'];
                if ($xmlArray) {
                    $data[$levelPath] = $this->getByPath($xmlArray, $pathArr);
                } else {
                    $data[$levelPath] = $xmlArray[$levelPath];
                }
            } else {
                // 存在相同节点时其键名是数字
                $tmp = [];
                $tmp_key = [];
                foreach ($xmlArray[$levelPath] as $item) {
                    $hasMerged = false;
                    $xmlArray = $item['_value'];
                    $res_data = $this->getByPath($xmlArray, $pathArr);
                    // ID相同合并最后一个
                    $mergeAttributes = ['id','name'];
                    foreach ($mergeAttributes as $mergeAttribute) {
                        if (array_key_exists('id', $res_data)) {
                            if (isset($tmp_key[$res_data['_attribute'][$mergeAttribute]])) {
                                foreach ($tmp as $k => $i) {
                                    if (isset($tmp_key[$i['_attribute'][$mergeAttribute]])) $tmp[$k] = $res_data;
                                    $hasMerged = true;
                                }
                            };
                            $tmp_key[$res_data['_attribute'][$mergeAttribute]] = true;
                        }
                    }
                    if (!$hasMerged) $tmp[] = $res_data;
                }
                $data[$levelPath] = $tmp;
            }
        }
        return $data;
    }
}