/* ------------------------------------
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-7-6 18:00:04
 * --------------------------------- */
$(document).ready(function () {
  // 使用防抖技术优化事件
  const debounce = (func, wait) => {
    var timeout;
    return function () {
      var context = this, args = arguments;
      clearTimeout(timeout);
      timeout = setTimeout(function () {
        func.apply(context, args);
      }, wait);
    };
  }
  // 节流函数，减少滚动事件触发频率
  const throttle = (func, wait) => {
    let timeout = null;
    return function () {
      if (!timeout) {
        timeout = setTimeout(() => {
          func.apply(this, arguments);
          timeout = null;
        }, wait);
      }
    };
  };
  /*---------------------点击“回到顶部”按钮时平滑滚动到顶部st---------------------*/
  $('#nav-backtop').click(function () {
    $('html, body').animate({ scrollTop: 0 }, 200);
    return false;
  });
  /*---------------------点击“回到顶部”按钮时平滑滚动到顶部end---------------------*/

  /*---------------------夜间模式切换事件st---------------------*/
  const THEMES = {
    LIGHT: 'light',
    DARK: 'dark',
    SYSTEM: 'system'
  };

  // 设置主题
  function setTheme(theme = THEMES.LIGHT) {
    let effectiveTheme = theme;

    if (theme === THEMES.SYSTEM) {
      effectiveTheme = getSystemTheme(); // 获取系统主题
    }

    $('html').attr('data-theme', effectiveTheme);
    setCookie('theme', theme, 7); // 过期时间为 7 天
    setCookie('system_theme', effectiveTheme, 1); // 过期时间为 1 天
  }

  // 获取系统主题
  function getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? THEMES.DARK : THEMES.LIGHT;
  }

  // 设置 Cookie
  function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${value}; expires=${expires.toUTCString()}; path=/`;
  }

  // 按钮点击事件
  $('.theme-toggle').on('click', 'button', function () {
    const theme = $(this).attr('title');
    switch (theme) {
      case '浅色模式':
        setTheme(THEMES.LIGHT);
        break;
      case '深色模式':
        setTheme(THEMES.DARK);
        break;
      case '跟随系统':
        setTheme(THEMES.SYSTEM);
        break;
    }
    setActiveButton($(this));
  });

  // 设置激活按钮
  function setActiveButton(button) {
    $('.theme-toggle button').removeClass('active');
    button.addClass('active');
  }

  // 初始化时设置激活按钮
  function initActiveButton() {
    const currentTheme = getCookie('theme') || THEMES.LIGHT;
    let activeButton;
    if (currentTheme === THEMES.LIGHT) {
      activeButton = $('.theme-toggle button[title="浅色模式"]');
    } else if (currentTheme === THEMES.DARK) {
      activeButton = $('.theme-toggle button[title="深色模式"]');
    } else if (currentTheme === THEMES.SYSTEM) {
      activeButton = $('.theme-toggle button[title="跟随系统"]');
    } else {
      activeButton = $('.theme-toggle button[title="浅色模式"]');
    }
    setActiveButton(activeButton);
  }

  // 获取 Cookie
  function getCookie(name) {
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
      const [cookieName, cookieValue] = cookie.trim().split('=');
      if (cookieName === name) {
        return cookieValue;
      }
    }
    return null;
  }

  // 初始化
  initActiveButton();

  // 监听系统主题变化
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    setTheme(THEMES.SYSTEM);
  });
  /*---------------------夜间模式切换事件end---------------------*/

  /*---------------------滚动事件st---------------------*/
  // 滚动进度条
  const updateScrollProgress = () => {
    const scrollTop = $(window).scrollTop();
    const docHeight = $(document).height();
    const winHeight = $(window).height();
    const scrollPercent = Math.round((scrollTop / (docHeight - winHeight)) * 100);
    if (scrollPercent > 0) {
      $('#nav-backtop').toggleClass('top-to-active', scrollPercent >= 90);
      $('.back-to-top').show();
      $('#nav-backtop>.top-to-text').text(scrollPercent >= 90 ? "回到顶部" : scrollPercent);
    } else {
      $('.back-to-top').hide();
    }
  }
  // 滚动Nav背景
  let $isNav = false;
  if ($('#navbar').hasClass('nav-ui-one')) {
    $isNav = true;
  }
  toggleNavOnScroll = (navSelector, classOne, classTwo, scrollThreshold = 100) => {
    if ($isNav) { return; }

    const $nav = $(navSelector);
    const currentScrollTop = $(this).scrollTop();

    if (currentScrollTop > scrollThreshold) {
      // 向下滚动超过阈值，切换到 classOne
      if ($nav.hasClass(classTwo)) {
        $nav.removeClass(classTwo).addClass(classOne);
      }
    } else {
      // 回到顶部，切换到 classTwo
      if ($nav.hasClass(classOne)) {
        $nav.removeClass(classOne).addClass(classTwo);
      }
    }
  }

  // 页面加载时更新进度
  updateScrollProgress();
  toggleNavOnScroll('#navbar', 'nav-ui-one', 'nav-ui-two', 100);
  /*---------------------滚动事件end---------------------*/

  /*---------------------滚动进事件集合st---------------------*/
  $(window).scroll(function () {
    /*滚动进度条*/
    throttle(updateScrollProgress(), 100);
    /*滚动Nav背景*/
    throttle(toggleNavOnScroll('#navbar', 'nav-ui-one', 'nav-ui-two', 50), 100);
  });
  /*---------------------滚动进事件集合end---------------------*/

  /*---------------------手机端导航栏st---------------------*/
  let startY = 0;
  let currentY = 0;
  let dragging = false;

  // 点击菜单按钮打开/关闭侧边栏
  $('#menu-line').click(function (event) {
    event.stopPropagation();
    const $navComponents = $('#nav-components');

    // 如果导航组件已经存在，则关闭它
    if ($navComponents.length) {
      closeSidebar();
      return;
    }

    // 获取导航组件元素
    const $mobileNav = $('#mobile-nav');

    // 添加背景虚化
    $mobileNav.append(`<div id="mobile-nav-bg" class="header-overlay-card"></div>`);

    // 克隆导航内容
    const navContent = $('.nav-inner > ul').clone();

    // 添加样式
    navContent.find('.sub-menu').addClass('mt-1');
    navContent.find('.sub-menu>ul').addClass('row p-1 ml-2');
    navContent.find('.sub-menu>ul>li').addClass('col-4 p-1');
    navContent.find('.nav-item').addClass('pb-3');
    navContent.find('.nav-item>.nav-a').addClass('font-weight-bold');
    navContent.find('.nav-item>.nav-a').prepend('<i class="iconfont icon-xingqiu font-weight-normal mr-1"></i>');

    // 创建导航组件
    $mobileNav.append(`<div id="nav-components" class="mobile-aside px-3 py-2"><div class="mx-auto"><i class="back-box mb-2"></i></div>${navContent.prop('outerHTML')}</div>`);

    // 延迟添加 .open 类，确保动画生效
    requestAnimationFrame(() => {
      $('#mobile-nav-bg').css({
        'opacity': '1',
        'visibility': 'visible'
      });
      $('#nav-components').addClass('open');
    });

    // body关闭滚动
    $('body').css('overflow', 'hidden');
  });

  // 点击文档关闭侧边栏（如果点击区域不在侧边栏内）
  $(document).click(function (event) {
    if ($('#nav-components').hasClass('open') && !$(event.target).closest('#nav-components').length && !$(event.target).closest('#menu-line').length) {
      closeSidebar();
    }
  });

  // 阻止侧边栏内部点击事件冒泡
  $('#nav-components').click(function (event) {
    event.stopPropagation();
  });

  // 触摸开始时记录初始位置
  $(document).on('touchstart', function (event) {
    if ($('#nav-components').hasClass('open')) {
      startY = event.originalEvent.touches[0].pageY;
      currentY = startY;
      dragging = true;
    }
  });

  // 触摸移动时更新侧边栏位置
  $(document).on('touchmove', function (event) {
    if (dragging) {
      currentY = event.originalEvent.touches[0].pageY;
      const offset = currentY - startY;
      if (offset > 0) {
        $('#nav-components').css('transform', `translateY(${offset}px)`);
      }
    }
  });

  // 触摸结束时判断是否关闭侧边栏
  $(document).on('touchend', function () {
    if (dragging) {
      const offset = currentY - startY;
      const threshold = $('#nav-components').height() / 2;

      if (offset > threshold) {
        closeSidebar();
      } else {
        $('#nav-components').css('transform', '');
      }
      dragging = false;
    }
  });

  // 关闭侧边栏的函数
  const closeSidebar = () => {
    const $navComponents = $('#nav-components');
    const $mobileNavBg = $('#mobile-nav-bg');

    // 移除 open 类并添加关闭动画
    $navComponents.removeClass('open');

    // 淡出背景
    $mobileNavBg.css({
      'opacity': 0,
      'visibility': 'hidden'
    });

    // 动画结束后清理侧边栏
    $navComponents.one('transitionend', function () {
      $navComponents.remove();
      $mobileNavBg.remove();
      $('body').css('overflow', '');
    });
  };
  /*---------------------手机端导航栏end---------------------*/

  /*---------------------搜索框按钮开始---------------------*/
  // 点击搜索按钮时，显示搜索面板
  $('#search-btn').click(function () {
    $('.main-search').addClass('open');
  });

  // 点击搜索关闭按钮时，隐藏搜索面板
  $('.search-box').on('click', '#search-close-btn', function () {
    $('.main-search').removeClass('open');
  });
  /*---------------------搜索框按钮结束---------------------*/

  /*---------------------通用面板函数开始---------------------*/
  // 创建不同类型的面板
  const createPanel = (type, content, closeBtnId) => {
    return $('<div class="main-' + type + '">' +
      '<div class="header-overlay-card"></div>' +
      '<div class="' + type + '-box card p-4 no-animation">' +
      content +
      '<div id="' + closeBtnId + '" class="close-btn"><i class="iconfont icon-guanbi"></i></div>' +
      '</div>' +
      '</div>');
  };

  // 显示面板
  const showPanel = (panelSelector, createFunc) => {
    if ($(panelSelector).length === 0) {
      $('body').append(createFunc());
    }
    requestAnimationFrame(() => {
      $(panelSelector).addClass('open');
    });
  };

  // 隐藏并移除面板
  const hidePanel = (panel) => {
    panel.removeClass('open');
    setTimeout(function () {
      panel.remove();
    }, 500);
  };

  // 点击关闭按钮时，隐藏对应的面板
  $(document).on('click', '.close-btn', function () {
    const panel = $(this).closest('.main-reward, .main-share');
    hidePanel(panel);
  });

  // 搜索框+赞赏面板+分享面板,点击文档其他地方时，隐藏赞赏面板
  $(document).click(function (event) {
    const $target = $(event.target);
    // 隐藏搜索框
    if (!$target.closest('.search-box, #search-btn').length && $('.search-box').is(':visible')) {
      $('.main-search').removeClass('open');
    }
    // 隐藏赞赏面板
    if (!$target.closest('.reward-box, #reward-btn').length && $('.reward-box').is(':visible')) {
      hidePanel($('.main-reward'));
    }
    // 隐藏分享面板
    if (!$target.closest('.share-box, #share-btn').length && $('.share-box').is(':visible')) {
      hidePanel($('.main-share'));
    }
  });

  // 防止事件冒泡
  $(document).click(function (event) {
    event.stopPropagation();
  });
  /*---------------------通用面板函数结束---------------------*/

  /*---------------------赞赏按钮开始---------------------*/
  // 创建赞赏面板
  const createRewardBox = () => {
    return createPanel('reward',
      '<div class="reward-content d-flex justify-content-center">' +
      '<div class="reward-qr d-flex flex-column align-items-center"><img src="' + decodeURIComponent(_WXQR) + '" /><span>微信</span></div>' +
      '<div class="reward-qr d-flex flex-column align-items-center"><img src="' + decodeURIComponent(_ZFBQR) + '" /><span>支付宝</span></div>' +
      '</div>',
      'reward-close-btn'
    );
  };

  // 点击赞赏按钮时，显示赞赏面板
  $('#reward-btn').click(function () {
    showPanel('.main-reward', createRewardBox);
  });
  /*---------------------赞赏按钮结束---------------------*/

  /*---------------------分享按钮开始---------------------*/
  const baseUrls = {
    qq: "http://connect.qq.com/widget/shareqq/index.html?url=",
    weibo: "http://service.weibo.com/share/share.php?url=",
    twitter: "https://twitter.com/intent/tweet?url=",
    wechat: "javascript:void(0);"
  };

  // 创建分享面板
  const createShareBox = () => {
    return createPanel('share',
      '<p class="p-2">' + decodeURIComponent(_ARTICLE_URL) + '</p>' +
      '<div class="share-a d-flex flex-row justify-content-center">' +
      '<a class="share-item d-flex align-items-center justify-content-center" href="' + baseUrls.qq + decodeURIComponent(_ARTICLE_URL) + '&title=' + decodeURIComponent(_ARTICLE_NAME) + '"><i class="iconfont icon-qq"></i></a>' +
      '<a class="share-item d-flex align-items-center justify-content-center" href="' + baseUrls.weibo + decodeURIComponent(_ARTICLE_URL) + '&title=' + decodeURIComponent(_ARTICLE_NAME) + '"><i class="iconfont icon-weibo"></i></a>' +
      '<a class="share-item d-flex align-items-center justify-content-center" href="' + baseUrls.twitter + decodeURIComponent(_ARTICLE_URL) + '&text=' + decodeURIComponent(_ARTICLE_NAME) + '"><i class="iconfont icon-tuite"></i></a>' +
      '<a id="share-wechat" class="share-item d-flex align-items-center justify-content-center" href="' + baseUrls.wechat + '"><i class="iconfont icon-pengyouquan"></i></a>' +
      '</div>',
      'share-close-btn'
    );
  };

  // 点击分享按钮时，显示分享面板
  $('#share-btn').click(function () {
    showPanel('.main-share', createShareBox);
  });

  // 点击微信分享按钮时，显示或隐藏微信二维码
  $(document).on('click', '#share-wechat', function () {
    if ($('.wechat-qrcode').length === 0) {
      $('.share-box').append('<div class="wechat-qrcode text-center my-4"><img src="//api.qrserver.com/v1/create-qr-code/?size=200x200&margin=10&data=' + encodeURIComponent(_ARTICLE_URL) + '" title="' + encodeURIComponent(_ARTICLE_NAME) + '"><div class="mt-2">微信扫码分享</div></div>');
    } else {
      $('.wechat-qrcode').remove();
    }
  });
  /*---------------------分享按钮结束---------------------*/

  /*---------------------动态计时器开始---------------------*/
  /**
         * 数字跳动动画函数（jQuery版）
         * @param {Object} options 配置选项
         * @param {number} options.start - 起始值
         * @param {number} options.end - 结束值
         * @param {string} options.selector - jQuery选择器
         * @param {number} [options.duration=1500] - 动画时长(ms)
         * @param {function} [options.easing] - 缓动函数
         * @param {function} [options.format] - 数字格式化函数
         */
  const animateNumber = (options) => {
    const config = $.extend({
      duration: 1500,
      easing: t => t * (2 - t), // easeOutQuad
      format: n => n.toLocaleString()
    }, options);

    const $element = $(config.selector);
    if (!$element.length) {
      console.error('元素未找到:', config.selector);
      return;
    }

    let startTime = null;
    const range = config.end - config.start;
    const isLargeNumber = Math.abs(range) > 10000;

    const animate = timestamp => {
      startTime = startTime || timestamp;
      const elapsed = timestamp - startTime;
      const progress = Math.min(elapsed / config.duration, 1);
      const eased = config.easing(progress);

      let current = config.start + range * eased;

      // 大数字优化处理
      if (isLargeNumber) {
        const remaining = config.end - current;
        current = Math.abs(remaining) > 1000
          ? config.end - remaining * (1 + Math.random() / 3)
          : current;
      }

      $element.text(config.format(Math.floor(current)));

      if (progress < 1) {
        requestAnimationFrame(animate);
      } else {
        $element.text(config.format(config.end));
      }
    };

    requestAnimationFrame(animate);
  }
  /*---------------------动态计时器结束---------------------*/
  /*---------------------懒加载开始---------------------*/
  // 基础配置
  var lazyLoadConfig = {
    effect: "fadeIn",
    threshold: 200,
    container: window
  };

  // 通用加载处理函数
  function handleLazyLoad(loadedClass, removedClass) {
    return function () {
      $(this).addClass(loadedClass).removeClass(removedClass);
    };
  }

  // 初始化懒加载
  $("img.lazy").lazyload($.extend({}, lazyLoadConfig, {
    load: handleLazyLoad("loaded", "lazy")
  }));

  $(".hh-widget img.widget-lazy").lazyload($.extend({}, lazyLoadConfig, {
    load: handleLazyLoad("loaded", "widget-lazy")
  }));
  /*---------------------懒加载结束---------------------*/

  /*---------------------一些插件集合str---------------------*/
  // 动画延迟函数
  function applyAnimationDelay(selector, delayFactor) {
    $(selector).each(function (index) {
      var delay = (index * delayFactor) + 's';
      $(this).css('animation-delay', delay);
    });
  }

  // 导航栏菜单动画
  applyAnimationDelay('.nav-menu>.nav-item', 0.15);

  // 侧边栏添加动画，但不包括具有 .no-animation 类的元素
  applyAnimationDelay('.hh-widget:not(.no-animation)', 0.3);

  // 选择所有.card元素，但不包括具有 .no-animation 类的元素
  applyAnimationDelay('.card:not(.no-animation)', 0.3);

  // 友情链接添加动画
  applyAnimationDelay('.links-card', 0.3);

  // 轮播图
  if (typeof Swiper !== 'undefined') {
    var indexSwiper = new Swiper('.swiper-container', {
      direction: 'horizontal', // 水平切换
      loop: true, // 循环模式
      autoplay: 5000, // 自动轮播间隔时间
      speed: 1000, // 切换速度
      autoplayDisableOnInteraction: false, // 用户操作swiper之后，是否禁止autoplay。默认为true：停止。
      pagination: '.swiper-pagination', // 分页器
      paginationClickable: true, // 分页器可点击
      prevButton: '.swiper-button-prev', // 上一页
      nextButton: '.swiper-button-next', // 下一页
      roundLengths: true, // 将slide的宽和高取整，以防止某些分辨率的屏幕上出现模糊
      parallax: true, // 开启视差效果
      lazyLoading: true, // 懒加载
    });

    $('.swiper-container').mouseenter(function () {
      indexSwiper.stopAutoplay();
    }).mouseleave(function () {
      indexSwiper.startAutoplay();
    });

    $('.swiper-container').hover(function () {
      indexSwiper.stopAutoplay();
    }, function () {
      indexSwiper.startAutoplay();
    });
  }

  // 灯箱
  if (typeof ViewImage !== 'undefined') {
    window.ViewImage && ViewImage.init('.post-content img[show-img],.comment-list img');
  }

  // SVG图标
  showSvg();

  // 首页底部时间之旅
  if ($('.timejourney-progress').length > 0) {
    // 元素存在，获取 data-percentage 属性值
    var percentage = $('.timejourney-progress').data('percentage');
    const percentageNum = percentage ? percentage : 0;

    // 设置进度条的宽度
    $('.timejourney-progress').css({
      'width': percentageNum + '%',
      'transition': 'width ' + (percentageNum / 10 + 1) + 's ease-in-out'
    });

    // 动态计时器
    animateNumber({
      start: 0,
      end: percentageNum,
      duration: (percentageNum / 10 * 1000 + 1500),
      selector: '#timejourney-progress',
      format: n => `${n}%`
    });
  }
  /*---------------------一些插件集合end---------------------*/
});