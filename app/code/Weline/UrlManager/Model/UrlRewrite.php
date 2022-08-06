<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\UrlManager\Model;

use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class UrlRewrite extends \Weline\Framework\Database\Model
{

    const fields_ID      = 'rewrite_id';
    const fields_URL_ID  = 'url_id';
    const fields_PATH    = 'path';
    const fields_REWRITE = 'rewrite';

    /**
     * @inheritDoc
     */
    public function setup(ModelSetup $setup, Context $context): void
    {
        $this->install($setup, $context);
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    public function install(ModelSetup $setup, Context $context): void
    {
        if (!$setup->tableExist()) {
            $setup->createTable()
                  ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, null, 'primary key auto_increment', '重写ID')
                  ->addColumn(self::fields_URL_ID, TableInterface::column_type_INTEGER, null, 'not null', 'URL ID')
                  ->addColumn(self::fields_PATH, TableInterface::column_type_TEXT, null, 'not null', 'URL路径')
                  ->addColumn(self::fields_REWRITE, TableInterface::column_type_TEXT, null, 'not null', 'URL重写路径')
                  ->create();
        }
    }
}