requirejs.config({
    //要在IE中及时获得正确的错误触发器，请强制进行定义/填充导出检查。
    enforceDefine: true,
    paths: {
        bootstrapBundle: [
            // CDN 加载jquery 如果是全球站点可考虑配置
            //'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min',
            //如果CDN位置失败，请从此位置加载jquery
            'Weline_Admin/backend/js/bootstrap.bundle.min',
            'Weline_Admin/backend/js/bootstrap.bundle.min.js',
        ],
        iotFunctions: 'Weline_Admin/backend/js/iot-functions.min',
        iotTimer: 'Weline_Admin/backend/js/iot-timer.min',
        svg4everybody: 'Weline_Admin/backend/js/svg4everybody.min'
    }
});