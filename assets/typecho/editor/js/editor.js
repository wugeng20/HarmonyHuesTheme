/**
 * HarmonyHues 后台文章编辑器扩展
 * ---------------------------------------------------------------------------
 * 结构说明（从上到下）：
 *   1. ICONS        工具栏图标
 *   2. helpers      纯工具函数
 *   3. field        面板表单构件（统一字段写法）
 *   4. HH.editor    编辑器读写
 *   5. HH.panel     弹窗面板（打开 / 关闭 / Esc / 点击遮罩）
 *   6. HH.register  模块注册表
 *   7. modules      内置模块（每个 = 一个按钮 + 一个面板 + 一段插入逻辑）
 *   8. HH.boot      启动：插入按钮、绑定事件
 *
 * 新增一个编辑按钮只需在 modules 里追加一个 HH.register({...})：
 *   {
 *     id: 'demo',                 // 按钮 id = wmd-demo-button，也用于面板 id
 *     slot: 'main' | 'extra',     // 放在第一组还是第二组分割栏后
 *     title: '按钮提示',
 *     icon: 'key',                // ICONS 里的 key
 *     panelId: 'demoPanel',       // 需要弹窗时
 *     okId: 'demo_ok', cancelId: 'demo_cancel',
 *     form: function (f) { ... }, // 返回面板内容
 *     build: function (v) { ... } // v = 字段值对象，返回要插入的文本
 *   }
 * 无弹窗的按钮用 action: function () { ... } 代替 form / build。
 * ---------------------------------------------------------------------------
 * @author 星语社长
 * @version 2.0.0
 */
(function (window, document, $) {
  'use strict';

  if (!$) {
    return;
  }

  var HH = window.HHEditor = window.HHEditor || {};

  /* =======================================================================
   * 1. 图标
   * ===================================================================== */
  var ICONS = {
    t: '<svg viewBox="0 0 1024 1024" p-id="7281" width="20" height="20"><path d="M153.6 819.2v102.4h716.8v-102.4H153.6z m230.4-215.04h256l46.08 112.64h107.52L550.4 153.6h-76.8L230.4 716.8h107.52l46.08-112.64zM512 254.976L607.744 512H416.256L512 254.976z" fill="#9b9b9b" p-id="7282"></path></svg>',
    indentation: '<svg viewBox="0 0 1024 1024" p-id="16735" width="20" height="20"><path d="M896 192H128a64 64 0 1 1 0-128h768a64 64 0 1 1 0 128zM896 448H469.333333a64 64 0 1 1 0-128h426.666667a64 64 0 1 1 0 128zM896 704H469.333333a64 64 0 1 1 0-128h426.666667a64 64 0 1 1 0 128zM896 960H128a64 64 0 1 1 0-128h768a64 64 0 1 1 0 128zM153.770667 719.104c-13.184 0-26.453333-2.944-38.954667-8.96a88.917333 88.917333 0 0 1-50.773333-80.768V394.581333c0-34.730667 19.456-65.706667 50.773333-80.768a88.704 88.704 0 0 1 94.805333 10.837334l146.730667 117.333333c21.376 17.152 33.621333 42.666667 33.621333 70.016s-12.245333 52.821333-33.578666 69.973333l-146.816 117.418667a89.344 89.344 0 0 1-55.808 19.712z m38.229333-244.608v75.008L238.890667 512 192 474.496z" fill="#9b9b9b" p-id="11600"></path></svg>',
    video: '<svg viewBox="0 0 1024 1024" p-id="16735" width="20" height="20"><path d="M910.222336 284.444672H113.77664V170.667008c0-31.419392 25.469952-56.889344 56.889344-56.889344h682.665984c31.419392 0 56.889344 25.469952 56.889344 56.889344v113.77664z m0 56.88832v512c0 31.419392-25.469952 56.889344-56.889344 56.889344H170.667008c-31.419392 0-56.889344-25.469952-56.889344-56.889344v-512H910.22336zM284.444672 170.667008l57.2416 113.77664h56.88832l-57.2416-113.77664h-56.88832z m170.665984 0l57.2416 113.77664H569.2416L512 170.668032h-56.889344z m170.667008 0l57.2416 113.77664h56.88832l-57.2416-113.77664h-56.88832z m6.00576 452.108288a28.444672 28.444672 0 0 0 12.721152-12.721152c7.02464-14.051328 1.330176-31.136768-12.721152-38.162432L439.38816 475.69408a28.444672 28.444672 0 0 0-12.720128-3.003392c-15.710208 0-28.444672 12.735488-28.444672 28.444672v192.396288c0 4.415488 1.028096 8.77056 3.002368 12.720128 7.025664 14.051328 24.112128 19.746816 38.162432 12.721152l192.396288-96.197632z" fill="#9b9b9b" p-id="16736"></path></svg>',
    msgpro: '<svg viewBox="0 0 1024 1024" p-id="8277" width="20" height="20"><path d="M606.656155 91.034827c0-50.196774-42.539639-91.034827-95.005193-91.034827S416.716669 40.767154 416.716669 91.034827a87.773454 87.773454 0 0 0 1.276189 15.314269c-131.092986 43.53223-238.718272 178.028387-238.718272 337.268435v125.279236s0 180.509866-46.226407 182.069653a45.588313 45.588313 0 1 0-1.20529 91.034826h759.687046a45.588313 45.588313 0 1 0 0-91.034826c-47.502596 0-47.502596-180.793464-47.502597-180.793464V443.617531c0-159.381846-100.535346-294.019802-238.647372-337.693831a87.348058 87.348058 0 0 0 1.276189-14.888873z m47.431697 796.412933c0 75.436959-63.313162 136.55224-142.43689 136.55224s-142.295091-60.831683-142.43689-136.55224z" fill="#9b9b9b" p-id="8278"></path></svg>',
    tolinks: '<svg viewBox="0 0 1024 1024" p-id="14030" width="20" height="20"><path d="M690.346667 938.666667H333.226667C178.005333 938.666667 85.333333 845.824 85.333333 690.346667V333.226667C85.333333 178.005333 178.005333 85.333333 333.226667 85.333333h357.546666a250.24 250.24 0 0 1 182.229334 65.792A249.856 249.856 0 0 1 938.24 333.226667v357.546666c0 155.221333-92.672 247.893333-247.893333 247.893334zM512 401.834667a32.128 32.128 0 0 0-23.296 9.898666L442.026667 460.8a190.421333 190.421333 0 0 0 0 259.797333 172.245333 172.245333 0 0 0 251.733333 0l93.482667-98.133333a190.549333 190.549333 0 0 0 0.426666-259.84A174.933333 174.933333 0 0 0 678.4 308.906667a32.128 32.128 0 0 0-3.754667-0.213334 31.530667 31.530667 0 0 0-31.232 28.842667 32.042667 32.042667 0 0 0 28.586667 34.986667 109.098667 109.098667 0 0 1 69.12 34.133333 125.056 125.056 0 0 1 0 171.477333l-93.482667 98.133334a111.402667 111.402667 0 0 1-159.146666 0 125.098667 125.098667 0 0 1 0-171.52l46.506666-49.066667A31.872 31.872 0 0 0 512 401.834667zM456.106667 249.173333a172.416 172.416 0 0 0-125.866667 54.613334l-93.44 98.133333a190.592 190.592 0 0 0 0 259.84 172.501333 172.501333 0 0 0 109.226667 53.76h2.986666a31.488 31.488 0 0 0 31.573334-28.586667 32 32 0 0 0-7.082667-23.424 31.488 31.488 0 0 0-21.333333-11.562666 109.354667 109.354667 0 0 1-69.12-34.133334 125.184 125.184 0 0 1 0-171.52l93.269333-98.133333a111.786667 111.786667 0 0 1 159.146667 0 125.098667 125.098667 0 0 1 0 171.52l-46.506667 49.066667a32 32 0 0 0 46.506667 43.946666l46.506666-49.024a190.549333 190.549333 0 0 0 0-259.84 171.050667 171.050667 0 0 0-125.866666-54.656z" p-id="14031" fill="#9b9b9b"></path></svg>',
    cloud: '<svg viewBox="0 0 1024 1024" p-id="10150" width="20" height="20"><path d="M945.493 610.987C928.426 491.52 819.2 402.774 692.906 433.494c-27.307 6.827-54.613 20.48-78.507 37.547-27.306 20.48-47.786 47.786-61.44 81.92-10.24 27.306-10.24 54.613-17.066 81.92-6.827 23.893-27.307 40.96-51.2 27.306-20.48-13.653-17.067-40.96-13.654-61.44 6.827-44.373 23.894-88.746 47.787-126.293 51.2-71.68 133.12-112.64 218.453-112.64 10.24 0 20.48-10.24 17.067-20.48-23.894-174.08-235.52-262.827-378.88-157.013-34.134 23.894-58.027 58.027-75.094 95.574-6.826 17.066-13.653 37.546-17.066 58.026 0 10.24 0 20.48-3.414 27.307-3.413 20.48 0 6.827-44.373 23.893-75.093 27.307-136.533 92.16-153.6 170.667-34.132 153.599 88.748 293.545 242.348 293.545H645.12c44.373 0 98.987 3.413 143.36-3.413 105.813-23.894 170.667-133.12 157.013-238.933z" fill="#9b9b9b" p-id="10151"></path></svg>',
    fold: '<svg viewBox="0 0 1024 1024" p-id="8155" width="20" height="20"><path d="M805.52 228.96L684.89 349.52 564.34 228.96l-39.21 39.21L684.66 427.7l0.23-0.31 0.31 0.31 159.53-159.53-39.21-39.21z m1.17 297.03H217.51c-28.71 0-51.99 23.28-51.99 51.99V779c0 28.71 23.28 51.99 51.99 51.99H806.7c28.71 0 51.99-23.28 51.99-51.99V577.98c-0.01-28.71-23.29-51.99-52-51.99z m17.33-402.03c57.42 0 103.97 46.55 103.97 103.97v568.39c0 57.42-46.55 103.97-103.97 103.97H200.18c-57.42 0-103.97-46.55-103.97-103.97V227.93c0-57.42 46.55-103.97 103.97-103.97h623.84z" fill="#9b9b9b" p-id="8156"></path></svg>',
    gridimg: '<svg viewBox="0 0 1024 1024" p-id="8155" width="20" height="20"><path d="M606.8 917.6c-1.3-0.2-2.6-0.4-3.8-0.7 1.2 0.2 2.5 0.5 3.8 0.7zM606.8 398.5c-1.3 0.2-2.6 0.4-3.8 0.7 1.2-0.3 2.5-0.6 3.8-0.7zM610.7 918c-1.3-0.1-2.6-0.2-3.9-0.4 1.3 0.2 2.6 0.3 3.9 0.4zM610.7 398c-1.3 0.1-2.6 0.2-3.9 0.5 1.2-0.3 2.6-0.4 3.9-0.5zM879.9 883c0.8-0.2 1.6-0.5 2.3-0.8-0.7 0.4-1.5 0.6-2.3 0.8z" fill="#9b9b9b" opacity=".4" p-id="11976"></path><path d="M562.9 256.4V149.8c0-28.6 23.3-51.9 51.9-51.9h260.5c28.6 0 51.9 23.3 51.9 51.9v106.6c0 28.6-23.3 52-51.9 52H614.8c-28.6 0-51.9-23.3-51.9-52zM462.8 151.5v416.3c0 27.8-22 50.6-49.4 51.9-0.9 0-1.7 0.1-2.6 0.1H148.9c-28.2-0.6-50.9-23.7-50.9-52V151.5c0-28.7 23.3-52 52-52h260.8c0.9 0 1.7 0 2.6 0.1 27.5 1.3 49.4 24.1 49.4 51.9zM462.7 765.4v106.5c0 28.7-23.3 52-52 52H150.3c-28.7 0-52-23.3-52-52V765.4c0-28.7 23.3-52 52-52h260.4c28.7 0 52 23.3 52 52zM875.6 397.8H614.7c-0.9 0-1.8 0-2.7 0.1-0.5 0-0.9 0-1.3 0.1-1.3 0.1-2.6 0.2-3.9 0.5-1.3 0.2-2.6 0.4-3.8 0.7-0.7 0.2-1.5 0.4-2.2 0.5-1.8 0.5-3.5 1.1-5.2 1.8-0.3 0.1-0.7 0.3-1 0.4-0.8 0.3-1.5 0.7-2.2 1-1.5 0.7-3 1.5-4.4 2.4-15.1 9.1-25.1 25.7-25.1 44.5v416.3c0 18.8 10.1 35.4 25.1 44.5 1.4 0.9 2.9 1.7 4.4 2.4 0.7 0.4 1.5 0.7 2.2 1 0.3 0.1 0.7 0.3 1 0.4 1.7 0.7 3.5 1.3 5.2 1.8 0.7 0.2 1.4 0.4 2.2 0.5 1.3 0.3 2.5 0.5 3.8 0.7 1.3 0.2 2.6 0.3 3.9 0.4 0.4 0 0.9 0.1 1.3 0.1 0.9 0 1.8 0.1 2.7 0.1h260.8c3.7 0 7.4-0.4 10.9-1.2 0.7-0.1 1.4-0.3 2-0.5 2.1-0.5 4.1-1.2 6-2 0.8-0.3 1.6-0.6 2.3-1 18.1-8.1 30.7-26.3 30.7-47.4v-416c0.1-28.7-23.2-52-51.8-52.1zM608.1 882.3c1.8 0.7 3.7 1.1 5.6 1.2-1.9 0-3.8-0.5-5.6-1.2z" fill="#9b9b9b" p-id="11977"></path><path d="M613.8 883.6c-2-0.1-3.9-0.5-5.6-1.2 1.7 0.6 3.6 1.1 5.6 1.2zM879.9 883c0.8-0.2 1.6-0.5 2.3-0.8-0.7 0.4-1.5 0.6-2.3 0.8z" fill="#9b9b9b" p-id="11978"></path><path d="M608.1 882.3c1.8 0.7 3.7 1.2 5.6 1.2-1.9 0-3.8-0.5-5.6-1.2z" fill="#9b9b9b" p-id="11979"></path></svg>',
    friendshiplink: '<svg viewBox="0 0 1024 1024" p-id="7283" width="20" height="20"><path d="M512 66.56c-246.01088 0-445.44 199.42912-445.44 445.44s199.42912 445.44 445.44 445.44c246.00064 0 445.44-199.43424 445.44-445.44 0-246.01088-199.43424-445.44-445.44-445.44zM453.21216 733.48096c-44.84608 44.8512-117.81632 44.8512-162.69312-0.03072-44.84608-44.84608-44.84608-117.82144 0-162.66752L405.76 455.54176c44.84608-44.83584 117.82144-44.83584 162.69312 0.04096 1.21344 1.20832 2.37568 2.45248 3.52256 3.712a27.47904 27.47904 0 0 1 6.47168 7.7056c0.13824 0.19456 0.29696 0.37888 0.44032 0.56832l-0.08192 0.08192a27.5712 27.5712 0 0 1-37.48864 37.12512c-0.73216 0.75776-7.95136-6.43072-11.79136-10.2656l-0.03072-0.04096c-23.424-23.41376-61.2096-23.60832-84.6336-0.1792l-115.42528 115.42528c-23.424 23.424-23.424 61.39904 0 84.81792l0.03072 0.03072c23.424 23.424 61.39392 23.424 84.81792 0l69.48864-69.48352a89.74848 89.74848 0 0 0 76.82048 1.02912l-107.38176 107.37152z m280.27392-280.26368l-115.24096 115.23584c-44.8512 44.84608-117.81632 44.84608-162.69312-0.03072a104.15104 104.15104 0 0 1-3.52256-3.70688 27.65312 27.65312 0 0 1-6.46656-7.72096c-0.14336-0.19456-0.29696-0.36864-0.44544-0.5632l0.09216-0.08192a27.55072 27.55072 0 0 1 37.48352-37.12c0.72192-0.768 7.95136 6.43072 11.78624 10.27072l0.03072 0.03584c23.424 23.41376 61.2096 23.6032 84.63872 0.1792l115.42528-115.42528c23.424-23.41888 23.424-61.39904 0-84.81792l-0.03584-0.03584c-23.41888-23.41888-61.3888-23.41888-84.81792 0l-69.48352 69.48864c-24.04352-11.76064-52.48-12.2112-76.81536-1.02912l107.38176-107.37664c44.8512-44.84608 117.82144-44.84608 162.69312 0.03072 44.83584 44.83584 44.83584 117.82144-0.01024 162.66752z" fill="#2196F3" p-id="7284"></path></svg>',
    equipment: '<svg viewBox="0 0 1024 1024" p-id="11789" width="20" height="20"><path d="M301.056 180.736h421.888c80.384 0 120.32 39.936 120.32 120.32v421.888c0 80.384-39.936 120.32-120.32 120.32H301.056c-80.384 0-120.32-39.936-120.32-120.32V301.056c0-80.384 39.936-120.32 120.32-120.32z" fill="#2196F3" p-id="11790"></path><path d="M421.888 421.888V120.32c0-33.28-27.136-60.416-60.416-60.416H120.32c-33.28 0-60.416 27.136-60.416 60.416v241.152c0 33.28 27.136 60.416 60.416 60.416h301.568zM120.32 0h241.152c66.56 0 120.32 53.76 120.32 120.32v361.472H120.32C53.76 481.792 0 428.032 0 361.472V120.32C0 53.76 53.76 0 120.32 0z m481.792 421.888h301.056c33.28 0 60.416-27.136 60.416-60.416V120.32c0-33.28-27.136-60.416-60.416-60.416h-241.152c-33.28 0-60.416 27.136-60.416 60.416v301.568zM662.528 0h241.152c66.56 0 120.32 53.76 120.32 120.32v241.152c0 66.56-53.76 120.32-120.32 120.32h-361.472V120.32c0-66.56 53.76-120.32 120.32-120.32z m-60.416 602.112v301.056c0 33.28 27.136 60.416 60.416 60.416h241.152c33.28 0 60.416-27.136 60.416-60.416v-241.152c0-33.28-27.136-60.416-60.416-60.416h-301.568v0.512z m-59.904-59.904h361.472c66.56 0 120.32 53.76 120.32 120.32v241.152c0 66.56-53.76 120.32-120.32 120.32h-241.152c-66.56 0-120.32-53.76-120.32-120.32v-361.472z m-120.32 59.904H120.32c-33.28 0-60.416 27.136-60.416 60.416v241.152c0 33.28 27.136 60.416 60.416 60.416h241.152c33.28 0 60.416-27.136 60.416-60.416v-301.568z m-301.568-59.904h361.472v361.472c0 66.56-53.76 120.32-120.32 120.32H120.32C53.76 1024 0 970.24 0 903.68v-241.152c0-66.56 53.76-120.32 120.32-120.32z" fill="#2196F3" p-id="11791"></path></svg>'
  };

  /* =======================================================================
   * 2. 工具函数
   * ===================================================================== */
  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function trim(value) {
    return String(value == null ? '' : value).replace(/^\s+|\s+$/g, '');
  }

  function markInvalid(el) {
    el.style.borderColor = '#d9534f';
    el.style.boxShadow = '0 0 0 2px rgba(217, 83, 79, .16)';
    el.addEventListener('input', function clear() {
      el.style.borderColor = '';
      el.style.boxShadow = '';
      el.removeEventListener('input', clear);
    });
  }

  /* =======================================================================
   * 3. 表单构件
   * ===================================================================== */
  var field = {
    /**
     * 文本输入
     * @param {{name:string,label:string,placeholder?:string,value?:string,id?:string,required?:boolean}} o
     */
    text: function (o) {
      return '<p><label>' + o.label + (o.required ? '*' : '') + '</label>' +
        '<input type="text" data-hh-field="' + o.name + '"' +
        (o.id ? ' id="' + o.id + '"' : '') +
        ' value="' + escapeHtml(o.value || '') + '"' +
        ' placeholder="' + escapeHtml(o.placeholder || '') + '"' +
        (o.required ? ' data-required="1"' : '') + '></p>';
    },

    /** 多行输入 */
    textarea: function (o) {
      return '<p><label>' + o.label + (o.required ? '*' : '') + '</label>' +
        '<textarea data-hh-field="' + o.name + '"' + (o.id ? ' id="' + o.id + '"' : '') +
        ' placeholder="' + escapeHtml(o.placeholder || '') + '"' +
        (o.required ? ' data-required="1"' : '') + '></textarea></p>';
    },

    /**
     * 下拉选择
     * @param {{name:string,label:string,id?:string,value?:string,options:Array}} o
     */
    select: function (o) {
      var options = (o.options || []).map(function (item) {
        var value = item[0], text = item[1];
        var selected = String(o.value == null ? '' : o.value) === String(value) ? ' selected' : '';
        return '<option value="' + escapeHtml(value) + '"' + selected + '>' + text + '</option>';
      }).join('');
      return '<p><label>' + o.label + '</label></p>' +
        '<p><select data-hh-field="' + o.name + '"' + (o.id ? ' id="' + o.id + '"' : '') + '>' +
        options + '</select></p>';
    },

    /** 说明文字 */
    note: function (text, danger) {
      return '<p style="' + (danger ? 'color:#d9534f;' : '') + 'font-size:12px;">' + text + '</p>';
    }
  };

  /* =======================================================================
   * 4. 编辑器读写
   * ===================================================================== */
  HH.editor = {
    /** 编辑器文本域 */
    el: function () {
      return document.getElementById('text');
    },

    /** 把光标处替换为 content */
    insert: function (content) {
      var textarea = HH.editor.el();
      if (!textarea) {
        return;
      }

      if (document.selection) {
        textarea.focus();
        document.selection.createRange().text = content;
      } else if (textarea.selectionStart || textarea.selectionStart === 0) {
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;
        textarea.value = textarea.value.substring(0, start) + content + textarea.value.substring(end);
        textarea.selectionStart = start + content.length;
        textarea.selectionEnd = start + content.length;
      } else {
        textarea.value += content;
      }

      textarea.focus();
    },

    focus: function () {
      var textarea = HH.editor.el();
      if (textarea) {
        textarea.focus();
      }
    }
  };

  /* =======================================================================
   * 5. 弹窗面板
   * ===================================================================== */
  HH.panel = {
    /**
     * 打开面板
     * @param {string} id 面板 DOM id
     * @param {{body:string, okId:string, cancelId:string, okText?:string, cancelText?:string, focus?:string}} o
     */
    open: function (id, o) {
      HH.panel.close(id);

      var html =
        '<div id="' + id + '">' +
        '<div class="wmd-prompt-background"></div>' +
        '<div class="wmd-prompt-dialog">' +
        o.body +
        '<form>' +
        '<button type="button" class="btn btn-s primary" id="' + o.okId + '">' + (o.okText || '确定') + '</button>' +
        '<button type="button" class="btn btn-s" id="' + o.cancelId + '">' + (o.cancelText || '取消') + '</button>' +
        '</form>' +
        '</div>' +
        '</div>';

      $('body').append(html);

      var $panel = $('#' + id);
      // 点击遮罩关闭
      $panel.find('.wmd-prompt-background').on('click', function () {
        HH.panel.close(id);
      });

      // 自动聚焦第一个可输入项
      var $first = $panel.find(o.focus || 'input[type="text"],textarea,select').first();
      if ($first.length) {
        $first.trigger('focus');
        if ($first.is('input[type="text"]') && $first[0].select) {
          $first[0].select();
        }
      }
    },

    /** 关闭面板并回焦编辑器 */
    close: function (id) {
      $('#' + id).remove();
      HH.editor.focus();
    },

    /** 读取面板字段值 */
    values: function (id) {
      var values = {};
      $('#' + id).find('[data-hh-field]').each(function () {
        values[this.getAttribute('data-hh-field')] = trim(this.value);
      });
      return values;
    },

    /** 校验必填项，返回是否通过 */
    validate: function (id) {
      var ok = true;
      $('#' + id).find('[data-required]').each(function () {
        if (!trim(this.value)) {
          markInvalid(this);
          ok = false;
        }
      });
      return ok;
    }
  };

  // Esc 关闭最上层面板
  $(document).on('keydown', function (event) {
    if (event.key !== 'Escape' && event.keyCode !== 27) {
      return;
    }
    var $panels = $('.wmd-prompt-dialog').parent();
    if (!$panels.length) {
      return;
    }
    HH.panel.close($panels.last().attr('id'));
  });

  /* =======================================================================
   * 6. 模块注册表
   * ===================================================================== */
  HH.modules = [];

  /** 注册一个编辑模块 */
  HH.register = function (def) {
    if (!def || !def.id) {
      return;
    }
    HH.modules.push(def);
  };

  /* =======================================================================
   * 7. 内置模块
   * ===================================================================== */

  // 文本样式
  HH.register({
    id: 't',
    slot: 'main',
    title: '文本样式自定义',
    icon: 't',
    panelId: 'TPanel',
    okId: 'text_ok',
    cancelId: 'text_cancel',
    form: function (f) {
      return '<p><b>文本样式自定义</b></p>' +
        f.text({ name: 'color', label: '输入文本颜色', placeholder: '选填（如 #ffffff 、red）' }) +
        f.select({
          name: 'textPosition', id: 'textPosition', label: '设置文本位置',
          options: [['', '默认'], ['text-center', '居中'], ['text-left', '左对齐'], ['text-right', '右对齐']]
        }) +
        f.textarea({ name: 'text', label: '输入文本内容', placeholder: '请输入内容，支持HTML语法' });
    },
    build: function (v) {
      var colorStyle = v.color ? ' style="color:' + v.color + '"' : '';
      return v.textPosition
        ? '<p class="' + v.textPosition + '"' + colorStyle + '>' + v.text + '</p>'
        : '<p' + colorStyle + '>' + v.text + '</p>';
    }
  });

  // 缩进（无面板）
  HH.register({
    id: 'indentation',
    slot: 'main',
    title: '缩进2字符',
    icon: 'indentation',
    action: function () {
      HH.editor.insert('&emsp;&emsp;');
    }
  });

  // 视频
  HH.register({
    id: 'video',
    slot: 'main',
    title: '插入视频',
    icon: 'video',
    panelId: 'videoPanel',
    okId: 'video_ok',
    cancelId: 'video_cancel',
    form: function (f) {
      return '<p><b>插入视频</b></p>' +
        f.select({
          name: 'alignment', id: 'AlignmentList', label: '视频对齐方式', value: 'center',
          options: [['center', '居中'], ['left', '左对齐'], ['right', '右对齐']]
        }) +
        f.text({ name: 'url', label: '输入视频地址', required: true, placeholder: '必填，不支持云解析（比如：https://xxx.com/xxx.mp4）' }) +
        f.text({ name: 'pic', label: '输入视频封面', placeholder: '选填，不填则不显示封面' });
    },
    build: function (v) {
      return '[player url="' + v.url + '" pic="' + v.pic + '" alignment="' + v.alignment + '" /]';
    }
  });

  // 消息提示
  HH.register({
    id: 'msgpro',
    slot: 'main',
    title: '消息提示框',
    icon: 'msgpro',
    panelId: 'messagePromptPanel',
    okId: 'mes_prompt_ok',
    cancelId: 'mes_prompt_cancel',
    form: function (f) {
      return '<p><b>插入消息提示框</b></p>' +
        f.select({
          name: 'type', id: 'MessagePromptList', label: '提示框样式', value: 'info',
          options: [['info', '一般提示[灰色]'], ['success', '成功提示[绿色]'], ['warning', '警告提示[黄色]'], ['danger', '危险提示[红色]']]
        }) +
        f.textarea({ name: 'msgtext', label: '输入文字内容', placeholder: '请输入内容，支持HTML/Markdown语法' });
    },
    build: function (v) {
      return '[' + v.type + ']' + v.msgtext + '[/' + v.type + ']';
    }
  });

  // 链接卡片
  HH.register({
    id: 'tolinks',
    slot: 'main',
    title: '链接卡片',
    icon: 'tolinks',
    panelId: 'linkCardPanel',
    okId: 'tolinks_ok',
    cancelId: 'tolinks_cancel',
    form: function (f) {
      return '<p><b>插入链接卡片</b></p>' +
        f.text({ name: 'title', label: '输入URL名称', required: true, placeholder: '必填，比如：百度' }) +
        f.text({ name: 'url', label: '输入URL地址', required: true, placeholder: '必填，比如：https://www.baidu.com/' }) +
        f.text({ name: 'favicon', label: '输入Favicon图标', placeholder: '选填，比如：https://www.baidu.com/favicon.ico' });
    },
    build: function (v) {
      return '[tolink title="' + v.title + '" url="' + v.url + '" favicon="' + v.favicon + '" /]';
    }
  });

  // 网盘
  HH.register({
    id: 'cloud',
    slot: 'main',
    title: '网盘下载',
    icon: 'cloud',
    panelId: 'cloudPanel',
    okId: 'cloud_ok',
    cancelId: 'cloud_cancel',
    form: function (f) {
      return '<p><b>网盘下载</b></p>' +
        f.select({
          name: 'type', id: 'cloudTypeList', label: '网盘类型', value: 'default',
          options: [
            ['default', '默认网盘'], ['baidu', '百度网盘'], ['quark', '夸克网盘'],
            ['aliyun', '阿里云网盘'], ['lanzou', '蓝奏云网盘'], ['360', '360网盘'],
            ['weiyun', '腾讯微云'], ['ctfile', '城通网盘'], ['github', 'GitHub仓库']
          ]
        }) +
        f.text({ name: 'title', label: '输入标题', required: true, placeholder: '必填，请输入文件标题' }) +
        f.text({ name: 'url', label: '下载地址', required: true, placeholder: '必填，请输入网盘下载地址' }) +
        f.text({ name: 'password', label: '提取密码', placeholder: '非必填，请输入提取密码' });
    },
    build: function (v) {
      return '[cloud type="' + v.type + '" title="' + v.title + '" url="' + v.url + '" password="' + v.password + '" /]';
    }
  });

  // 折叠框
  HH.register({
    id: 'fold',
    slot: 'main',
    title: '折叠框',
    icon: 'fold',
    panelId: 'foldPanel',
    okId: 'fold_ok',
    cancelId: 'fold_cancel',
    form: function (f) {
      return '<p><b>折叠框</b></p>' +
        f.text({ name: 'title', label: '输入标题', required: true, placeholder: '必填，请输入标题' }) +
        f.select({
          name: 'type', id: 'foldTypeList', label: '是否折叠', value: 'close',
          options: [['close', '是'], ['open', '否']]
        }) +
        f.textarea({ name: 'foldtext', label: '输入内容', placeholder: '请输入内容，支持HTML/Markdown语法' });
    },
    build: function (v) {
      return '[fold title="' + v.title + '" type="' + v.type + '"]' + v.foldtext + '[/fold]';
    }
  });

  // 宫格图片
  HH.register({
    id: 'gridimg',
    slot: 'main',
    title: '宫格图片',
    icon: 'gridimg',
    panelId: 'gridImgPanel',
    okId: 'gridimg_ok',
    cancelId: 'gridimg_cancel',
    form: function (f) {
      return '<p><b>宫格图片</b></p>' +
        f.text({ name: 'imgColumns', label: '宫格图片列数', value: '3' }) +
        f.text({ name: 'imgGap', label: '宫格图片间距', value: '10px' }) +
        f.note('说明：在[GridImg][/GridImg]标签里面添加图片：![标题](xxxx.jpg)，文章可以添加多个宫格图片，其他待开发。', true);
    },
    build: function (v) {
      var columns = parseInt(v.imgColumns, 10) || 3;
      var images = '';
      for (var i = 0; i < columns; i++) {
        images += '![图片标题' + i + '](图片链接' + i + ')\n';
      }
      return '[GridImg columns="' + columns + '" gap="' + (v.imgGap || '10px') + '"]\n' + images + '[/GridImg]\n';
    }
  });

  // 友情链接（页面专用）
  HH.register({
    id: 'friendshiplink',
    slot: 'extra',
    title: '友情链接页面专用',
    icon: 'friendshiplink',
    panelId: 'friendshipLinkPanel',
    okId: 'friendship_ok',
    cancelId: 'friendship_cancel',
    form: function (f) {
      return '<p><b>添加友情链接</b></p>' +
        f.note('提示：友情链接页面专用，其他地方使用无效！！推荐放在最前面', true) +
        f.text({ name: 'linkname', label: '友链名称', required: true, placeholder: '必填，请输入友链名称' }) +
        f.text({ name: 'linkurl', label: '友链地址', required: true, value: 'http://' }) +
        f.text({ name: 'linkavatar', label: '友链头像', required: true }) +
        f.textarea({ name: 'linkdesc', label: '友链描述', placeholder: '非必填，请输入友链描述' }) +
        f.text({ name: 'linkgroup', label: '友链分组', placeholder: '非必填，请输入友链分组' }) +
        f.select({
          name: 'status', id: 'linkStatusList', label: '友链状态', value: '正常',
          options: [['正常', '正常'], ['失联', '失联'], ['隐藏', '隐藏']]
        });
    },
    build: function (v) {
      return '[Links title="' + v.linkname + '" url="' + v.linkurl + '" avatar="' + v.linkavatar +
        '" desc="' + v.linkdesc + '" group="' + v.linkgroup + '" status="' + v.status + '" /]';
    }
  });

  // 我的设备（页面专用）
  HH.register({
    id: 'equipment',
    slot: 'extra',
    title: '我的设备页面专用',
    icon: 'equipment',
    panelId: 'meEquipmentPanel',
    okId: 'equipment_ok',
    cancelId: 'equipment_cancel',
    form: function (f) {
      return '<p><b>添加我的设备</b></p>' +
        f.note('提示：我的设备页面专用，其他地方使用无效！！推荐放在最前面', true) +
        f.text({ name: 'deviceCategory', label: '我的分类', required: true, value: '好玩分享', placeholder: '必填，例如：生产力、家庭娱乐等等' }) +
        f.text({ name: 'deviceModel', label: '设备名称', required: true, placeholder: '必填，例如：iPhone 13、MacBook Pro等等' }) +
        f.text({ name: 'deviceImage', label: '设备图片', required: true, placeholder: '必填，例如：https://xxxx/iphone13.jpg' }) +
        f.text({ name: 'deviceType', label: '设备型号', placeholder: '非必填，例如：手机、电脑等等' }) +
        f.textarea({ name: 'deviceDescription', label: '设备描述/评价', placeholder: '非必填，例如：iPhone 13屏幕显示效果好等等' }) +
        f.text({ name: 'deviceSpecs', label: '设备参数', placeholder: '非必填，例如：白色 / 256G等等' });
    },
    build: function (v) {
      return '[Equipment category="' + v.deviceCategory + '" model="' + v.deviceModel +
        '" image="' + v.deviceImage + '" type="' + v.deviceType + '" specs="' + v.deviceSpecs +
        '" description="' + v.deviceDescription + '" /]';
    }
  });

  /* =======================================================================
   * 8. 启动
   * ===================================================================== */
  HH.boot = function () {
    if (!$('#wmd-button-row').length) {
      return;
    }

    // 分割栏（沿用原有 id，便于旧样式继续生效）
    $('#wmd-more-button').after(
      '<li class="wmd-spacer wmd-spacer3" id="wmd-spacer5"></li>' +
      '<li class="wmd-spacer wmd-spacer4" id="wmd-spacer6"></li>'
    );

    // 按 slot 分组插入，保证注册顺序即显示顺序
    ['main', 'extra'].forEach(function (slot) {
      var buttons = HH.modules.filter(function (def) {
        return (def.slot || 'main') === slot;
      }).map(function (def) {
        return '<li class="wmd-button" id="wmd-' + def.id + '-button" title="' + escapeHtml(def.title) + '">' +
          (ICONS[def.icon] || '') + '</li>';
      });

      if (!buttons.length) {
        return;
      }

      var $target = $('#wmd-spacer' + (slot === 'extra' ? '6' : '5'));
      if ($target.length) {
        $target.after(buttons.join(''));
      }
    });

    // 绑定事件
    HH.modules.forEach(function (def) {
      var selector = '#wmd-' + def.id + '-button';

      $(document).on('click', selector, function () {
        if (typeof def.action === 'function') {
          def.action();
          return;
        }

        HH.panel.open(def.panelId, {
          body: def.form(field),
          okId: def.okId,
          cancelId: def.cancelId
        });
      });

      if (def.panelId) {
        $(document).on('click', '#' + def.okId, function () {
          if (!HH.panel.validate(def.panelId)) {
            return;
          }
          var content = def.build(HH.panel.values(def.panelId));
          HH.panel.close(def.panelId);
          if (content) {
            HH.editor.insert(content);
          }
        });

        $(document).on('click', '#' + def.cancelId, function () {
          HH.panel.close(def.panelId);
        });
      }
    });
  };

  // Typecho 编辑器按钮行在 window.load 后才稳定
  if (document.readyState === 'complete') {
    HH.boot();
  } else {
    window.addEventListener('load', HH.boot);
  }
})(window, document, window.jQuery);
