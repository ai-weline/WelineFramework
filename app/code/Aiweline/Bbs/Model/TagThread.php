<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Bbs\Model;

use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class TagThread extends \Weline\Framework\Database\Model
{
    const table = 'bbs_tag_thread';
    const fields_ID  = 'tagid';
    public function provideTable(): string
    {
        return self::table;
    }
    public function providePrimaryField(): string
    {
        return self::fields_ID;
    }

    /**
     * @inheritDoc
     */
    function setup(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement setup() method.
    }

    /**
     * @inheritDoc
     */
    function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    function install(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement install() method.
    }
}