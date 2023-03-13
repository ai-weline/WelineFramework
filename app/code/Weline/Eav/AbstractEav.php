<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/6 22:41:28
 */

namespace Weline\Eav;

use Weline\Eav\Cache\EavCache;
use Weline\Eav\Model\Attribute;
use Weline\Eav\Model\Entity;
use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Exception\Core;
use Weline\Framework\Manager\ObjectManager;

abstract class AbstractEav implements EavInterface
{
    public string $entity_code = '';
    public string $entity_name = '';
    public string $entity_id_field_type = '';
    public int $entity_id_field_length = 0;
    /**
     * @var \Weline\Eav\Model\Entity
     */
    private Entity $entity;
    /**
     * @var CacheInterface
     */
    private CacheInterface $eavCache;
    /**
     * @var \Weline\Eav\Model\Attribute
     */
    private Attribute $attribute;

    function __construct(
        Entity    $entity,
        EavCache  $eavCache,
        Attribute $attribute
    )
    {
        $this->entity    = $entity;
        $this->eavCache  = $eavCache->create();
        $this->attribute = $attribute;
    }

    function __init()
    {
        if (empty($this->entity_code) && empty($this::entity_code)) {
            throw new Exception(__('Eav模型未设置实体代码entity_code常量或者未设置entity_code属性。Eav类：%1', $this::class));
        }
        if (empty($this->entity_name) && empty($this::entity_name)) {
            throw new Exception(__('Eav模型未设置实体名entity_name常量或者未设置entity_name属性。Eav类：%1', $this::class));
        }
        if (empty($this->entity_id_field_type) && empty($this::entity_id_field_type)) {
            throw new Exception(__('Eav模型未设置实体代码entity_id_field_type常量或者未设置entity_id_field_type属性。Eav类：%1', $this::class));
        }
        if (empty($this->entity_id_field_length) && empty($this::entity_name)) {
            throw new Exception(__('Eav模型未设置实体名entity_id_field_length常量或者未设置entity_id_field_length属性。Eav类：%1', $this::class));
        }
    }

    public function getEntityName(): string
    {
        return $this->entity_name ?: $this::entity_name;
    }

    public function getEntityCode(): string
    {
        return $this->entity_code ?: $this::entity_code;
    }

    public function getEntityFieldIdType(): string
    {
        return $this->entity_id_field_type ?: $this::entity_id_field_type;
    }

    public function getEntityFieldIdLength(): int
    {
        return $this->entity_id_field_length ?: $this::entity_id_field_length;
    }

    /**
     * @inheritDoc
     */
    public function getEntity(): Entity
    {
        if ($entity = $this->eavCache->get($this->getEntityCode())) {
            return $entity;
        }
        $entity = $this->entity->load($this->getEntityCode());
        $this->eavCache->set($this->getEntityCode(), $entity);
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getAttribute(string $code, int|string $entity_id = null): Attribute
    {
        $this->attribute->clear()
                        ->where($this->attribute::fields_entity, $this->getEntityCode())
                        ->where($this->attribute::fields_code, $code)
                        ->find()->fetch();
        if (!$this->attribute->getId()) {
            $this->attribute->setData('values', []);
            return $this->attribute;
        }
        $attribute = clone $this->attribute;
        if ($entity_id) {
            /**@var \Weline\Eav\Model\Attribute\Type\Value $valueModel */
            $valueModel = ObjectManager::getInstance(Attribute\Type\Value::class);
            $valueModel->setAttribute($attribute);
            $this->attribute->clear()
                            ->fields('main_table.code,main_table.entity,main_table.name,main_table.type,v.value')
                            ->where($this->attribute::fields_entity, $this->getEntityCode())
                            ->where($this->attribute::fields_code, $code);
            $this->attribute->joinModel(
                $valueModel,
                'v',
                "main_table.code=v.attribute and v.entity_id='{$entity_id}'",
                'left', 'v.value'
            );
        }
        $attribute->setData('values', $this->attribute
            ->select()
            ->fetch());
        return $attribute;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attribute
            ->where($this->attribute::fields_entity, $this->getEntityCode())
            ->select()
            ->fetch()
            ->getItems();
    }

    /**
     * @inheritDoc
     * @throws null
     */
    public function addAttribute(string $code, string $name, string $type, bool $multi_value = false): bool
    {
        if ($this->attribute->where([$this->attribute::fields_entity => $this->getEntityCode(),
                                     $this->attribute::fields_code   => $code])->find()->fetch()->getId()) {
//            throw new Exception(__('实体（%1）已经存在属性（%2）', [$this->getEntityCode(), $code]));
            return false;
        }
        $this->existType($type);
        $this->existEntity($this->getEntityCode());
        try {
            $this->attribute->clear()->setData(
                [
                    $this->attribute::fields_code            => $code,
                    $this->attribute::fields_name            => $name,
                    $this->attribute::fields_type            => $type,
                    $this->attribute::fields_entity          => $this->getEntityCode(),
                    $this->attribute::fields_multiple_valued => intval($multi_value),
                ]
            )->forceCheck(true, [
                $this->attribute::fields_entity,
                $this->attribute::fields_code
            ])->save();
            return true;
        } catch (\Exception $exception) {
            p($exception->getMessage());
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function setAttribute(Attribute $attribute): bool
    {
        if ($attribute->getEntity() !== $this->getEntityCode()) {
            throw new Exception(__('警告：属性不属于当前Eav实体！当前实体：%1，当前属性：%2，当前属性所属实体：%3',
                                   [
                                       $this->getEntityCode(),
                                       $attribute->getCode() . ':' . $attribute->getName(),
                                       $attribute->getEntity()
                                   ]
                                )
            );
        }
        return $attribute->save(true);
    }

    /**
     * @param string $type 属性类型
     *
     * @return bool
     * @throws \Exception
     */
    public function existType(string $type): bool
    {
        /**@var \Weline\Eav\Model\Attribute\Type $typeModel */
        $typeModel = ObjectManager::getInstance(Attribute\Type::class);
        $typeModel->load($type);
        if ($typeModel->getId()) {
            return true;
        } else {
            throw new \Exception(__('属性类型不存在！类型：%1', $type));
        }
    }

    /**
     * @param string $code 实体是存在
     *
     * @return bool
     * @throws \Exception
     */
    public function existEntity(string $code): bool
    {
        /**@var Entity $entityModel */
        $entityModel = ObjectManager::getInstance(Entity::class);
        $entityModel->load($code);
        if ($entityModel->getId()) {
            return true;
        } else {
            throw new \Exception(__('属性所属实体不存在！实体：%1', $code));
        }
    }

    /**
     * @inheritDoc
     */
    public function unsetAttribute(string $code): bool
    {
        try {
            $this->attribute->load($code)->delete();
            return true;
        } catch (\ReflectionException|Exception|Core $e) {
            return false;
        }
    }
}