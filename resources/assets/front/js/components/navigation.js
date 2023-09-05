class Navigation extends HTMLElement {
  constructor() {
    super();
    const toggler = this.querySelector('.navigation .navigation-toggler');
    toggler.addEventListener('click', this.handleNavigationToggle.bind(this));
    window.addEventListener('resize', this.handleWindowResize.bind(this));
  }

  handleWindowResize() {
    if (window.innerWidth >= 1120) {
      document.body.classList.remove('navigation-opened');
      // this.applyNavigationHeight();
    }
  }

  handleNavigationToggle() {
    document.body.classList.toggle('navigation-opened');
    // this.applyNavigationHeight();
  }

  applyNavigationHeight() {
    const site = document.body.querySelector('.site');
    const navigationMain = this.querySelector('.navigation-main');
    const navigationCollapsible = this.querySelector('.navigation-main');
    if (document.body.classList.contains('navigation-opened')) {
      const height = navigationMain.clientHeight + navigationCollapsible.clientHeight;
      site.style.height = `${height}px`;
    } else {
      site.style.height = 'inherit';
    }
  }
}

export default Navigation;
