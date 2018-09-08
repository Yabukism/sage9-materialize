export default {
  init() {
    // JavaScript to be fired on all pages
    $(".nav-item-dropdown-button").dropdown({constrainWidth: true});
    $(".side-menu-nav-item-dropdown-button").dropdown({constrainWidth: false});
    $(".button-collapse").sideNav();
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
