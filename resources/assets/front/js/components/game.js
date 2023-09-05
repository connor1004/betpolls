/* eslint-disable no-undef */
class Game extends HTMLElement {
  constructor() {
    super();
    this.init();
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

    const gameTeamContents = this.querySelectorAll('.game-team-content td');
    for (let i = 0, ni = gameTeamContents.length; i < ni; i++) {
      const gameTeamContent = gameTeamContents[i];
      if (!gameTeamContent.innerHTML.trim()) {
        gameTeamContent.style.padding = '0';
        gameTeamContent.style.borderBottom = '0';
      }
    }
  }
}

export default Game;
