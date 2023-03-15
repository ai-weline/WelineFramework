<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/6 23:01:21
 */

namespace Weline\Eav\Model\Attribute\Type;

use Weline\Eav\Model\Attribute;
use Weline\Eav\Model\Entity;
use Weline\Framework\App\Exception;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Value extends \Weline\Framework\Database\Model
{
    public const fields_ID        = 'value_id';
    public const fields_attribute = 'attribute';
    public const fields_entity_id = 'entity_id';
    public const fields_value     = 'value';

    private ?Attribute $attribute = null;

    /**
     * @DESC          # 设置值属性
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/9 22:35
     * 参数区：
     *
     * @param \Weline\Eav\Model\Attribute $attribute
     *
     * @return $this
     * @throws null
     */
    public function setAttribute(Attribute $attribute): static
    {
        if (empty($attribute->getId())) {
            throw new Exception(__('属性不存在！'));
        }
        $this->attribute = $attribute;
        $this->setData(self::fields_attribute, $attribute->getCode());
        $this->getTable();
        return $this;
    }

    /**
     * @DESC          # 设置值属性
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/9 22:35
     * 参数区：
     *
     * @param string $code
     *
     * @return $this
     */
    public function setAttributeCode(string $code): static
    {
        $this->setData(self::fields_attribute, $code);
        return $this;
    }

    function getAttribute()
    {
        if ($this->attribute) {
            return $this->attribute;
        }
        $this->attribute = ObjectManager::getInstance(Attribute::class);
        return $this->attribute->load($this->getData(self::fields_attribute));
    }

    public function getTable(string $table = ''): string
    {
        if ($table) {
            return parent::getTable($table);
        }
        if (!$this->attribute) {
            throw new Exception(__('属性不存在！'));
        }
        $table                   = 'eav_' . $this->attribute->getEntity() . '_' . $this->attribute->getType();
        $this->origin_table_name = parent::getTable($table);
        return $this->origin_table_name;
    }

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
        /**@var \Weline\Eav\Model\Entity $entity */
        $entity = ObjectManager::getInstance(\Weline\Eav\Model\Entity::class);

        // 创建对应实体类型值表
        /**@var \Weline\Framework\Setup\Db\ModelSetup $setup */
        $setup = ObjectManager::getInstance(\Weline\Framework\Setup\Db\ModelSetup::class);
        /**@var \Weline\Eav\Model\Attribute\Type $type */
        $type = ObjectManager::getInstance(Attribute\Type::class);

        $types    = $type->select()->fetch()->getItems();
        $entities = $entity->clear()->select()->fetch()->getItems();
        /**@var Entity $entity */
        foreach ($entities as $entity) {
            /**@var \Weline\Eav\Model\Attribute\Type $type */
            foreach ($types as $type) {
                $eav_entity_type_table = $setup->getTable('eav_' . $entity->getCode() . '_' . $type->getCode());
                $setup->dropTable($eav_entity_type_table);
                if (!$setup->tableExist($eav_entity_type_table)) {
                    $setup->createTable('实体' . $entity->getCode() . '的Eav模型' . $type->getCode() . '类型数据表', $eav_entity_type_table)
                          ->addColumn(
                              self::fields_attribute,
                              TableInterface::column_type_VARCHAR,
                              60,
                              'not null ',
                              '属性'
                          )
                          ->addColumn(
                              self::fields_entity_id,
                              $entity->getEntityIdFieldType(),
                              $entity->getEntityIdFieldLength(),
                              'not null',
                              '实体ID'
                          )
                          ->addColumn(
                              self::fields_value,
                              $type->getFieldType(),
                              $type->getFieldLength(),
                              'not null',
                              '实体值'
                          )
                          ->addConstraints('primary key(`' . self::fields_attribute . '`,`' . self::fields_entity_id . '`,`'
                                           . self::fields_value . '`)')
                          ->create();
                }
            }
        }
    }

    function setValueId(int $value_id): static
    {
        return $this->setData(self::fields_ID, $value_id);
    }

    function getValueId(): int
    {
        return intval($this->getData(self::fields_ID));
    }

    function setEntityId(string|int $id): static
    {
        return $this->setData(self::fields_entity_id, $id);
    }

    function getEntityId(): int|string
    {
        return $this->getData(self::fields_entity_id);
    }

    function setValue(string|int $value): static
    {
        return $this->setData(self::fields_value, $value);
    }

    function getValue(): string|int
    {
        return $this->getData(self::fields_value);
    }
}