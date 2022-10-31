<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/30 15:05:57
 */

namespace Weline\Cron\Controller;

use Weline\Cron\Helper\CronStatus;
use Weline\Cron\Model\CronTask;
use Weline\Framework\Exception\Core;

class Cron extends \Weline\Framework\App\Controller\BackendController
{
    /**
     * @var \Weline\Cron\Model\CronTask
     */
    private CronTask $cronTask;

    function __construct(
        CronTask $cronTask
    )
    {
        $this->cronTask = $cronTask;
    }

    function listing()
    {
        $listings = $this->cronTask->pagination()->select()->fetch();
        $this->assign('tasks', $listings->getOriginData());
        $this->assign('pagination', $listings->getPagination());
        $this->assign('total', $listings->getPaginationData()['totalSize']);
        return $this->fetch();
    }

    function lock(): string
    {
        $task_id = $this->request->getPost('task_id');
        try {
            $task = $this->cronTask->load($task_id);
            $task->setData($task::fields_STATUS, CronStatus::BLOCK->value)
                 ->save();
//            return $this->fetchJson($this->success(__('锁定任务：%1', $task->getData('name'))));
            $this->getMessageManager()->addSuccess(__('锁定任务：%1', $task->getData('name')));
            $this->redirect('*/cron/listing');
        } catch (\ReflectionException|Core $e) {
            $this->getMessageManager()->addError(__('锁定任务失败：%1',$e->getMessage()));
            $this->redirect('*/cron/listing');
//            return $this->fetchJson($this->error($e->getMessage()));
        }
    }

    function unlock(): string
    {
        $task_id = $this->request->getPost('task_id');
        try {
            $task = $this->cronTask->load($task_id);
            $task->setData($task::fields_STATUS, CronStatus::PENDING->value)
                 ->save();
//            return $this->fetchJson($this->success(__('解锁任务：%1', $task->getData('name'))));
            $this->getMessageManager()->addSuccess(__('解锁任务：%1', $task->getData('name')));
            $this->redirect('*/cron/listing');
        } catch (\ReflectionException|Core $e) {
//            return $this->fetchJson($this->error($e->getMessage()));
            $this->getMessageManager()->addError(__('解锁任务失败：%1',$e->getMessage()));
            $this->redirect('*/cron/listing');
        }
    }
}