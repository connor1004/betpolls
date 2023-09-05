/* eslint-disable no-undef */
class Leaderboard {
  constructor() {
    this.init();
    this.handlePagination = this.handlePagination.bind(this);
    this.handleSearch = this.handleSearch.bind(this);
    this.handleDynamicPeriod = this.handleDynamicPeriod.bind(this);
  }

  async handleSearch() {
    $('.leaderboard-page #action-layout form').submit();
  }

  async handlePagination(event) {
    let url = event.target.href;
    event.preventDefault();

    if (url.indexOf('?') >= 0) {
      url += '&ajax=true';
    } else {
      url += '?ajax=true';
    }

    const response = await fetch(url);
    const html = await response.text();
    $('#results-layout').html(html);
  }

  handleDynamicPeriod(event) {
    const { checked } = event.target;
    const $form = $(event.target).closest('form');
    $form.find('.dynamic-period-layout')[0].style.display = checked ? '' : 'none';
    $form.find('.static-period-layout')[0].style.display = checked ? 'none' : '';
  }

  init() {
    const leaderboard = document.querySelector('.leaderboard-page');
    if (leaderboard === null) {
      return;
    }

    $(document).on('click', '.leaderboard-page #results-layout .pagination a.page-link', this.handlePagination);
    $('.leaderboard-page #action-layout form .select2').on('change', this.handleSearch);
    $('.leaderboard-page #action-layout form [name="dynamic_period"]').on('change', this.handleDynamicPeriod);

    this.handleDynamicPeriod({
      target: $('.leaderboard-page #action-layout form [name="dynamic_period"]')[0]
    });
    document.querySelector('.leaderboard-page #action-layout form app-daterange-picker').addEventListener('valueChange', this.handleSearch);
  }
}

export default Leaderboard;
