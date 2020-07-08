<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/27
 * 时间：12:24
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Controller\Data;


interface DataInterface
{
    const dir='Controller';

    const type_pc_FRONTEND='FrontendController';
    const type_pc_BACKEND='BackendController';
    const type_api_REST_FRONTEND='FrontendRestController';
    const type_api_REST_BACKEND='BackendRestController';
}