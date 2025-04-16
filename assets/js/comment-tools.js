/* ------------------------------------
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-12-20 22:16:26
 * --------------------------------- */
$(document).ready(function () {
  // 表情包数据
  const emojis = [
    {
      name: 'Heo',
      path: '/usr/themes/HarmonyHues/assets/emoji/Heo',
      list: { '3dyanjing': '3D眼镜', 'lpl': 'LPL加油', 'aoye': '熬夜', 'baozha': '爆炸', 'buhaoyisi': '不好意思', 'qinqin': '亲亲', 'sanbing': '伞兵', 'yiqiangxiao': '倚墙笑', 'toutoukan': '偷偷看', 'zaijian': '再见', 'chujiaren': '出家人', 'jiaban': '加班', 'mianqiangxiao': '勉强笑', 'weixian': '危险', 'fahongbao': '发红包', 'chishou': '吃手', 'chigua': '吃瓜', 'tuxie': '吐血', 'chaojia': '吵架', 'youhou': '呦吼', 'ziyaxiao': '呲牙笑', 'hashiqi': '哈士奇', 'hashiqishiquyishi': '哈士 奇失去意识', 'hashiqishiwang': '哈士奇失望', 'kuqi': '哭泣', 'changge': '唱歌', 'xihuan': '喜欢', 'heiha': '嘿哈', 'daxiao': '大笑', 'shiwang': '失望', 'toutu': '头秃', 'fendou': '奋斗', 'haoqi': '好奇', 'haode': '好的', 'haixiu': '害羞', 'xiaochou': '小丑', 'xiaotou': '小偷', 'ganga': '尴尬', 'yingyuan': '应援', 'kaixin': '开心', 'yinqibushi': '引起不适', 'weixiao': '微笑', 'sikao': '思考', 'exin': '恶心', 'jingxia': '惊吓', 'jingya': '惊讶', 'gandong': '感动', 'fennu': '愤怒', 'wokanhaoni': '我看好你', 'shoujixiangji': '手机相机', 'damie': '打咩', 'dapai': '打牌', 'tuosai': '托腮', 'fue': '扶额', 'koubi': '抠鼻', 'taiyanjing': '抬眼镜', 'wuzuixiao': '捂嘴笑', 'wulian': '捂脸', 'cahan': '擦汗', 'doujiyan': '斗鸡眼', 'zhihuideyanshen': '智慧的眼神', 'yuebing': '月饼', 'youmeiyougaocuo': '有没有搞错', 'leiben': '泪奔', 'shensi': '深思', 'huaji': '滑稽', 'huajiheshui': '滑稽喝水', 'huajinaicha': '滑稽奶茶', 'huajiningmeng': '滑稽柠檬', 'huajikuanghan': '滑稽狂汗', 'huajibeizi': '滑稽被子', 'fannao': '烦恼', 'xiongmao': '熊猫', 'xiongmaochangge': '熊猫唱歌', 'xiongmaoxihuan': '熊猫喜欢', 'xiongmaoshiwang': '熊猫失望', 'niunianjinbao': '牛年进宝', 'goutou': '狗头', 'goutouweibo': '狗头围脖', 'goutoushiwang': '狗头失望', 'goutoupangci': '狗头胖次', 'goutouhua': '狗头花', 'goutoucao': '狗头草', 'zhutou': '猪头', 'shengbing': '生病', 'dianhua': '电话', 'yiwen': '疑问', 'tengtong': '疼痛', 'kanchuanyiqie': '看穿 一切', 'xuanyun': '眩晕', 'shuijiao': '睡觉', 'jinyan': '禁言', 'xiaoku': '笑哭', 'jiujie': '纠结', 'lvmao': '绿帽', 'shuaku': '耍酷', 'huzi': '胡子', 'caigou': '菜狗', 'caigouhua': '菜狗花', 'beida': '被打', 'liekai': '裂开', 'songfu': '送福', 'songhua': '送花', 'yinxian': '阴险', 'nanyizhixin': '难以置信', 'guilian': '鬼脸', 'heixian': '黑线', 'guzhang': '鼓掌' }
    },
    {
      name: 'GIF',
      path: '/usr/themes/HarmonyHues/assets/emoji/GIF',
      list: { 'gif_1': '熊猫头流泪表情包', 'gif_2': '向话吗', 'gif_3': "又关我什么事", 'gif_4': "很高兴为你服务", 'gif_5': "你不准玩微信", 'gif_6': "好了，孩子们", 'gif_7': "你有点蔡徐坤", 'gif_8': "蔡徐坤的肯定", 'gif_9': "这人真棒", 'gif_10': "蔡徐坤偷听", 'gif_11': "du瘾发作", 'gif_12': "甄子丹的嘲笑", 'gif_13': "原神启动", 'gif_14': "鲲之蔑视" }
    }
  ];

  // 获取 DOM 元素
  const emojiBox = $('.emoji-box');
  const emojiBtn = $('#emoji-btn');
  const textarea = $('#textarea')[0];

  // 生成表情包分类和列表
  const generateEmojis = () => {
    const emojiBar = $('<ul class="emoji-bar"></ul>');
    const emojiLists = [];

    // 遍历表情包数据
    emojis.forEach((category, index) => {
      const { name, path, list } = category;

      // 创建表情包分类标题
      emojiBar.append(`<li class="d-inline-block p-2" data-type="${name}" data-index="${index}">${name}</li>`);

      // 创建表情包列表
      const emojiList = $('<ul class="emoji-dropdown scroll-cover p-2"></ul>').attr('data-type', name);

      // 生成表情包项
      const emojiItems = Object.entries(list).map(([emojiKey, emojiName]) => {
        if (name === 'GIF') {
          return `<li class="emoji-item p-2" data-text="" data-type="${name}"><img class="gif-img lazy" data-original="${path}/${emojiKey}.webp" alt="${emojiName}" title="${emojiName}"></li>`;
        }

        return `<li class="emoji-item p-2" data-text="${emojiKey}" data-type="${name}"><img class="lazy" data-original="${path}/${emojiKey}.webp" alt="${emojiName}" title="${emojiName}"></li>`;
      });

      // 将表情包项添加到列表
      emojiList.append(emojiItems.join(''));
      emojiLists.push(emojiList);
    });

    // 将分类标题和列表添加到容器
    emojiBox.append(emojiLists).append(emojiBar);

    // 默认显示第一个分类的表情包列表
    emojiBox.find('.emoji-dropdown').hide().first().show();
  };

  // 插入文本到光标位置
  const insertTextAtCursor = (textarea, text) => {
    const startPos = textarea.selectionStart;
    const endPos = textarea.selectionEnd;
    const textBefore = textarea.value.substring(0, startPos);
    const textAfter = textarea.value.substring(endPos, textarea.value.length);
    const insertedText = text; // 插入的文本 
    textarea.value = textBefore + insertedText + textAfter;
    textarea.selectionStart = textarea.selectionEnd = startPos + insertedText.length;
    $(textarea).trigger('change');
    textarea.focus();
  };

  // 监听表情按钮点击事件
  emojiBtn.on('click', function (e) {
    e.stopPropagation();
    // 检测是否存在表情包内容
    if (emojiBox.find('.emoji-bar').length === 0) {
      generateEmojis(); // 如果不存在，生成表情包内容
      emojiBox.find('.emoji-bar > li').first().addClass('emoji-active');
    }
    $(this).parent().toggleClass('active');
    $(this).next('.emoji-box').fadeToggle();

    // 重新初始化 LazyLoad
    $(".emoji-item img.lazy").lazyload({
      effect: "fadeIn",
      threshold: 200,
      load: function () {
        // 图片加载完成后添加 loaded 类
        $(this).addClass("loaded");
      }
    });

    // 手动触发 LazyLoad 的加载逻辑
    $(".emoji-item img.lazy").trigger("appear");
  });

  // 监听表情点击事件
  emojiBox.on('click', '.emoji-item', function (e) {
    e.stopPropagation();
    const emojiText = $(this).attr('data-text'); // 获取表情文本
    const emojiType = $(this).attr('data-type'); // 获取表情包分类
    let content = `:(owo=${emojiText})`; // 表情包文本格式
    if (emojiType === 'GIF') {
      const emojiName = $(this).find('img').attr('alt'); // 获取GIF表情名称
      const gifUrl = $(this).find('img').attr('src');
      content = `![${emojiName}](${gifUrl})`
    }
    insertTextAtCursor(textarea, content); // 插入文本
    emojiBtn.parent().removeClass('active');
    $(this).closest('.emoji-box').fadeOut();
  });

  // 监听分类标题点击事件，切换表情包列表
  emojiBox.on('click', '.emoji-bar li', function () {
    const type = $(this).data('type');
    emojiBox.find('.emoji-dropdown').hide(); // 隐藏所有表情包列表
    emojiBox.find(`.emoji-dropdown[data-type="${type}"]`).show(); // 显示当前分类的表情包列表
    $(this).addClass('emoji-active').siblings().removeClass('emoji-active'); // 高亮当前分类标题
  });

  // 监听文档点击事件，隐藏表情框
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.emoji-box').length && !$(e.target).is('#emoji-btn')) {
      $('.emoji-box').fadeOut();
      emojiBtn.parent().removeClass('active');
    }
  });
});