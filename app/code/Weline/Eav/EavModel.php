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
use Weline\Framework\Database\Model;
use Weline\Framework\Exception\Core;
use Weline\Framework\Manager\ObjectManager;

abstract class EavModel extends Model implements EavInterface
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

    private array $attributes = [];

    function __construct(
        Entity    $entity,
        EavCache  $eavCache,
        Attribute $attribute,
                  $data = [],
    )
    {
        $this->entity    = $entity;
        $this->eavCache  = $eavCache->create();
        $this->attribute = $attribute;
        parent::__construct($data);
    }

    function __init()
    {
        parent::__init();
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
    public function getAttribute(string $code, int|string $entity_id = null): Attribute|null
    {
        // 如果已经有属性则直接返回
        /**@var Attribute $attribute */
        $attribute = $this->attributes[$code] ?? null;
        if ($attribute) {
            if ($entity_id && !$attribute->getData($attribute::value_key)) {
                $attribute = $attribute->getValue($entity_id, true);
            }
            return $attribute;
        }
        // 如果当前实体有ID则读取属性时跟随读取属性值
        if (!$entity_id) {
            $entity_id = $this->getId();
        }
        // 查找属性
        $this->attribute->clear()
                        ->where($this->attribute::fields_entity, $this->getEntityCode())
                        ->where($this->attribute::fields_code, $code)
                        ->find()
                        ->fetch();
        if (!$this->attribute->getId()) {
            return null;
        }
        $this->attribute->current_setEntity($this);
        if ($entity_id) {
            $this->attribute = $this->attribute->getValue($entity_id, true);
        }
        $this->attributes[$code] = clone $this->attribute;
        return $this->attributes[$code];
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        // 获取缓存属性
        $cache_key  = $this->getEntityCode() . '-attributes';
        $attributes = $this->eavCache->get($cache_key);
        if ($attributes) {
            foreach ($attributes as &$attribute) {
                /**@var Attribute $attribute */
                $attribute = ObjectManager::make(Attribute::class, ['data' => $attribute]);
                $attribute->current_setEntity($this->getEntity());
            }
            return $attributes;
        }
        // 数据库读取属性
        $attributes = $this->attribute
            ->where($this->attribute::fields_entity, $this->getEntityCode())
            ->select()
            ->fetch()
            ->getItems();
        $cache_data = [];
        foreach ($attributes as $attribute) {
            $cache_data[] = $attribute->getData();
            /**@var Attribute $attribute */
            $attribute->current_setEntity($this);
        }
        // 缓存属性
        $this->eavCache->set($cache_key, $cache_data, 300);
        return $attributes;
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
        if ($attribute->current_getEntity()->getEntityCode() !== $this->getEntityCode()) {
            throw new Exception(__('警告：属性不属于当前Eav实体！当前实体：%1，当前属性：%2，当前属性所属实体：%3',
                                   [
                                       $this->getEntityCode(),
                                       $attribute->getCode() . ':' . $attribute->getName(),
                                       $attribute->getEntity()
                                   ]
                                )
            );
        }
        if ($attribute->getValue()) {
            $attribute->unsetData('value');
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
    public function unsetAttribute(string $code, bool $remove_value = false): bool
    {
        try {
            if ($remove_value) {
                $this->attribute->w_getValueModel()
                                ->where('attribute', $code)
                                ->delete();
            }
            $this->attribute->load($code)->delete();
            return true;
        } catch (\ReflectionException|Exception|Core $e) {
            if(DEV) throw $e;
            return false;
        }
    }


    /**
     * @DESC          # Eav: 获取实体
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/15 22:43
     * 参数区：
     * @return \Weline\Eav\Model\Entity
     */
    public function eav_getEntity(): \Weline\Eav\Model\Entity
    {
        if ($entity = $this->eavCache->get($this->getEntityCode())) {
            return $entity;
        }
        $entity = $this->entity->load($this->getEntityCode());
        $this->eavCache->set($this->getEntityCode(), $entity);
        return $entity;
    }
}