<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

return [
    'Aiweline_HelloWorld' => [
        'status'      => 1,
        'version'     => '1.1.0',
        'router'      => 'hello',
        'description' => '<a href="bbs.aiweline.com">Index</a>',
        'path'        => 'Aiweline/HelloWorld',
    ],
    'Aiweline_Index' => [
        'status'      => 1,
        'version'     => '1.1.0',
        'router'      => '',
        'description' => '<p>模组名：Aiweline_Index</p>
<p>描述：首页应用，此应用用于配置网站首页！</p>
<p>作者：秋枫雁飞（Aiweline）</p>
<p>签名：人生总要做点有意义的事儿，非是一定要成功，但是一定是在做。</p>
<a href="http://bbs.aiweline.com"></a>',
        'path' => 'Aiweline/Index',
    ],
    'Aiweline_NewsSource' => [
        'status'      => 1,
        'version'     => '1.1.0',
        'router'      => 'news',
        'description' => '<p>模组名：NewsSource</p>
<p>作者：秋枫雁飞（Aiweline）</p>
<p>签名：人生总要做点有意义的事儿，非是一定要成功，但是一定是在做。</p>
<a href="http://bbs.aiweline.com"></a>',
        'path' => 'Aiweline/NewsSource',
    ],
    'Aiweline_Bbs' => [
        'status'      => 1,
        'version'     => '1.1.0',
        'router'      => 'bbs',
        'description' => '<a href="bbs.aiweline.com">Bbs</a>',
        'path'        => 'Aiweline\\Bbs',
    ],
    'Aiweline_Test2' => [
        'status'      => 1,
        'version'     => '1.0.0',
        'router'      => '',
        'description' => '',
        'path'        => 'Aiweline\\Test2',
    ],
    'Weline_Admin' => [
        'status'      => 1,
        'version'     => '1.0.0',
        'router'      => 'admin',
        'description' => '<a href="bbs.aiweline.com">Admin模块</a>',
        'path'        => 'Weline\\Admin',
    ],
];
