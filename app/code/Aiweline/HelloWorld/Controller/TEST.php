<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Controller;

use Aiweline\HelloWorld\Model\AiwelineHelloWorld;

class TEST extends \Weline\Framework\App\Controller\FrontendController
{
    private AiwelineHelloWorld $aiwelineHelloWorld;

    public function __construct(
        AiwelineHelloWorld $aiwelineHelloWorld
    ) {
        $this->aiwelineHelloWorld = $aiwelineHelloWorld;
    }

    public function testMethod()
    {
        $this->aiwelineHelloWorld->save([
            'entity_id'=>2,
            'demo'=>4
                                        ]);
        $data = $this->aiwelineHelloWorld->select()->fetch();
        $this->assign('data', $data);
        return $this->fetch();
    }
}
