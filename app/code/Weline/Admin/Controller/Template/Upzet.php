<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller\Template;

class Upzet extends BaseController
{
    function index()
    {
        return $this->fetch();
    }

    function calendar()
    {
        return $this->fetch();
    }

    function emailInbox()
    {
        return $this->fetch();
    }

    function emailRead()
    {
        return $this->fetch();
    }

    function emailCompose()
    {
        return $this->fetch();
    }

    function layoutsLightSidebar()
    {
        return $this->fetch('layouts-light-sidebar');
    }

    function layoutsCompactSidebar()
    {
        return $this->fetch('layouts-compact-sidebar');
    }

    function layoutsIconSidebar()
    {
        return $this->fetch('layouts-icon-sidebar');
    }

    function layoutsBoxed()
    {
        return $this->fetch('layouts-boxed');
    }
    function layoutshorizontal()
    {
        return $this->fetch('layouts-horizontal');
    }
    function authlockscreen()
    {
        return $this->fetch('auth-lock-screen');
    }
    function authlogin()
    {
        return $this->fetch('auth-login');
    }
    function authrecoverpw()
    {
        return $this->fetch('auth-recoverpw');
    }
    function authregister()
    {
        return $this->fetch('auth-register');
    }
    function chartsapex()
    {
        return $this->fetch('charts-apex');
    }
    function chartschartjs()
    {
        return $this->fetch('charts-chartjs');
    }
    function chartsflot()
    {
        return $this->fetch('charts-flot');
    }
    function chartsknob()
    {
        return $this->fetch('charts-knob');
    }
    function chartssparkline()
    {
        return $this->fetch('charts-sparkline');
    }
    function formadvanced()
    {
        return $this->fetch('form-advanced');
    }
    function formeditors()
    {
        return $this->fetch('form-editors');
    }
    function formelements()
    {
        return $this->fetch('form-elements');
    }
    function formmask()
    {
        return $this->fetch('form-mask');
    }
    function formuploads()
    {
        return $this->fetch('form-uploads');
    }
    function formvalidation()
    {
        return $this->fetch('form-validation');
    }
    function formwizard()
    {
        return $this->fetch('form-wizard');
    }
    function formxeditable()
    {
        return $this->fetch('form-xeditable');
    }
    function iconsdripicons()
    {
        return $this->fetch('icons-dripicons');
    }
    function iconsfontawesome()
    {
        return $this->fetch('icons-fontawesome');
    }
    function iconsmaterialdesign()
    {
        return $this->fetch('icons-materialdesign');
    }
    function iconsremix()
    {
        return $this->fetch('icons-remix');
    }
    function mapsgoogle()
    {
        return $this->fetch('maps-google');
    }
    function mapsvector()
    {
        return $this->fetch('maps-vector');
    }
    function pages404()
    {
        return $this->fetch('pages-404');
    }
    function pages500()
    {
        return $this->fetch('pages-500');
    }
    function pagescomingsoon()
    {
        return $this->fetch('pages-comingsoon');
    }
    function pagesfaqs()
    {
        return $this->fetch('pages-faqs');
    }
    function pagesmaintenance()
    {
        return $this->fetch('pages-maintenance');
    }
    function pagespricing()
    {
        return $this->fetch('pages-pricing');
    }
    function pagesstarter()
    {
        return $this->fetch('pages-starter');
    }
    function pagestimeline()
    {
        return $this->fetch('pages-timeline');
    }
    function tablesbasic()
    {
        return $this->fetch('tables-basic');
    }
    function tablesdatatable()
    {
        return $this->fetch('tables-datatable');
    }
    function tableseditable()
    {
        return $this->fetch('tables-editable');
    }
    function tablesresponsive()
    {
        return $this->fetch('tables-responsive');
    }
    function uialerts()
    {
        return $this->fetch('ui-alerts');
    }
    function uibadge()
    {
        return $this->fetch('ui-badge');
    }
    function uibuttons()
    {
        return $this->fetch('ui-buttons');
    }
    function uicards()
    {
        return $this->fetch('ui-cards');
    }
    function uicarousel()
    {
        return $this->fetch('ui-carousel');
    }
    function uidropdowns()
    {
        return $this->fetch('ui-dropdowns');
    }
    function uigrid()
    {
        return $this->fetch('ui-grid');
    }
    function uiimages()
    {
        return $this->fetch('ui-images');
    }
    function uilightbox()
    {
        return $this->fetch('ui-lightbox');
    }
    function uimodals()
    {
        return $this->fetch('ui-modals');
    }
    function uioffcanvas()
    {
        return $this->fetch('ui-offcanvas');
    }
    function uipagination()
    {
        return $this->fetch('ui-pagination');
    }
    function uipopovertooltips()
    {
        return $this->fetch('ui-popover-tooltips');
    }
    function uiprogressbars()
    {
        return $this->fetch('ui-progressbars');
    }
    function uirangeslider()
    {
        return $this->fetch('ui-rangeslider');
    }
    function uirating()
    {
        return $this->fetch('ui-rating');
    }
    function uisessiontimeout()
    {
        return $this->fetch('ui-session-timeout');
    }
    function uisweetalert()
    {
        return $this->fetch('ui-sweet-alert');
    }
    function uitabsaccordions()
    {
        return $this->fetch('ui-tabs-accordions');
    }
    function uitypography()
    {
        return $this->fetch('ui-typography');
    }
    function uivideo()
    {
        return $this->fetch('ui-video');
    }
}