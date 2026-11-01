/* ------------------------------------
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2026-10-29 14:05:00
 * --------------------------------- */
$(document).ready(function () {
  // 表情包数据：由 PHP 扫描 assets/emoji 目录后注入（core/function.php:getEmojiPacks）
  // 每个子目录 = 一个表情包；目录内的 info.json 可自定义标签、插入方式和中文名
  const packs = Array.isArray(window.HH_EMOJI_PACKS) ? window.HH_EMOJI_PACKS : [];

  // 获取 DOM 元素
  const emojiBox = $('.emoji-box');
  const emojiBtn = $('#emoji-btn');
  const textarea = $('#textarea')[0];

  // 没有可用表情包时直接隐藏入口，避免点了没反应
  if (!packs.length || !emojiBtn.length) {
    $('.com-emoji').hide();
    return;
  }

  // 生成表情包分类和列表
  const generateEmojis = () => {
    const emojiBar = $('<ul class="emoji-bar"></ul>');
    const emojiLists = [];

    // 遍历表情包数据
    packs.forEach((pack, index) => {
      const label = pack.label || pack.name;
      const mode = pack.mode === 'image' ? 'image' : 'emoji';

      // 创建表情包分类标题
      emojiBar.append(
        $('<li class="d-inline-block p-2"></li>')
          .attr('data-type', label)
          .attr('data-index', index)
          .text(label)
      );

      // 创建表情包列表
      const emojiList = $('<ul class="emoji-dropdown scroll-cover p-2"></ul>').attr('data-type', label);

      (pack.items || []).forEach((item) => {
        const url = (pack.path || '') + item.file;
        const title = item.title || item.key;
        const $item = $('<li class="emoji-item p-2"></li>')
          .attr('data-type', label)
          .attr('data-mode', mode)
          .attr('data-text', mode === 'image' ? '' : item.key)
          .attr('data-url', url);

        $('<img class="lazy">')
          .addClass(mode === 'image' ? 'gif-img' : '')
          .attr({
            'data-original': url,
            alt: title,
            title: title
          })
          .appendTo($item);

        emojiList.append($item);
      });

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
    $('.emoji-item img.lazy').lazyload({
      effect: 'fadeIn',
      threshold: 200,
      load: function () {
        // 图片加载完成后添加 loaded 类
        $(this).addClass('loaded');
      }
    });

    // 手动触发 LazyLoad 的加载逻辑
    $('.emoji-item img.lazy').trigger('appear');
  });

  // 监听表情点击事件
  emojiBox.on('click', '.emoji-item', function (e) {
    e.stopPropagation();
    const mode = $(this).attr('data-mode'); // emoji：插入行内表情代码，image：插入图片
    const emojiText = $(this).attr('data-text'); // 获取表情文本
    const emojiUrl = $(this).attr('data-url'); // 获取表情图片地址
    const emojiName = $(this).find('img').attr('alt') || ''; // 获取表情名称

    // image 模式适合动图/大表情，作为 Markdown 图片插入
    const content =
      mode === 'image'
        ? `![${emojiName}](${emojiUrl})`
        : `:(owo=${emojiText})`;

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
