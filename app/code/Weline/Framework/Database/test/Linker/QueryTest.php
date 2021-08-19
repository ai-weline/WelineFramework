<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\test\Linker;

use Weline\Framework\Database\Linker;
use Weline\Framework\Manager\ObjectManager;

class QueryTest extends \Weline\Framework\UnitTest\TestCore
{

    public function testWhere()
    {
        /**@var \Weline\Framework\Database\DbManager $dbManager */
        $dbManager = ObjectManager::getInstance(\Weline\Framework\Database\DbManager::class);
        /**@var Linker\QueryInterface $query */
        $query = $dbManager->create()->getQuery();
//        pp($query->table('weline')->alias('a')->where("(a.stores = '1') OR (a.stores like '%1%')")->where('a.id=1')->where('a.id',1)->find()->fetch());
//        pp($query->table('weline')->alias('a')->where([['a.stores', 1], ['a.id', 1]])->find()->fetch());
//        pp($query->table('weline')->alias('a')->where([ ['a.stores', '=', '1', 'OR'], ['a.stores', 'like', '%1%'] ])->find()->fetch());
//        pp($query->table('weline')->alias('a')->fields('a.`id`,a.`stores`')->where('id',1)->where('stores',1)->find()->fetch());
//        pp($query->table('weline')
//            ->alias('a')
//            ->join('store_user su', 'su.store_id=a.stores','left')
//            ->where('a.id', 1)
//            ->where('a.stores', 1)
//            ->find()
//            ->fetch()
//        );
        pp($query->table('weline')
            ->alias('a')
            ->where('a.id', 1)
            ->update([
                'id' => 1,
                'stores' => 2
            ])
        );
        p($query->getLastSql());
    }
}
