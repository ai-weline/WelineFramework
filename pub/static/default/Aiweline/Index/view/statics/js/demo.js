console.log('我是JS输出！')

function openWindow(src, title) {
    let iWidth = 1200; //弹出窗口的宽度;
    let iHeight = 800; //弹出窗口的高度;
    let iTop = (window.screen.availHeight - 30 - iHeight) / 2; //获得窗口的垂直位置;
    let iLeft = (window.screen.availWidth - 10 - iWidth) / 2; //获得窗口的水平位置;
    window.open(src, title, "height=" + iHeight + ", width=" + iWidth + ", " +
        "top=" + iTop + ", left=" + iLeft ,"resizable:no","scrollbars:yes");
}