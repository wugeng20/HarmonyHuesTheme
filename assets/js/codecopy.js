/* ------------------------------------
 * Harmony Hues主题
 *
 * @author  星语社长
 * @link  https://biibii.cn
 * @update  2024-12-19 19:28:41
 * --------------------------------- */
$(document).ready(function () {
  // 为所有 .pre-copy 按钮绑定点击事件
  $('.pre-copy').on('click', function () {
    // 缓存当前按钮和代码内容
    const $copyButton = $(this);
    const codeContent = $copyButton.closest('.pre-container').find('code').text();

    // 使用 Clipboard API 复制内容
    navigator.clipboard.writeText(codeContent)
      .then(() => {
        // 复制成功，更新按钮文本
        $copyButton.text("复制成功");
        setTimeout(() => $copyButton.text("复制"), 1000); // 1 秒后恢复
      })
      .catch((err) => {
        // 复制失败，尝试使用旧版方法
        const tempTextarea = $('<textarea>');
        $('body').append(tempTextarea);
        tempTextarea.val(codeContent).select();
        const isSuccess = document.execCommand('copy');
        tempTextarea.remove();

        if (isSuccess) {
          $copyButton.text("复制成功");
          setTimeout(() => $copyButton.text("复制"), 1000);
        } else {
          alert('复制失败，请手动复制代码。');
          console.error('复制失败:', err);
        }
      });
  });
});