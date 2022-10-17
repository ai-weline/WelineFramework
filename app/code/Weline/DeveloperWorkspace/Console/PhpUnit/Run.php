<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/16 08:37:10
 */

namespace Weline\DeveloperWorkspace\Console\PhpUnit;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;

class Run implements \Weline\Framework\Console\CommandInterface
{

    /**
     * @inheritDoc
     */
    public function execute(array $args = [])
    {
        $php_unit_xml_path = BP . 'phpunit.xml';
        # 先把所有模组的test文件目录写到phpunit.xml【避免全目录扫描提升测试速度】
        $modules      = Env::getInstance()->getActiveModules();
        $php_unit_xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="http://schema.phpunit.de/6.2/phpunit.xsd"
         backupGlobals="false"
         backupStaticAttributes="false"
         bootstrap="tests/bootstrap.php"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         defaultTestSuite="unit"
         processIsolation="false"
         stopOnFailure="false">
    <testsuites>';

        foreach ($modules as $module) {
            $test_path      = $module['base_path'] . 'test';
            $testsuite_path = $module['base_path'] . 'test' . DS . 'testsuite.xml';
            $testsuites     = '';
            if (is_file($testsuite_path)) {
                $xml = simplexml_load_file($testsuite_path);
                foreach ($xml->children() as $testsuite) {
                    $testsuite = get_object_vars($testsuite);
                    if (!isset($testsuite['@attributes']['name'])) {
                        throw new Exception(__('testsuite套件配置错误,未配置套件名：%1 ，示例：<testsuite name="unit">
        <file>TestCache.php</file>
    </testsuite>', $testsuite_path));
                    }
                    $suite_name = $testsuite['@attributes']['name'] ?? $module['name'];
                    unset($testsuite['@attributes']);
                    foreach ($testsuite as $key => $testsuite_data) {
                        $testsuites .= "
        <testsuite name='unit'>
            <{$key}>{$testsuite_data}</{$key}>
        </testsuite>
        <testsuite name='$suite_name'>
            <{$key}>{$testsuite_data}</{$key}>
        </testsuite>
                        ";
                    }
                }
            } else {
                $testsuites .= "
        <testsuite name='unit'>
            <directory suffix=\"Test.php\">$test_path</directory>
        </testsuite>
        <testsuite name='{$module['name']}'>
            <directory suffix=\"Test.php\">$test_path</directory>
        </testsuite>
                        ";
            }
            $php_unit_xml .= "
            $testsuites
            ";
        }
        $php_unit_xml .= '    </testsuites>
</phpunit>';
        file_put_contents( $php_unit_xml_path, $php_unit_xml);
        // TODO 使用xml文件测试
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return 'PhpUnite测试套件测试命令';
    }
}