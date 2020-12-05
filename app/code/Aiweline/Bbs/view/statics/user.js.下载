var storage = {
    // 检查可用的模式
    test: function () {
        // 本地存储是否可用
        var test = 'test';
        try {
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch (e) {
            return false;
        }
    },
    setItem: function (k, v) {
        localStorage.setItem(k, v)
    }
    // cookies 是否可用
    // 都不可用, 直接提示无法登陆
    // 要存储的数据一般有 身份信息, 状态信息"x, 设置信息? 统计信息
}
var cookie = {
    get: function (cname) {
        var name = cname + "="
        var ca = document.cookie.split(';')
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i].trim()
            if (c.indexOf(name) == 0) return c.substring(name.length, c.length)
        }
        return ""
    },
    sid: function () {
        return this.get("bbs_sid")
    },
    token: function () {
        return this.get("bbs_token")
    }
}
var user = {
    signin: function () {
        var div = document.createElement("div")
        div.id = "signin"
        div.innerHTML = `<ul>
            <li>
                <i class="icon icon-user icon-fw"></i>
                <input type="text" placeholder="Email / 用户名" id="email" name="email">
            </li>
            <li>
                <i class="icon icon-lock icon-fw"></i>
                <input type="password" placeholder="密码" id="password" name="password">
            </li>
            <li>
                <button type="button" >登录</button>
                <a href="?user-create.htm" class="text-muted"><small>用户注册</small></a>
            </li>
        </ul>`
        document.body.appendChild(div)

        // 为登录按钮挂载一个事件监听, 如果已经登录则这个挂载是不必要的
        // 为窗口背景挂载一个关闭按钮区域
        // 当登录成功, 移除登录窗口时, 事件监听是否还存在?
        return div.onclick = function () {
            if (div.style.display == "none") {
                div.style.display = ""
            } else {
                div.style.display = "none"
            }
        }
    },
    signout: function () {
        // 发送退出数据 转变模式到游客
        // 清空本地存储的所有数据, 通常退出的意义
        // 当退出登录时, 重新挂载登录窗口, 也可以始终都不移除它?
        // alert("aoaoaoaoaoao")
    },
    regedit: function () {
        // 游客可以注册, 但通常作为低频数据结构, 它不应放入js
    },
    id: "int",
    token: "string",
    name: "string",
    avact: "string",
    online: false,
    init: function () {
        // 基本数据初始化
        this.online = cookie.token == "" ? false : true

        // 基本状态初始化
        this.online ? this.signout() : this.signin()
    }
}

user.init()
//alert("cacxscasca")
// 所以只在判断未登录状态下才挂载这个面板
// 为页面添加默认隐藏的登录窗口
// signin()

var admin = {
    thread: {
        list: [],
        delete: function(){},
    },
}

// 侧滑选中
window.onload = function () {

    //侧滑显示删除按钮

    var open = null;//open初始化，判断是否是已展开元素
    var list = document.getElementsByClassName("thread");//list获取所有的待展开框
    for (var i = 0; i < list.length; i++) {
        var x, y, X, Y, moveX, moveY;
        list[i].addEventListener('touchstart', function (e) {
            /*获取最初的触摸位置*/
            x = e.changedTouches[0].pageX;
            y = e.changedTouches[0].pageY;
            moveX = true;
            moveY = true;
        });

        list[i].addEventListener('touchmove', function (e) {
            X = e.changedTouches[0].pageX;
            Y = e.changedTouches[0].pageY;

            //左右滑动
            if (moveX && Math.abs(X - x) - Math.abs(Y - y) > 0) {
                e.stopPropagation();//阻止冒泡事件
                //右滑收起删除按钮
                if (X - x > 10) {
                    e.preventDefault();
                    this.classList.remove("moveleft");
                }
                //左滑显示删除按钮
                if (x - X > 10) {
                    e.preventDefault();
                    this.classList.add("moveleft");
                    open = this;//存入展开的li元素
                }
                //moveY = false;//左右滑动时不执行上下滑动时的事件
            }

            //上下滑动
            if (moveY && Math.abs(X - x) - Math.abs(Y - y) < 0) {
                moveX = false;//上下滑动时不执行左右滑动时的事件
            }
        });

        list[i].addEventListener('click', function (e) {
            //在已展开的元素中执行操作
            if (open) {
                var obj = e.target;
                var objli = e.target.closest(".list-li");

                //点击li元素里不是删除按钮的部分，li元素收起
                if (obj.className != "btn") {
                    open.classList.remove("moveleft");
                } else if (obj.className == "btn") {//点击删除按钮执行删除
                    var objp = obj.parentNode;
                    var objpp = obj.parentNode.parentNode;
                    objpp.removeChild(objp);
                }
            }
        });

    }
}