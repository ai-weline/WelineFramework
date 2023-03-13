<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/6 20:25:54
 */

namespace Weline\Eav\Model;

use Weline\Eav\Model\Attribute\Type\Value;
use Weline\Framework\App\Exception;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Attribute extends \Weline\Framework\Database\Model
{

    public const fields_ID              = 'code';
    public const fields_code            = 'code';
    public const fields_name            = 'name';
    public const fields_type            = 'type';
    public const fields_entity          = 'entity';
    public const fields_multiple_valued = 'multiple_valued';

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
//        $setup->dropTable();
        if (!$setup->tableExist()) {
            $setup->createTable('属性表')
                  ->addColumn(
                      self::fields_ID,
                      TableInterface::column_type_INTEGER,
                      0,
                      'primary key auto_increment',
                      'ID')
                  ->addColumn(
                      self::fields_code,
                      TableInterface::column_type_VARCHAR,
                      60,
                      'unique',
                      '代码')
                  ->addColumn(
                      self::fields_entity,
                      TableInterface::column_type_VARCHAR,
                      60,
                      'not null',
                      '所属实体')
                  ->addColumn(
                      self::fields_name,
                      TableInterface::column_type_VARCHAR,
                      120,
                      'not null',
                      '名称')
                  ->addColumn(
                      self::fields_type,
                      TableInterface::column_type_VARCHAR,
                      120,
                      'not null',
                      '类型')
                  ->addColumn(
                      self::fields_multiple_valued,
                      TableInterface::column_type_SMALLINT,
                      0,
                      'default 0',
                      '是否多值')
                  ->create();
        }
    }

    function getEntity(): string
    {
        return $this->getData(self::fields_entity) ?: '';
    }


    function setEntity(string $entity): static
    {
        return $this->setData(self::fields_entity, $entity);
    }

    function getCode(): string
    {
        return $this->getData(self::fields_code) ?: '';
    }

    function setCode(string $code): static
    {
        return $this->setData(self::fields_code, $code);
    }

    function getType(): string
    {
        return $this->getData(self::fields_type) ?: '';
    }

    function setType(string $type): static
    {
        return $this->setData(self::fields_type, $type);
    }


    function getName(): string
    {
        return $this->getData(self::fields_name) ?: '';
    }

    function setName(string $name): static
    {
        return $this->setData(self::fields_name, $name);
    }

    function getMultipleValued(): bool
    {
        return (bool)$this->getData(self::fields_multiple_valued);
    }

    function setMultipleValued(bool $is_multiple_valued = false): static
    {
        return $this->setData(self::fields_multiple_valued, $is_multiple_valued);
    }

    function getValue(string $entity_id = null)
    {
        if (!$this->getData(self::fields_entity)) {
            throw new Exception(__('该属性没有entity实体！'));
        }
        if (!$this->getData(self::fields_code)) {
            throw new Exception(__('该属性没有code代码！'));
        }
        if ($entity_id) {
            $attribute  = clone $this;
            $valueModel = $this->getValueModel();
            $valueModel->setAttribute($this);
            $attribute->clear()
                      ->fields('main_table.code,main_table.entity,main_table.name,main_table.type,v.value')
                      ->where($attribute::fields_entity, $attribute->getEntity())
                      ->where($attribute::fields_code, $attribute->getCode());
            $attribute->joinModel(
                $valueModel,
                'v',
                "main_table.code=v.attribute and v.entity_id='{$entity_id}'",
                'left', 'v.value'
            );
            if ($attribute->getMultipleValued()) {
                $attribute->setData('values', $attribute
                    ->select()
                    ->fetch());
            } else {
                $attribute->setData('values', $attribute
                    ->find()
                    ->fetch());
            }
            $attribute->setData('values', $attribute
                ->select()
                ->fetch());
            return $attribute->getValue();
        }
        return $this->getData('value');
    }

    /**
     * @DESC          # 方法描述
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/13 20:19
     * 参数区：
     *
     * @param \Weline\Eav\Model\Attribute\Type\Value|array|string|int $value     Array:[['entity_id'=>1,'value'=>1],...] 或者 1 或者 ‘1’
     * @param string|int                                              $entity_id 如果有这个参数 value参数可以是[1,2,3...] 示例：setValue([1,2,3],1)
     *
     * @return \Weline\Eav\Model\Attribute
     * @throws \Weline\Framework\App\Exception
     */
    function setValue(Attribute\Type\Value|array|string|int $value, string|int $entity_id): static
    {
        if (is_string($value) || is_int($value)) {
            $this->getValueModel()->setEntityId($entity_id)
                 ->setValue($value)
                 ->forceCheck(true, [Value::fields_attribute, Value::fields_entity_id, Value::fields_value])
                 ->save(true);
        } elseif (is_array($value)) {
            $data = [];
            foreach ($value as $item) {
                if ((!isset($item['entity_id']) || !$item['entity_id'])) {
                    $item['entity_id'] = $entity_id;
                }
                if ((!isset($item['value']) || !$item['value'])) {
                    throw new Exception(__('属性值未设置或为空！'));
                }
                $data[] = ['entity_id' => $item['entity_id'], 'value' => $item['value'], 'attribute' => $this->getCode()];
            }
            $this->getValueModel()->insert($data, ['entity_id', 'value', 'attribute'])->fetch();
        } elseif ($value instanceof Value) {
            $value->save(true);
        }
        return $this;
    }

    function getValueModel(): Attribute\Type\Value
    {
        /**@var \Weline\Eav\Model\Attribute\Type\Value $valueModel */
        $valueModel = ObjectManager::getInstance(Attribute\Type\Value::class);
        $valueModel->setAttribute($this);
        return $valueModel;
    }
}