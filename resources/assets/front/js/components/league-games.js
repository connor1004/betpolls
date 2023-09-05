/* eslint-disable no-undef */
class LeagueGames extends HTMLElement {
  constructor() {
    super();
    this.init();
  }

  handleShowMore(event) {
    const element = event.target;
    const gameItemElement = element.closest('.game-item');
    const gameDetailsElement = gameItemElement.querySelector('.game-details');
    const $activeGameItemElements = $(this.querySelectorAll('.game-item.active'));
    const $activeGameDetailsElements = $activeGameItemElements.find('.game-details');

    $activeGameDetailsElements.slideUp(100, 'swing', () => {
      $activeGameItemElements.removeClass('active');
      $activeGameDetailsElements.css('display', '');
    });

    $(gameDetailsElement).slideDown(100, 'swing', () => {
      $(gameItemElement).addClass('active');
      $(gameDetailsElement).css('display', '');
    });
  }

  init() {
    $(this).on('click', '.game-show-more', this.handleShowMore.bind(this));
  }
}

export default LeagueGames;
