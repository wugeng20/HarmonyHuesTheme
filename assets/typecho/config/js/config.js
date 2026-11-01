/**
 * HarmonyHues 主题设置面板
 * 把 Typecho 平铺的表单项重组为「左侧分类 + 右侧卡片分区」
 */
(function () {
  'use strict';

  var GROUPS = {
    basic: { no: '01', title: '基础信息', desc: '站点图标、LOGO、博主资料与收款码。' },
    look: { no: '02', title: '外观与布局', desc: '导航形态、首页排版、缩略图与图标库。' },
    nav: { no: '03', title: '导航与分类', desc: '菜单配置、隐藏分类与文章封面。' },
    safe: { no: '04', title: '功能与安全', desc: '外链页、头像源与 CSP 策略。' },
    home: { no: '05', title: '侧边栏与首页', desc: '侧栏模块、首页模块与文案。' },
    comment: { no: '06', title: '评论', desc: '敏感词、中文限制与字数上限。' },
    mail: { no: '07', title: '邮件通知', desc: 'SMTP 发信设置，仅评论邮件开启时生效。' },
    footer: { no: '08', title: '页脚与自定义', desc: '备案、建站日期、统计与自定义代码。' },
    backup: { no: '09', title: '备份与恢复', desc: '备份当前配置、还原或删除备份。' }
  };

  function el(tag, cls, html) {
    var node = document.createElement(tag);
    if (cls) node.className = cls;
    if (html != null) node.innerHTML = html;
    return node;
  }

  function badgeOf(label) {
    var text = label.textContent;
    var changed = text.replace(/[（(]\s*(非必填|必填)\s*[)）]/, '');
    if (changed === text) return;
    var required = /必填/.test(text) && !/非必填/.test(text);
    label.textContent = changed.trim();
    label.appendChild(el('em', 'hhc-badge ' + (required ? 'is-required' : 'is-optional'),
      required ? '必填' : '非必填'));
  }

  function tidyDescAll() {
    tidyDesc(document);
  }

  function tidyDesc(scope) {
    var nodes = scope.querySelectorAll('.description');
    for (var i = 0; i < nodes.length; i++) {
      var text = nodes[i].textContent.replace(/^\s*介绍\s*[：:]\s*/, '');
      if (text !== nodes[i].textContent) nodes[i].textContent = text;
    }
  }

  function build() {
    var firstField = document.querySelector('.setting-content[data-hhc]');
    if (!firstField) return;
    var form = firstField.closest('form');
    if (!form) return;
    var fields = Array.prototype.slice.call(form.querySelectorAll('.setting-content[data-hhc]'));
    if (!fields.length) return;

    /* 表单：display:none 会把提交按钮藏掉，这里用 display:contents 让子项参与外层网格 */
    form.classList.add('hhc-form');

    var wrap = el('div', 'hhc-wrap');
    form.parentNode.insertBefore(wrap, form);
    wrap.appendChild(form);

    var intro = el('header', 'hhc-intro');
    intro.innerHTML =
      '<p class="hhc-eyebrow">HARMONYHUES · THEME OPTIONS</p>' +
      '<h2 class="hhc-title">主题设置' +
      (function () {
        var meta = document.getElementById('hhc-meta');
        var ver = meta ? meta.getAttribute('data-version') : '';
        return ver ? '<span class="hhc-ver">v' + ver + '</span>' : '';
      })() + '</h2>' +
      '<p class="hhc-sub">按分类快速定位；每个分区内是同类配置项。改动后点底部「保存设置」，改前建议先在「备份与恢复」里备份一份。</p>';
    wrap.insertBefore(intro, form);

    var aside = el('aside', 'hhc-aside');
    var nav = el('nav', 'hhc-nav');
    nav.setAttribute('aria-label', '设置分类');
    var search = el('div', 'hhc-search');
    search.innerHTML = '<input type="search" class="hhc-search-input" placeholder="搜索设置项…" aria-label="搜索设置项">';
    aside.appendChild(search);
    aside.appendChild(nav);
    wrap.insertBefore(aside, form);

    var main = el('div', 'hhc-main');
    /* 字段必须留在 form 内部，否则不会被提交 */
    form.insertBefore(main, form.firstChild);

    var emptyTip = el('p', 'hhc-empty', '没有匹配的设置项，换个关键词试试。');
    emptyTip.hidden = true;
    main.appendChild(emptyTip);

    /* 按出现顺序建分区 */
    var order = [];
    var buckets = {};
    fields.forEach(function (field) {
      var key = field.getAttribute('data-hhc');
      if (!buckets[key]) { buckets[key] = []; order.push(key); }
      buckets[key].push(field);
      if (field.getAttribute('data-hhc-mono') === '1') field.classList.add('hhc-mono');
      var label = field.querySelector('.typecho-label');
      if (label) badgeOf(label);
    });

    var navItems = {};
    order.forEach(function (key) {
      var meta = GROUPS[key] || { no: '00', title: key, desc: '' };
      var section = el('section', 'hhc-section');
      section.id = 'hhc-' + key;
      section.setAttribute('data-group', key);
      var head = el('header', 'hhc-section-head');
      head.innerHTML = '<span class="hhc-section-no">' + meta.no + '</span>' +
        '<div class="hhc-section-text"><h3 class="hhc-section-title">' + meta.title + '</h3>' +
        '<p class="hhc-section-desc">' + meta.desc + '</p></div>';
      section.appendChild(head);
      var body = el('div', 'hhc-section-body');
      section.appendChild(body);
      buckets[key].forEach(function (f) { body.appendChild(f); });
      main.appendChild(section);

      var link = el('a', 'hhc-nav-item' + (order[0] === key ? ' is-active' : ''));
      link.href = '#hhc-' + key;
      link.setAttribute('data-target', 'hhc-' + key);
      link.innerHTML = '<i>' + meta.no + '</i><span>' + meta.title + '</span>';
      nav.appendChild(link);
      navItems[key] = link;
    });

    /* 备份表单：位于主题表单之外，单独包一张卡片 */
    var backupForm = document.querySelector('.harmonyhues-backup');
    if (backupForm) {
      var backup = el('section', 'hhc-section hhc-section--backup mb-4');
      backup.id = 'hhc-backup';
      backup.setAttribute('data-group', 'backup');
      backup.innerHTML = '<header class="hhc-section-head">' +
        '<div class="hhc-section-text"><h3 class="hhc-section-title">备份与恢复</h3>' +
        '<p class="hhc-section-desc">备份当前配置、还原或删除备份；改配置前建议先备份一份。</p></div></header>';
      var bBody = el('div', 'hhc-section-body hhc-section-body--backup');
      bBody.appendChild(backupForm);
      backup.appendChild(bBody);
      wrap.insertBefore(backup, wrap.firstChild);
      backupForm.classList.add('hhc-backup-form');
    }

    /* 提交按钮条 */
    var submitBar = form.querySelector('ul.typecho-option:not(.setting-content):not([data-hhc])');
    if (submitBar) submitBar.classList.add('hhc-submit');

    /* 点击跳转 */
    nav.addEventListener('click', function (event) {
      var link = event.target.closest('.hhc-nav-item');
      if (!link) return;
      event.preventDefault();
      var target = document.getElementById(link.getAttribute('data-target'));
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    /* 滚动高亮 */
    var sections = Array.prototype.slice.call(wrap.querySelectorAll('.hhc-section'));
    if ('IntersectionObserver' in window && sections.length) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          var key = entry.target.getAttribute('data-group');
          Object.keys(navItems).forEach(function (k) {
            navItems[k].classList.toggle('is-active', k === key);
          });
        });
      }, { rootMargin: '-15% 0px -70% 0px', threshold: 0 });
      sections.forEach(function (s) { io.observe(s); });
    }

    /* 搜索 */
    var input = search.querySelector('.hhc-search-input');
    input.addEventListener('input', function () {
      var q = this.value.trim().toLowerCase();
      var anyVisible = false;

      sections.forEach(function (section) {
        var hit = 0;
        Array.prototype.forEach.call(section.querySelectorAll('.setting-content[data-hhc]'), function (field) {
          var text = field.textContent.toLowerCase();
          var match = !q || text.indexOf(q) !== -1;
          field.classList.toggle('is-hidden', !match);
          if (match) hit++;
        });
        section.classList.toggle('is-hidden', !!q && hit === 0);
        if (!section.classList.contains('is-hidden')) anyVisible = true;
      });

      Object.keys(navItems).forEach(function (key) {
        var section = document.getElementById('hhc-' + key);
        var dim = !!q && (!section || section.classList.contains('is-hidden'));
        navItems[key].classList.toggle('is-dim', dim);
      });

      emptyTip.hidden = anyVisible;

      if (q) {
        var firstVisible = sections.filter(function (s) { return !s.classList.contains('is-hidden'); })[0];
        Object.keys(navItems).forEach(function (k) {
          navItems[k].classList.toggle('is-active', !!firstVisible && firstVisible.id === 'hhc-' + k);
        });
        window.scrollTo({ top: 0, behavior: 'auto' });
      }
    });
  }

  document.addEventListener('DOMContentLoaded', tidyDescAll);

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', build);
  } else {
    build();
  }
})();
