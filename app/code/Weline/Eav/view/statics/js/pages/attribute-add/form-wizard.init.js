/*
Template Name: Weline -  Admin & WelineFramework
Author: 秋枫雁飞(aiweline)
Contact: 秋枫雁飞(aiweline) 1714255949@qq.com
File: Form wizard Js File
*/

$(document).ready(function () {
    $('#basic-pills-wizard').bootstrapWizard({
        'tabClass': 'nav nav-pills nav-justified'
    });

    $('#progress-wizard').bootstrapWizard({
        onInit: function (tab, navigation, index) {
            // var triggerTabList = [].slice.call(document.querySelectorAll('.twitter-bs-wizard-nav .nav-link'))
            // triggerTabList.forEach(function (triggerEl) {
            //     var tabTrigger = new bootstrap.Tab(triggerEl)
            //     triggerEl.addEventListener('click', function (event) {
            //         event.preventDefault()
            //         tabTrigger.show()
            //     })
            // })
        },
        onTabShow: function (tab, navigation, index) {
            var $total = navigation.find('li').length;
            var $current = index + 1;
            var $percent = ($current / $total) * 100;
            $('#progress-wizard').find('.progress-bar').css({width: $percent + '%'});
        },
        onNext: function (tab, navigation, index) {
            let tab_id = $(tab.find('a').get(0)).attr('href')
            let form = $(tab_id).find('form')
            let validate_status = form.get(0).reportValidity()
            if (validate_status) {
                if (form.find('input[name="selected"]').val() === '1') {
                    return true;
                }
                form.submit();
            } else {
                return false;
            }
        },
        onTabClick: function (activeTab, navigation, currentIndex, nextIndex) {
        }
    });

});

// Active tab pane on nav link

var triggerTabList = [].slice.call(document.querySelectorAll('.twitter-bs-wizard-nav .nav-link'))
triggerTabList.forEach(function (triggerEl) {
    var tabTrigger = new bootstrap.Tab(triggerEl)
    triggerEl.addEventListener('click', function (event) {
        event.preventDefault()
        tabTrigger.show()
    })
})



