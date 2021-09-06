<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Setup\Db;

use Weline\Framework\App\Exception;
use Weline\Framework\Database\AbstractModel;
use Weline\Framework\Database\Db\Ddl\Table;
use Weline\Framework\Database\Db\Ddl\Table\Alter;
use Weline\Framework\Database\Db\DdlFactory;
use Weline\Framework\Database\DbManager;
use Weline\Framework\Database\DbManager\ConfigProvider;

/**
 * 这个类用来对Model表结构修改，自动读取Model模型的表名和主键
 */
class ModelSetup extends DbManager
{
    protected AbstractModel $model;

    private Table $ddl_table;

    /**
     * Setup constructor.
     * @param ConfigProvider $configProvider
     * @param DdlFactory $ddl_table
     * @throws Exception
     * @throws \ReflectionException
     */
    function __construct(
        ConfigProvider $configProvider,
        DdlFactory     $ddl_table
    )
    {
        parent::__construct($configProvider);
        $this->ddl_table = $ddl_table->create();
    }

    /**
     * @DESC          # 设置模型
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/6 22:25
     * 参数区：
     * @param AbstractModel $model
     * @return $this
     */
    function putModel(AbstractModel $model): ModelSetup
    {
        $this->model = $model;
        return $this;
    }
    /**
     * @DESC         | 创建表
     *
     * 参数区：
     *
     * @param string $comment
     * @return Table\Create
     */
    public function createTable(string $comment = ''): Table\Create
    {
        return $this->ddl_table->createTable()->createTable($this->model->getOriginTableName(), $comment);
    }

    /**
     * @DESC         |修改表 两个都留空仅读取表修改类，用此类对表进行其他修改 【提示：如果对表名进行了修改，请紧接着修改Model模型名（或者模型提供对应表名，否则无法找到对应表）】
     *
     * 参数区：
     *
     * @param string $comment 留空不修改表注释
     * @param string $new_table_name 留空不修改表名
     * @return Alter
     */
    public function alterTable(string $comment = '', string $new_table_name = ''): Alter
    {
        return $this->ddl_table->alterTable()->forTable($this->model->getTable(), $this->model->primary_key, $comment, $new_table_name);
    }

    /**
     * @DESC          # 获取前缀
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:27
     * 参数区：
     * @return string
     */
    public function getTablePrefix(): string
    {
        $prefix = $this->getConfig()->getPrefix();
        return $prefix ?? '';
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     * @param string $table
     * @return bool
     * @throws Exception
     * @throws \ReflectionException
     * @throws \Weline\Framework\Database\Exception\LinkException
     */
    public function tableExist(): bool
    {
        try {
            $this->query("DESC {$this->model->getTable()}");
            return true;
        } catch (\PDOException $exception) {
            return false;
        }
    }

    /**
     * @DESC         |获取表名
     *
     * 参数区：
     *
     * @param string $name
     * @return string
     */
    public function getTable(string $name = ''): string
    {
        if (!strstr($name, $this->getTablePrefix())) {
            $name = $this->getTablePrefix() . $name;
        }
        return $name;
    }

    /**
     * @DESC         |删除表
     *
     * 参数区：
     *
     * @return bool
     */
    public function dropTable(): bool
    {
        try {
            $this->query('DROP TABLE ' . $this->model->getTable());
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @DESC          # 方法描述
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:56
     * 参数区：
     * @param string $sql
     * @return mixed
     * @throws Exception
     * @throws \ReflectionException
     * @throws \Weline\Framework\Database\Exception\LinkException
     */
    function query(string $sql): mixed
    {
        return $this->getLinker()->query($sql)->fetch();
    }
}