<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\Observer;

use Weline\Framework\Event\Event;
use Weline\Framework\View\Template;

class Compiler implements \Weline\Framework\Event\ObserverInterface
{
    const area = 'backend';
    const require_js_file = 'base' . DIRECTORY_SEPARATOR . 'require.configs.js';
    const require_js_type = 'require.configs.js';

    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        $eventData = $event->getEvenData();
        $area = $eventData->getArea();
        if (self::area === $area) {
            $type = $eventData->getType();
            switch ($type):
                case self::require_js_type:
                    $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR.'statics'. DIRECTORY_SEPARATOR . self::require_js_file;
                    if (!is_dir(dirname($path))) {
                        mkdir($path, 755);
                    }
                    if (!is_file($path)) {
                        touch($path);
                    }
                    file_put_contents($path, sprintf(<<<CONFIG_CONTEND
requirejs.config(
    //要在IE中及时获得正确的错误触发器，请强制进行定义/填充导出检查。
    %s
);
CONFIG_CONTEND, json_encode($eventData->getResources())));
                    break;
                default;
            endswitch;
        };
    }

    protected string $generate_content = '';

    function generateRequireConfigJsContent(array $resources): string
    {
        p(json_encode($resources));
        foreach ($resources as $key => $resource) {
            if (is_numeric($key)) {
                $this->generate_content .= '"' . $resource . '",';
            } else if (is_string($resource)) {
                $this->generate_content .= $key . ':"' . $resource . '",';
            } else if (is_array($resource)) {
                $this->generate_content .= $key . ':[';
                $this->generate_content = $this->generateRequireConfigJsContent($resource) . ',';
                $this->generate_content .= '],';
                /*foreach ($resource as $r_key => $r_item) {
                    if (is_array($r_item)) {
                        $this->generate_content = $this->generateRequireConfigJsContent($r_item) . ',';
                    } elseif (is_numeric($r_key)) {
                        $this->generate_content .= '"' . $r_item . '",';
                    } else {
                        $this->generate_content .= $r_key . ':"' . $r_item . '",';
                    }
                }*/
//                $this->generate_content .= '],';
            }
        }
        return $this->generate_content;
    }
    /*protected ?Template $template=null;
    function getTemplate(){
        if(!$this->template){
            $this->template=Template::getInstance()->init();
        }
        return $this->template;
    }*/
}