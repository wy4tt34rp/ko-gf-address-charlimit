(function () {
  function applyLimits(root) {
    root = root || document;
    var max = 30;

    root.querySelectorAll('.ginput_address_line_1 input, .ginput_address_line_2 input')
      .forEach(function (el) {
        el.setAttribute('maxlength', String(max));
      });
  }

  document.addEventListener('DOMContentLoaded', function () {
    applyLimits(document);
  });

  document.addEventListener('gform_post_render', function () {
    applyLimits(document);
  });

  new MutationObserver(function (muts) {
    muts.forEach(function (m) {
      m.addedNodes.forEach(function (n) {
        if (n.nodeType === 1) applyLimits(n);
      });
    });
  }).observe(document.documentElement, { childList: true, subtree: true });
})();
