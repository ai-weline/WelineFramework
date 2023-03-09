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

abstract class EavAbstract implements EavInterface
{
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
    public function addAttribute(string $code, string $name, string $type): bool
    {
        return $this->attribute->setData(
            [
                $this->attribute::fields_code   => $code,
                $this->attribute::fields_name   => $name,
                $this->attribute::fields_type   => $type,
                $this->attribute::fields_entity => $this->getEntityCode(),
            ]
        )->forceCheck(true, [
            $this->attribute::fields_entity,
            $this->attribute::fields_code
        ])->save();
    }

    /**
     * @inheritDoc
     */
    public function getAttribute(string $code,int|string $entity_id=null): Attribute
    {

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
     */
    public function setAttribute(Attribute $attribute): bool
    {

    }

    /**
     * @inheritDoc
     */
    public function unsetAttribute(string $code): bool
    {
        try {
            $this->entity->load($code)->delete();
            return true;
        } catch (\ReflectionException|Exception|Core $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getEntityCode(): string
    {
        return self::entity_code;
    }

    /**
     * @inheritDoc
     */
    public function getEntityName(): string
    {
        return self::entity_name;
    }
}