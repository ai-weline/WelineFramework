<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\NewsSource\Service;

use Aiweline\NewsSource\Model\AiwelineNews;
use Aiweline\NewsSource\Model\AiwelineNewsCategory;
use Aiweline\NewsSource\Model\AiwelineNewsPost;
use Aiweline\NewsSource\Model\AiwelineNewsSource;
use Weline\Framework\App\Exception;

/**
 * 文件信息
 * DESC:   | 新闻服务类
 * 作者：   秋枫雁飞
 * 日期：   2020/7/21
 * 时间：   21:18
 * 网站：   https://bbs.aiweline.com
 * Email：  aiweline@qq.com
 */
class News
{
    /**
     * @DESC         |添加新闻
     *
     * 参数区：
     *
     * @param array $params
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @return array
     */
    public function add(array $params): array
    {
        $result = [
            'id'   => 0,
            'code' => 0,
            'msg'  => 0,
        ];
        // 存来源
        $source = $params['source'];
        if (empty($source)) {
            $result['msg'] = '资源不能为空！';

            return $result;
        }
        // 存分类
        $category = $params['category'];
        if (empty($source)) {
            $result['msg'] = '分类不能为空！';

            return $result;
        }
        $source_model   = new AiwelineNewsSource();
        $source_id      = $source_model->add($source);
        $category_model = new AiwelineNewsCategory();
        $category_id    = $category_model->add($category);

        if ($source_id === 0 || $category_id === 0) {
            $result['msg'] = '资源或分类创建失败！';

            return $result;
        }
        if (empty($params['pushtime']) || ! strtotime($params['pushtime'])) {
            $result['msg'] = '发布时间错误！';

            return $result;
        }
        // 资源
        $news_model = new AiwelineNews();
        $news_data  = [
            'source_id'   => $source_id,
            'category_id' => $category_id,
            'title'       => $params['title'],
            'author'      => $params['author'],
            'abstract'    => $params['abstract'],
            'pushtime'    => $params['pushtime'],
        ];

        try {
            $news_model->add($news_data);
        } catch (Exception $exception) {
            $source_model->where('id', '=', $source_id)->delete();
            $category_model->where('id', '=', $category_id)->delete();
            $result['msg'] = '资源发布失败！';

            return $result;
        }

        // 新闻入库
        $exist_category = $news_model->where('title', '=', $news_data['title'])
            ->where('source_id', '=', $news_data['source_id'])
            ->where('category_id', '=', $news_data['category_id'])
            ->find();
        $news_id = $exist_category['id'] ?? false;
        if (! $news_id) {
            $result['msg'] = '资源发布失败！';

            return $result;
        }
        // post 内容
        try {
            $post_id = (new AiwelineNewsPost())->add([
                'post_id' => $news_id,
                'content' => $params['content'],
            ]);
        } catch (Exception $exception) {
            $source_model->where('id', '=', $source_id)->delete();
            $category_model->where('id', '=', $category_id)->delete();
            $news_model->where('id', '=', $news_id)->delete();
            $result['msg'] = '资源发布失败！';

            return $result;
        }

        if (! $post_id) {
            $result['msg'] = '资源内容发布失败！';

            return $result;
        }
        $result['id']   = $news_id;
        $result['code'] = 1;
        $result['msg']  = '发布成功！';

        return $result;
    }

    /**
     * @DESC         |获取新闻
     *
     * 参数区：
     *
     * @param int $page
     * @param int $pageSize
     * @return \think\Collection
     */
    public function getNews(int $page, int $pageSize)
    {
        $news_mode = new AiwelineNews();
        $start     = ($page - 1) * $pageSize;
        $end       = $page * $pageSize;

        return $news_mode->order('create_time', 'desc')->limit($start, $end)->select();
    }
}
