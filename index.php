<?php

declare(strict_types=1);

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */
$start = microtime(true);
require __DIR__ . '/pub/index.php';
$end = microtime(true);
var_dump(($start-$end)/1000000);