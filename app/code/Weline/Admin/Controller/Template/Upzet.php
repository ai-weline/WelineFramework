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
    public function index()
    {
        return $this->fetch();
    }

    public function calendar()
    {
        return $this->fetch();
    }

    public function emailInbox()
    {
        return $this->fetch();
    }

    public function emailRead()
    {
        return $this->fetch();
    }

    public function emailCompose()
    {
        return $this->fetch();
    }

    public function layoutsLightSidebar()
    {
        return $this->fetch('layouts-light-sidebar');
    }

    public function layoutsCompactSidebar()
    {
        return $this->fetch('layouts-compact-sidebar');
    }

    public function layoutsIconSidebar()
    {
        return $this->fetch('layouts-icon-sidebar');
    }

    public function layoutsBoxed()
    {
        return $this->fetch('layouts-boxed');
    }
    public function layoutshorizontal()
    {
        return $this->fetch('layouts-horizontal');
    }
    public function authlockscreen()
    {
        return $this->fetch('auth-lock-screen');
    }
    public function authlogin()
    {
        return $this->fetch('auth-login');
    }
    public function authrecoverpw()
    {
        return $this->fetch('auth-recoverpw');
    }
    public function authregister()
    {
        return $this->fetch('auth-register');
    }
    public function chartsapex()
    {
        return $this->fetch('charts-apex');
    }
    public function chartschartjs()
    {
        return $this->fetch('charts-chartjs');
    }
    public function chartsflot()
    {
        return $this->fetch('charts-flot');
    }
    public function chartsknob()
    {
        return $this->fetch('charts-knob');
    }
    public function chartssparkline()
    {
        return $this->fetch('charts-sparkline');
    }
    public function formadvanced()
    {
        return $this->fetch('form-advanced');
    }
    public function formeditors()
    {
        return $this->fetch('form-editors');
    }
    public function formelements()
    {
        return $this->fetch('form-elements');
    }
    public function formmask()
    {
        return $this->fetch('form-mask');
    }
    public function formuploads()
    {
        return $this->fetch('form-uploads');
    }
    public function formvalidation()
    {
        return $this->fetch('form-validation');
    }
    public function formwizard()
    {
        return $this->fetch('form-wizard');
    }
    public function formxeditable()
    {
        return $this->fetch('form-xeditable');
    }
    public function iconsdripicons()
    {
        return $this->fetch('icons-dripicons');
    }
    public function iconsfontawesome()
    {
        return $this->fetch('icons-fontawesome');
    }
    public function iconsmaterialdesign()
    {
        return $this->fetch('icons-materialdesign');
    }
    public function iconsremix()
    {
        return $this->fetch('icons-remix');
    }
    public function mapsgoogle()
    {
        return $this->fetch('maps-google');
    }
    public function mapsvector()
    {
        return $this->fetch('maps-vector');
    }
    public function pages404()
    {
        return $this->fetch('pages-404');
    }
    public function pages500()
    {
        return $this->fetch('pages-500');
    }
    public function pagescomingsoon()
    {
        return $this->fetch('pages-comingsoon');
    }
    public function pagesfaqs()
    {
        return $this->fetch('pages-faqs');
    }
    public function pagesmaintenance()
    {
        return $this->fetch('pages-maintenance');
    }
    public function pagespricing()
    {
        return $this->fetch('pages-pricing');
    }
    public function pagesstarter()
    {
        return $this->fetch('pages-starter');
    }
    public function pagestimeline()
    {
        return $this->fetch('pages-timeline');
    }
    public function tablesbasic()
    {
        return $this->fetch('tables-basic');
    }
    public function tablesdatatable()
    {
        return $this->fetch('tables-datatable');
    }
    public function tableseditable()
    {
        return $this->fetch('tables-editable');
    }
    public function tablesresponsive()
    {
        return $this->fetch('tables-responsive');
    }
    public function uialerts()
    {
        return $this->fetch('ui-alerts');
    }
    public function uibadge()
    {
        return $this->fetch('ui-badge');
    }
    public function uibuttons()
    {
        return $this->fetch('ui-buttons');
    }
    public function uicards()
    {
        return $this->fetch('ui-cards');
    }
    public function uicarousel()
    {
        return $this->fetch('ui-carousel');
    }
    public function uidropdowns()
    {
        return $this->fetch('ui-dropdowns');
    }
    public function uigrid()
    {
        return $this->fetch('ui-grid');
    }
    public function uiimages()
    {
        return $this->fetch('ui-images');
    }
    public function uilightbox()
    {
        return $this->fetch('ui-lightbox');
    }
    public function uimodals()
    {
        return $this->fetch('ui-modals');
    }
    public function uioffcanvas()
    {
        return $this->fetch('ui-offcanvas');
    }
    public function uipagination()
    {
        return $this->fetch('ui-pagination');
    }
    public function uipopovertooltips()
    {
        return $this->fetch('ui-popover-tooltips');
    }
    public function uiprogressbars()
    {
        return $this->fetch('ui-progressbars');
    }
    public function uirangeslider()
    {
        return $this->fetch('ui-rangeslider');
    }
    public function uirating()
    {
        return $this->fetch('ui-rating');
    }
    public function uisessiontimeout()
    {
        return $this->fetch('ui-session-timeout');
    }
    public function uisweetalert()
    {
        return $this->fetch('ui-sweet-alert');
    }
    public function uitabsaccordions()
    {
        return $this->fetch('ui-tabs-accordions');
    }
    public function uitypography()
    {
        return $this->fetch('ui-typography');
    }
    public function uivideo()
    {
        return $this->fetch('ui-video');
    }
}
