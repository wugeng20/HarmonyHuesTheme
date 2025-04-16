/* ------------------------------------
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
$(document).ready(function () {
  // 获取所有标题的锚点元素
  var headings = $('[id^="cl-"]');
  // 获取所有目录项
  var catalogItems = $('.atoc-list a');

  // 设置 active 类的函数
  setActiveCatalogItem = () => {
    var scrollPosition = $(window).scrollTop() + 100; // 当前滚动位置 + 100px 偏移

    // 遍历所有标题锚点
    headings.each(function () {
      var heading = $(this);
      var headingId = heading.attr('id'); // 获取标题的 ID
      var catalogItem = $('a[data-target="' + headingId + '"]'); // 获取对应的目录项

      // 检查当前标题是否在视口中
      if (heading.offset().top <= scrollPosition) {
        // 移除所有目录项的 active 类
        catalogItems.removeClass('active');
        // 为当前目录项添加 active 类
        catalogItem.addClass('active');
      }
    });
  };

  setActiveCatalogItem(); // 初始化时设置 active 类

  // 监听页面滚动事件
  $(window).scroll(() => {
    setActiveCatalogItem();
  });

  // 不在文章视口消失
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      const $toc = $('.widget-toc-main');
      entry.isIntersecting ? $toc.show() : $toc.hide();
    });
  });

  // 检查目标元素是否存在
  const targetElement = document.querySelector('#post');
  if (targetElement) {
    observer.observe(targetElement);
  }

});