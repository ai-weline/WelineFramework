/*
Template Name: Weline -  Admin & WelineFramework
Author: 秋枫雁飞(aiweline)
Contact: 秋枫雁飞(aiweline) 1714255949@qq.com
File: Table editable Init Js File
*/

$(function () {
    var pickers = {};
    let table_edit = $('.table-edits tr');
    table_edit.editable({
        /*选择数据*/
        /* dropdowns: {
             source: ['Weline_Backend::system_menu', 'Weline_Backend::system_configuration']
           },*/
        edit: function (values) {
            $(".edit i", this)
                .removeClass('fa-pencil-alt')
                .addClass('fa-save')
                .attr('title', '保存');
        },
        save: function (values) {
            $(".edit i", this)
                .removeClass('fa-save')
                .addClass('fa-pencil-alt')
                .attr('title', '编辑');
            console.log(this)
            if (this in pickers) {
                pickers[this].destroy();
                delete pickers[this];
            }
        },
        cancel: function (values) {
            $(".edit i", this)
                .removeClass('fa-save')
                .addClass('fa-pencil-alt')
                .attr('title', '编辑');

            if (this in pickers) {
                pickers[this].destroy();
                delete pickers[this];
            }
        }
    });
});