<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/6/15
 * 时间：17:48
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Database\test;


use Weline\Framework\App\Env;
use Weline\Framework\Database\DbManager\ConfigProvider;
use Weline\Framework\Database\Linker;
use Weline\Framework\Manager\ObjectManager;

class DbManager extends \Weline\Framework\UnitTest\TestCore
{
    function testCreate(){
        $dbManager = ObjectManager::getInstance(\Weline\Framework\Database\DbManager::class);
        /**@var Linker $linker*/
        $linker = $dbManager->create();
        p($linker->query('show tables;'));
    }
}
/**

php bin/m system:install ^
--db-type=mysql ^
--db-hostname=127.0.0.1 ^
--db-database=weline ^
--db-username=weline ^
--db-password=weline

 */