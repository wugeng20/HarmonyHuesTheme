/**
 * @description 后台文章编辑器功能
 * @author 星语社长
 * @version 1.0.7
 */

window.onload = function () {
    $(document).ready(function () {
        // 检查按钮插入区域是否存在
        if ($("#wmd-button-row").length > 0) {
            // 添加一个分割栏
            $('#wmd-more-button').after(`<li class="wmd-spacer wmd-spacer3" id="wmd-spacer5"></li>`
                + `<li class="wmd-spacer wmd-spacer4" id="wmd-spacer6"></li>`
            );

            // 插入自定义按钮
            insertCustomButtons();

            // 绑定按钮点击事件
            bindButtonClickEvents();

            // 绑定确定和取消按钮事件
            bindConfirmCancelEvents();
        }
    });
};

/*--------------1、插入自定义按钮区域--------------*/
function insertCustomButtons() {
    // 文章内容功能
    $('#wmd-spacer5').after(
        '<li class="wmd-button" id="wmd-t-button" title="文字样式自定义"><svg viewBox="0 0 1024 1024" p-id="7281" width="20" height="20"><path d="M153.6 819.2v102.4h716.8v-102.4H153.6z m230.4-215.04h256l46.08 112.64h107.52L550.4 153.6h-76.8L230.4 716.8h107.52l46.08-112.64zM512 254.976L607.744 512H416.256L512 254.976z" fill="#9b9b9b" p-id="7282"></path></svg></li>'
        + '<li class="wmd-button" id="wmd-video-button" title="插入视频"><svg viewBox="0 0 1024 1024" p-id="16735" width="20" height="20"><path d="M910.222336 284.444672H113.77664V170.667008c0-31.419392 25.469952-56.889344 56.889344-56.889344h682.665984c31.419392 0 56.889344 25.469952 56.889344 56.889344v113.77664z m0 56.88832v512c0 31.419392-25.469952 56.889344-56.889344 56.889344H170.667008c-31.419392 0-56.889344-25.469952-56.889344-56.889344v-512H910.22336zM284.444672 170.667008l57.2416 113.77664h56.88832l-57.2416-113.77664h-56.88832z m170.665984 0l57.2416 113.77664H569.2416L512 170.668032h-56.889344z m170.667008 0l57.2416 113.77664h56.88832l-57.2416-113.77664h-56.88832z m6.00576 452.108288a28.444672 28.444672 0 0 0 12.721152-12.721152c7.02464-14.051328 1.330176-31.136768-12.721152-38.162432L439.38816 475.69408a28.444672 28.444672 0 0 0-12.720128-3.003392c-15.710208 0-28.444672 12.735488-28.444672 28.444672v192.396288c0 4.415488 1.028096 8.77056 3.002368 12.720128 7.025664 14.051328 24.112128 19.746816 38.162432 12.721152l192.396288-96.197632z" fill="#9b9b9b" p-id="16736"></path></svg></li>'
        + '<li class="wmd-button" id="wmd-msgpro-button" title="消息提示框"><svg viewBox="0 0 1024 1024" p-id="8277" width="20" height="20"><path d="M606.656155 91.034827c0-50.196774-42.539639-91.034827-95.005193-91.034827S416.716669 40.767154 416.716669 91.034827a87.773454 87.773454 0 0 0 1.276189 15.314269c-131.092986 43.53223-238.718272 178.028387-238.718272 337.268435v125.279236s0 180.509866-46.226407 182.069653a45.588313 45.588313 0 1 0-1.20529 91.034826h759.687046a45.588313 45.588313 0 1 0 0-91.034826c-47.502596 0-47.502596-180.793464-47.502597-180.793464V443.617531c0-159.381846-100.535346-294.019802-238.647372-337.693831a87.348058 87.348058 0 0 0 1.276189-14.888873z m47.431697 796.412933c0 75.436959-63.313162 136.55224-142.43689 136.55224s-142.295091-60.831683-142.43689-136.55224z" fill="#9b9b9b" p-id="8278"></path></svg></li>'
        + '<li class="wmd-button" id="wmd-tolinks-button" title="链接卡片"><svg viewBox="0 0 1024 1024" p-id="14030" width="20" height="20"><path d="M690.346667 938.666667H333.226667C178.005333 938.666667 85.333333 845.824 85.333333 690.346667V333.226667C85.333333 178.005333 178.005333 85.333333 333.226667 85.333333h357.546666a250.24 250.24 0 0 1 182.229334 65.792A249.856 249.856 0 0 1 938.24 333.226667v357.546666c0 155.221333-92.672 247.893333-247.893333 247.893334zM512 401.834667a32.128 32.128 0 0 0-23.296 9.898666L442.026667 460.8a190.421333 190.421333 0 0 0 0 259.797333 172.245333 172.245333 0 0 0 251.733333 0l93.482667-98.133333a190.549333 190.549333 0 0 0 0.426666-259.84A174.933333 174.933333 0 0 0 678.4 308.906667a32.128 32.128 0 0 0-3.754667-0.213334 31.530667 31.530667 0 0 0-31.232 28.842667 32.042667 32.042667 0 0 0 28.586667 34.986667 109.098667 109.098667 0 0 1 69.12 34.133333 125.056 125.056 0 0 1 0 171.477333l-93.482667 98.133334a111.402667 111.402667 0 0 1-159.146666 0 125.098667 125.098667 0 0 1 0-171.52l46.506666-49.066667A31.872 31.872 0 0 0 512 401.834667zM456.106667 249.173333a172.416 172.416 0 0 0-125.866667 54.613334l-93.44 98.133333a190.592 190.592 0 0 0 0 259.84 172.501333 172.501333 0 0 0 109.226667 53.76h2.986666a31.488 31.488 0 0 0 31.573334-28.586667 32 32 0 0 0-7.082667-23.424 31.488 31.488 0 0 0-21.333333-11.562666 109.354667 109.354667 0 0 1-69.12-34.133334 125.184 125.184 0 0 1 0-171.52l93.269333-98.133333a111.786667 111.786667 0 0 1 159.146667 0 125.098667 125.098667 0 0 1 0 171.52l-46.506667 49.066667a32 32 0 0 0 46.506667 43.946666l46.506666-49.024a190.549333 190.549333 0 0 0 0-259.84 171.050667 171.050667 0 0 0-125.866666-54.656z" p-id="14031" fill="#9b9b9b"></path></svg></li>'
        + '<li class="wmd-button" id="wmd-cloud-button" title="网盘下载"><svg viewBox="0 0 1024 1024" p-id="10150" width="20" height="20"><path d="M945.493 610.987C928.426 491.52 819.2 402.774 692.906 433.494c-27.307 6.827-54.613 20.48-78.507 37.547-27.306 20.48-47.786 47.786-61.44 81.92-10.24 27.306-10.24 54.613-17.066 81.92-6.827 23.893-27.307 40.96-51.2 27.306-20.48-13.653-17.067-40.96-13.654-61.44 6.827-44.373 23.894-88.746 47.787-126.293 51.2-71.68 133.12-112.64 218.453-112.64 10.24 0 20.48-10.24 17.067-20.48-23.894-174.08-235.52-262.827-378.88-157.013-34.134 23.894-58.027 58.027-75.094 95.574-6.826 17.066-13.653 37.546-17.066 58.026 0 10.24 0 20.48-3.414 27.307-3.413 20.48 0 6.827-44.373 23.893-75.093 27.307-136.533 92.16-153.6 170.667-34.132 153.599 88.748 293.545 242.348 293.545H645.12c44.373 0 98.987 3.413 143.36-3.413 105.813-23.894 170.667-133.12 157.013-238.933z" fill="#9b9b9b" p-id="10151"></path></svg></li>'
        + '<li class="wmd-button" id="wmd-fold-button" title="折叠框"><svg viewBox="0 0 1024 1024" p-id="8155" width="20" height="20"><path d="M805.52 228.96L684.89 349.52 564.34 228.96l-39.21 39.21L684.66 427.7l0.23-0.31 0.31 0.31 159.53-159.53-39.21-39.21z m1.17 297.03H217.51c-28.71 0-51.99 23.28-51.99 51.99V779c0 28.71 23.28 51.99 51.99 51.99H806.7c28.71 0 51.99-23.28 51.99-51.99V577.98c-0.01-28.71-23.29-51.99-52-51.99z m17.33-402.03c57.42 0 103.97 46.55 103.97 103.97v568.39c0 57.42-46.55 103.97-103.97 103.97H200.18c-57.42 0-103.97-46.55-103.97-103.97V227.93c0-57.42 46.55-103.97 103.97-103.97h623.84z" fill="#9b9b9b" p-id="8156"></path></svg></li>'
    );

    // 独立页面功能页面
    $('#wmd-spacer6').after(
        '<li class="wmd-button" id="wmd-friendshiplink-button" title="友情链接页面专用"><svg viewBox="0 0 1024 1024" p-id="7283" width="20" height="20"><path d="M512 66.56c-246.01088 0-445.44 199.42912-445.44 445.44s199.42912 445.44 445.44 445.44c246.00064 0 445.44-199.43424 445.44-445.44 0-246.01088-199.43424-445.44-445.44-445.44zM453.21216 733.48096c-44.84608 44.8512-117.81632 44.8512-162.69312-0.03072-44.84608-44.84608-44.84608-117.82144 0-162.66752L405.76 455.54176c44.84608-44.83584 117.82144-44.83584 162.69312 0.04096 1.21344 1.20832 2.37568 2.45248 3.52256 3.712a27.47904 27.47904 0 0 1 6.47168 7.7056c0.13824 0.19456 0.29696 0.37888 0.44032 0.56832l-0.08192 0.08192a27.5712 27.5712 0 0 1-37.48864 37.12512c-0.73216 0.75776-7.95136-6.43072-11.79136-10.2656l-0.03072-0.04096c-23.424-23.41376-61.2096-23.60832-84.6336-0.1792l-115.42528 115.42528c-23.424 23.424-23.424 61.39904 0 84.81792l0.03072 0.03072c23.424 23.424 61.39392 23.424 84.81792 0l69.48864-69.48352a89.74848 89.74848 0 0 0 76.82048 1.02912l-107.38176 107.37152z m280.27392-280.26368l-115.24096 115.23584c-44.8512 44.84608-117.81632 44.84608-162.69312-0.03072a104.15104 104.15104 0 0 1-3.52256-3.70688 27.65312 27.65312 0 0 1-6.46656-7.72096c-0.14336-0.19456-0.29696-0.36864-0.44544-0.5632l0.09216-0.08192a27.55072 27.55072 0 0 1 37.48352-37.12c0.72192-0.768 7.95136 6.43072 11.78624 10.27072l0.03072 0.03584c23.424 23.41376 61.2096 23.6032 84.63872 0.1792l115.42528-115.42528c23.424-23.41888 23.424-61.39904 0-84.81792l-0.03584-0.03584c-23.41888-23.41888-61.3888-23.41888-84.81792 0l-69.48352 69.48864c-24.04352-11.76064-52.48-12.2112-76.81536-1.02912l107.38176-107.37664c44.8512-44.84608 117.82144-44.84608 162.69312 0.03072 44.83584 44.83584 44.83584 117.82144-0.01024 162.66752z" fill="#2196F3" p-id="7284"></path></svg></li>'
        + '<li class="wmd-button" id="wmd-equipment-button" title="我的设备页面专用"><svg viewBox="0 0 1024 1024" p-id="11789" width="20" height="20"><path d="M301.056 180.736h421.888c80.384 0 120.32 39.936 120.32 120.32v421.888c0 80.384-39.936 120.32-120.32 120.32H301.056c-80.384 0-120.32-39.936-120.32-120.32V301.056c0-80.384 39.936-120.32 120.32-120.32z" fill="#2196F3" p-id="11790"></path><path d="M421.888 421.888V120.32c0-33.28-27.136-60.416-60.416-60.416H120.32c-33.28 0-60.416 27.136-60.416 60.416v241.152c0 33.28 27.136 60.416 60.416 60.416h301.568zM120.32 0h241.152c66.56 0 120.32 53.76 120.32 120.32v361.472H120.32C53.76 481.792 0 428.032 0 361.472V120.32C0 53.76 53.76 0 120.32 0z m481.792 421.888h301.056c33.28 0 60.416-27.136 60.416-60.416V120.32c0-33.28-27.136-60.416-60.416-60.416h-241.152c-33.28 0-60.416 27.136-60.416 60.416v301.568zM662.528 0h241.152c66.56 0 120.32 53.76 120.32 120.32v241.152c0 66.56-53.76 120.32-120.32 120.32h-361.472V120.32c0-66.56 53.76-120.32 120.32-120.32z m-60.416 602.112v301.056c0 33.28 27.136 60.416 60.416 60.416h241.152c33.28 0 60.416-27.136 60.416-60.416v-241.152c0-33.28-27.136-60.416-60.416-60.416h-301.568v0.512z m-59.904-59.904h361.472c66.56 0 120.32 53.76 120.32 120.32v241.152c0 66.56-53.76 120.32-120.32 120.32h-241.152c-66.56 0-120.32-53.76-120.32-120.32v-361.472z m-120.32 59.904H120.32c-33.28 0-60.416 27.136-60.416 60.416v241.152c0 33.28 27.136 60.416 60.416 60.416h241.152c33.28 0 60.416-27.136 60.416-60.416v-301.568z m-301.568-59.904h361.472v361.472c0 66.56-53.76 120.32-120.32 120.32H120.32C53.76 1024 0 970.24 0 903.68v-241.152c0-66.56 53.76-120.32 120.32-120.32z" fill="#2196F3" p-id="11791"></path></svg></li>'

    );
}

/*--------------2、绑定按钮点击事件区域--------------*/
function bindButtonClickEvents() {
    $(document).on('click', '#wmd-t-button', function () {
        showTextStylePanel();
    });

    $(document).on('click', '#wmd-video-button', function () {
        showVideoPanel();
    });

    $(document).on('click', '#wmd-msgpro-button', function () {
        showMessagePromptPanel();
    });

    $(document).on('click', '#wmd-tolinks-button', function () {
        showLinkCardPanel();
    });

    $(document).on('click', '#wmd-cloud-button', function () {
        showCloudPanel();
    });

    $(document).on('click', '#wmd-fold-button', function () {
        showFoldPanel();
    });

    $(document).on('click', '#wmd-friendshiplink-button', function () {
        showFriendshipLinkPanel();
    });

    $(document).on('click', '#wmd-equipment-button', function () {
        showMeEquipmentPanel();
    })
}

/*--------------3、显示面板区域--------------*/
// 显示文字样式自定义面板
function showTextStylePanel() {
    $('body').append(
        '<div id="TPanel">' +
        '<div class="wmd-prompt-background"></div>' +
        '<div class="wmd-prompt-dialog">' +
        '<p><b>文字样式自定义</b></p>' +
        '<p><label>输入文字颜色</label><input name="color" type="text" placeholder="选填（如 #ffffff 、red）"></p>' +
        '<p><label class="mr-1">是否居中</label><input type="checkbox" id="isCenter"></p>' +
        '<p><label>输入文字内容</label><textarea name="text" type="text" placeholder="请输入内容，支持HTML语法"></textarea></p>' +
        '<form>' +
        '<button type="button" class="btn btn-s primary" id="text_ok">确定</button>' +
        '<button type="button" class="btn btn-s" id="text_cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>'
    );
    $('.wmd-prompt-dialog input')[0].select(); // 聚焦
}

// 显示插入视频面板
function showVideoPanel() {
    $('body').append(
        '<div id="videoPanel">' +
        '<div class="wmd-prompt-background"></div>' +
        '<div class="wmd-prompt-dialog">' +
        '<p><b>插入视频</b></p>' +
        '<p><label>输入视频地址</label><input type="text" name="url" placeholder="必填，不支持云解析（比如：https://xxx.com/xxx.mp4）"></p>' +
        '<p><label>输入视频封面</label><input type="text" name="pic" placeholder="选填，不填则显示默认的背景图片"></p>' +
        '<form>' +
        '<button type="button" class="btn btn-s primary" id="video_ok">确定</button>' +
        '<button type="button" class="btn btn-s" id="video_cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>'
    );
    $('.wmd-prompt-dialog input')[0].select();
}

// 消息提示框
function showMessagePromptPanel() {
    $('body').append(
        '<div id="messagePromptPanel">' +
        '<div class="wmd-prompt-background"></div>' +
        '<div class="wmd-prompt-dialog">' +
        '<div>' +
        '<p><b>插入消息提示框</b></p>' +
        '<p><labe>提示框样式</labe></p>' +
        '<p><select id="MessagePromptList"><option value="info">一般提示[灰色]</option><option value="success">成功提示[绿色]</option><option value="warning">警告提示[黄色]</option><option value="danger">危险提示[红色]</option></select></p>' +
        '<p><label>输入文字内容</label><textarea name="msgtext" type="text" placeholder="请输入内容，支持HTML/Markdown语法"></textarea></p>' +
        '</div>' +
        '<form>' +
        '<button type="button" class="btn btn-s primary" id="mes_prompt_ok">确定</button>' +
        '<button type="button" class="btn btn-s" id="mes_prompt_cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>');
    $('.wmd-prompt-dialog textarea')[0].select();
}

// 链接跳转卡片
function showLinkCardPanel() {
    $('body').append(
        '<div id="linkCardPanel">' +
        '<div class="wmd-prompt-background"></div>' +
        '<div class="wmd-prompt-dialog">' +
        '<p><b>插入链接卡片</b></p>' +
        '<p><label>输入URL名称</label><input type="text" name="title" placeholder="必填，比如：百度"></p>' +
        '<p><label>输入URL地址</label><input type="text" name="url" placeholder="必填，比如：https://www.baidu.com/"></p>' +
        '<p><label>输入Favicon图标</label><input type="text" name="favicon" placeholder="选填，比如：https://www.baidu.com/favicon.ico"></p>' +
        '<form>' +
        '<button type="button" class="btn btn-s primary" id="tolinks_ok">确定</button>' +
        '<button type="button" class="btn btn-s" id="tolinks_cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>'
    );
    $('.wmd-prompt-dialog input')[0].select();
}

// 网盘下载
function showCloudPanel() {
    $('body').append(
        '<div id="cloudPanel">' +
        '<div class="wmd-prompt-background"></div>' +
        '<div class="wmd-prompt-dialog">' +
        '<div>' +
        '<p><b>网盘下载</b></p>' +
        '<p><labe>网盘类型</labe></p>' +
        '<p><select id="cloudTypeList">' +
        '<option value="default">默认网盘</option>' +
        '<option value="baidu">百度网盘</option>' +
        '<option value="quark">夸克网盘</option>' +
        '<option value="aliyun">阿里云网盘</option>' +
        '<option value="lanzou">蓝奏云网盘</option>' +
        '<option value="360">360网盘</option>' +
        '<option value="weiyun">腾讯微云</option>' +
        '<option value="ctfile">城通网盘</option>' +
        '<option value="github">GitHub仓库</option>' +
        '</select></p>' +
        '<p><label>输入标题</label><input type="text" name="title" placeholder="必填，请输入文件标题"></p>' +
        '<p><label>下载地址</label><input type="text" name="url" placeholder="必填，请输入网盘下载地址"></p>' +
        '<p><label>提取密码</label><input type="text" name="password" placeholder="非必填，请输入提取密码"></p>' +
        '</div>' +
        '<form>' +
        '<button type="button" class="btn btn-s primary" id="cloud_ok">确定</button>' +
        '<button type="button" class="btn btn-s" id="cloud_cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>');
    $('.wmd-prompt-dialog input')[0].select();
}

// 折叠框
function showFoldPanel() {
    $('body').append(
        '<div id="foldPanel">' +
        '<div class="wmd-prompt-background"></div>' +
        '<div class="wmd-prompt-dialog">' +
        '<div>' +
        '<p><b>折叠框</b></p>' +
        '<p><label>输入标题</label><input type="text" name="title" placeholder="必填，请输入标题"></p>' +
        '<p><labe>是否折叠</labe></p>' +
        '<p><select id="foldTypeList"><option value="close">是</option><option value="open">否</option></select></p>' +
        '<p><label>输入内容</label><textarea name="foldtext" type="text" placeholder="请输入内容，支持HTML/Markdown语法"></textarea></p>' +
        '</div>' +
        '<form>' +
        '<button type="button" class="btn btn-s primary" id="fold_ok">确定</button>' +
        '<button type="button" class="btn btn-s" id="fold_cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>');
    $('.wmd-prompt-dialog input')[0].select();
}

// 友情链接卡片
function showFriendshipLinkPanel() {
    $('body').append(
        '<div id="friendshipLinkPanel">' +
        '<div class="wmd-prompt-background"></div>' +
        '<div class="wmd-prompt-dialog">' +
        '<p><b>添加友情链接</b></p>' +
        '<p style="color:red;font-size:12px;">提示：友情链接页面专用，其他地方使用无效!!！推荐放在最前面</p>' +
        '<p><label>友链名称*</label><input type="text" name="linkname" placeholder="必填，请输入友链名称"></p>' +
        '<p><label>友链地址*</label><input type="text" name="linkurl" value="http://"></p>' +
        '<p><label>友链头像*</label><input type="text" name="linkavatar" value=""></p>' +
        '<p><label>友链描述</label><textarea type="text" name="linkdesc" placeholder="非必填，请输入友链描述"></textarea></p>' +
        '<p><label>友链分组</label><input type="text" name="linkgroup" placeholder="非必填，请输入友链分组"></p>' +
        '<p><label>友链状态</label><select id="linkStatusList"><option value="正常">正常</option><option value="失联">失联</option><option value="隐藏">隐藏</option></select></p>' +
        '<form>' +
        '<button type="button" class="btn btn-s primary" id="friendship_ok">确定</button>' +
        '<button type="button" class="btn btn-s" id="friendship_cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>'
    );
    $('.wmd-prompt-dialog input')[0].select();
}

// 我的设备页面卡片
function showMeEquipmentPanel() {
    $('body').append(
        '<div id="meEquipmentPanel">' +
        '<div class="wmd-prompt-background"></div>' +
        '<div class="wmd-prompt-dialog">' +
        '<p><b>添加我的设备</b></p>' +
        '<p style="color:red;font-size:12px;">提示：我的设备页面专用，其他地方使用无效!!！推荐放在最前面</p>' +
        '<p><label>我的分类*</label><input type="text" name="deviceCategory" placeholder="必填，例如：生产力、家庭娱乐等等" value="好玩分享"></p>' +
        '<p><label>设备名称*</label><input type="text" name="deviceModel" placeholder="必填，例如：iPhone 13、MacBook Pro等等" value=""></p>' +
        '<p><label>设备图片*</label><input type="text" name="deviceImage" placeholder="必填，例如：https://xxxx/iphone13.jpg"></p>' +
        '<p><label>设备型号</label><input type="text" name="deviceType" placeholder="非必填，例如：手机、电脑等等" value=""></p>' +
        '<p><label>设备描述/评价</label><textarea type="text" name="deviceDescription" placeholder="非必填，例如：iPhone 13屏幕显示效果好等等"></textarea></p>' +
        '<p><label>设备参数</label><input type="text" name="deviceSpecs" placeholder="非必填，例如：白色 / 256G等等" value=""></p>' +
        '<form>' +
        '<button type="button" class="btn btn-s primary" id="equipment_ok">确定</button>' +
        '<button type="button" class="btn btn-s" id="equipment_cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>'
    );
    $('.wmd-prompt-dialog input')[0].select();
}

/*--------------4、绑定确定和取消按钮事件区域--------------*/
function bindConfirmCancelEvents() {
    // 确认事件
    $(document).on('click', '#video_ok', function () {
        handleVideoConfirm();
    });

    $(document).on('click', '#text_ok', function () {
        handleTextStyleConfirm();
    });

    $(document).on('click', '#mes_prompt_ok', function () {
        handleMesPromptConfirm();
    });

    $(document).on('click', '#tolinks_ok', function () {
        handlLinkCardConfirm();
    });

    $(document).on('click', '#cloud_ok', function () {
        handleCloudConfirm();
    });

    $(document).on('click', '#fold_ok', function () {
        handleFoldConfirm();
    });

    $(document).on('click', '#friendship_ok', function () {
        handleFriendshipLinkConfirm();
    })

    $(document).on('click', '#equipment_ok', function () {
        handleMeEquipmentConfirm();
    })

    // 取消事件
    $(document).on('click', '#text_cancel', function () {
        closePanel('#TPanel');
    });

    $(document).on('click', '#video_cancel', function () {
        closePanel('#videoPanel');
    });

    $(document).on('click', '#mes_prompt_cancel', function () {
        closePanel('#messagePromptPanel');
    });

    $(document).on('click', '#tolinks_cancel', function () {
        closePanel('#linkCardPanel');
    });

    $(document).on('click', '#cloud_cancel', function () {
        closePanel('#cloudPanel');
    });

    $(document).on('click', '#fold_cancel', function () {
        closePanel('#foldPanel');
    });

    $(document).on('click', '#friendship_cancel', function () {
        closePanel('#friendshipLinkPanel');
    });

    $(document).on('click', '#equipment_cancel', function () {
        closePanel('#meEquipmentPanel');
    })
}

// 处理视频确认事件
function handleVideoConfirm() {
    const myField = document.getElementById('text');
    const textContent = $('.wmd-prompt-dialog input[name="url"]').val();
    const pic = $('.wmd-prompt-dialog input[name="pic"]').val();
    const content = `[player url="${textContent}" pic="${pic}" /]`;
    insertContentToTextArea(myField, content, '#videoPanel');
}

// 处理文字样式确认事件
function handleTextStyleConfirm() {
    const myField = document.getElementById('text');
    const content = $('.wmd-prompt-dialog textarea[name="text"]').val();
    const color = $('.wmd-prompt-dialog input[name="color"]').val();
    const isCenter = $('#isCenter').is(':checked');
    const colorStyle = color ? ` style="color:${color}"` : '';
    const insertContent = isCenter ? `<p class="center"${colorStyle}>${content}</p>` : `<p${colorStyle}>${content}</p>`;
    insertContentToTextArea(myField, insertContent, '#TPanel');
}

// 处理消息提示框确认事件
function handleMesPromptConfirm() {
    const myField = document.getElementById('text');
    const textContent = $('.wmd-prompt-dialog textarea[name="msgtext"]').val();
    const type = $('#MessagePromptList').val();
    const content = `[${type}]${textContent}[/${type}]`;
    insertContentToTextArea(myField, content, '#messagePromptPanel');
}

// 处理链接卡片确认事件
function handlLinkCardConfirm() {
    const myField = document.getElementById('text');
    const title = $('.wmd-prompt-dialog input[name="title"]').val();
    const url = $('.wmd-prompt-dialog input[name="url"]').val();
    const favicon = $('.wmd-prompt-dialog input[name="favicon"]').val();
    const content = `[tolink title="${title}" url="${url}" favicon="${favicon}" /]`;
    insertContentToTextArea(myField, content, '#linkCardPanel');
}

// 处理网盘下载确认事件
function handleCloudConfirm() {
    const myField = document.getElementById('text');
    const type = $('#cloudTypeList').val();
    const title = $('.wmd-prompt-dialog input[name="title"]').val();
    const url = $('.wmd-prompt-dialog input[name="url"]').val();
    const password = $('.wmd-prompt-dialog input[name="password"]').val();
    const content = `[cloud type="${type}" title="${title}" url="${url}" password="${password}" /]`;
    insertContentToTextArea(myField, content, '#cloudPanel');
}

// 处理折叠框事件
function handleFoldConfirm() {
    const myField = document.getElementById('text');
    const title = $('.wmd-prompt-dialog input[name="title"]').val();
    const type = $('#foldTypeList').val();
    const textContent = $('.wmd-prompt-dialog textarea[name="foldtext"]').val();
    const content = `[fold title="${title}" type="${type}"]${textContent}[/fold]`;
    insertContentToTextArea(myField, content, '#foldPanel');
}

// 处理友情链接卡事件
function handleFriendshipLinkConfirm() {
    const myField = document.getElementById('text');
    const title = $('.wmd-prompt-dialog input[name="linkname"]').val();
    const url = $('.wmd-prompt-dialog input[name="linkurl"]').val();
    const avatar = $('.wmd-prompt-dialog input[name="linkavatar"]').val();
    const desc = $('.wmd-prompt-dialog textarea[name="linkdesc"]').val();
    const group = $('.wmd-prompt-dialog input[name="linkgroup"]').val();
    const status = $('#linkStatusList').val();
    const content = `[Links title="${title}" url="${url}" avatar="${avatar}" desc="${desc}" group="${group}" status="${status}" /]`;
    insertContentToTextArea(myField, content, '#friendshipLinkPanel');
}

// 处理我的设备事件
function handleMeEquipmentConfirm() {
    const myField = document.getElementById('text');
    // *category
    const category = $('.wmd-prompt-dialog input[name="deviceCategory"]').val();
    // *model
    const model = $('.wmd-prompt-dialog input[name="deviceModel"]').val();
    // *image
    const image = $('.wmd-prompt-dialog input[name="deviceImage"]').val();
    // type
    const type = $('.wmd-prompt-dialog input[name="deviceType"]').val();
    // specs
    const specs = $('.wmd-prompt-dialog input[name="deviceSpecs"]').val();
    // description
    const description = $('.wmd-prompt-dialog textarea[name="deviceDescription"]').val();
    const content = `[Equipment category="${category}" model="${model}" image="${image}" type="${type}" specs="${specs}" description="${description}" /]`;
    insertContentToTextArea(myField, content, '#meEquipmentPanel');
}

// 关闭面板
function closePanel(panelId) {
    $(panelId).remove();
    $('#wmd-editarea>textarea').focus();
}

/*--------------插入内容到编辑器--------------*/
function insertContentToTextArea(textarea, content, modelId) {
    $(modelId).remove();

    if (document.selection) {
        textarea.focus();
        const sel = document.selection.createRange();
        sel.text = content;
        textarea.focus();
    } else if (textarea.selectionStart || textarea.selectionStart === '0') {
        const startPos = textarea.selectionStart;
        const endPos = textarea.selectionEnd;
        const cursorPos = startPos + content.length;

        textarea.value = textarea.value.substring(0, startPos) + content + textarea.value.substring(endPos, textarea.value.length);

        textarea.selectionStart = cursorPos;
        textarea.selectionEnd = cursorPos;
        textarea.focus();
    } else {
        textarea.value += content;
        textarea.focus();
    }
}


