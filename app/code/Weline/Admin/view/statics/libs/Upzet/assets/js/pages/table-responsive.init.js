/*
Template Name: Weline -  Admin & WelineFramework
Author: Themesdesign
Contact: themesdesign.in@gmail.com
File: Table responsive Init Js File
*/

$(function() {
    $('.table-responsive').responsiveTable({
        addDisplayAllBtn: 'btn btn-secondary'
    });

    $('.btn-toolbar [data-toggle=dropdown]').attr('data-bs-toggle', "dropdown");
});