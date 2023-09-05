/* eslint-disable no-undef */
class EventGame extends HTMLElement {
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
    $(this).find('form').submit(async (event) => {
      event.preventDefault();
      const formElement = event.target;
      const votes = $(formElement).serialize();
      const response = await fetch(formElement.action, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: votes
      });
      const text = await response.text();
      this.outerHTML = text;
      return false;
    });

    $(this).find('input.custom-control-input').on('click', (event) => {
      const element = event.target;
      const previousValue = $(element).attr('previousValue');
      if (previousValue === 'checked') {
        $(element).prop('checked', false);
        $(element).attr('previousValue', false);
      } else {
        $(element).closest('.game-betting').find('input[type=radio]').attr('previousValue', false);
        $(element).attr('previousValue', 'checked');
      }
    });

    const gameCandidateContents = this.querySelectorAll('.game-candidate-content td');
    for (let i = 0, ni = gameCandidateContents.length; i < ni; i++) {
      const gameCandidateContent = gameCandidateContents[i];
      if (!gameCandidateContent.innerHTML.trim()) {
        gameCandidateContent.style.padding = '0';
        gameCandidateContent.style.borderBottom = '0';
      }
    }

    $(this).on('click', '.game-show-more', this.handleShowMore.bind(this));
  }
}

export default EventGame;
