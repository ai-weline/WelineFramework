<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/6 20:41:35
 */

namespace Weline\Eav;

use Weline\Eav\Model\Attribute;
use Weline\Eav\Model\Entity;

interface EavInterface
{
    const entity_code = '';
    const entity_name = '';
    const entity_id_field_type = '';

    /**
     * @DESC          # 获取实体代码
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/6 22:16
     * 参数区：
     * @return string
     */
    public function getEntityCode(): string;

    /**
     * @DESC          # 获取实体实体ID字段类型
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/6 22:16
     * 参数区：
     * @return string
     */
    public function getEntityFieldIdType(): string;

    /**
     * @DESC          # 获取实体名
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/6 22:16
     * 参数区：
     * @return string
     */
    public function getEntityName(): string;

    /**
     * @DESC          # 获取实体
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/6 20:50
     * 参数区：
     * @return Entity
     */
    public function getEntity(): Entity;

    /**
     * @DESC          # 添加属性
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/6 20:50
     * 参数区：
     *
     * @param string $code 属性代码
     * @param string $name 属性名
     * @param string $type 属性类型
     *
     * @return mixed
     */
    public function addAttribute(string $code, string $name, string $type): bool;

    /**
     * @DESC          # 设置属性
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/6 21:34
     * 参数区：
     *
     * @param \Weline\Eav\Model\Attribute $attribute
     *
     * @return bool
     */
    public function setAttribute(Attribute $attribute): bool;

    /**
     * @DESC          # 卸载属性
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/6 21:36
     * 参数区：
     *
     * @param string $code 属性代码
     *
     * @return bool
     */
    public function unsetAttribute(string $code): bool;

    /**
     * @DESC          # 返回单个属性
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/6 20:53
     * 参数区：
     *
     * @param string          $code      属性的代码
     *
     * @param int|string|null $entity_id 实体的具体ID【例如产品ID,分类ID,具体要看实体是哪个】
     *
     * @return \Weline\Eav\Model\Attribute
     */
    public function getAttribute(string $code, int|string $entity_id = null): Attribute;

    /**
     * @DESC          # 返回多个属性
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/3/6 20:52
     * 参数区：
     * @return \Weline\Eav\Model\Attribute[]
     */
    public function getAttributes(): array;
}