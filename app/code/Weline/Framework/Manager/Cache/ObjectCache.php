<?php
/**
 * @Author       秋枫雁飞
 * @Email        aiweline@qq.com/1714255949@qq.com
 * @Desc         文件由Aiweline(秋枫雁飞)编写，若有升级需要
 *               建议不要随意修改文件源码。
 **/

namespace Weline\Framework\Manager\Cache;


class ObjectCache extends \Weline\Framework\Cache\CacheManager
{
    public function __construct(string $identity = 'framework_object')
    {
        parent::__construct($identity);
    }
}