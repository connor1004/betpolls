import moment from 'moment';

class WeekSelector extends HTMLElement {
  constructor() {
    super();
    this.init();
  }

  generateBlock(startOfWeek) {
    return (`
      <div class="week-selector-block">
        ${this.generateBlockInner(startOfWeek)}
      </div>
    `);
  }

  generateBlockInner(startOfWeek) {
    let html = (`
      <div class="week-selector-block-inner" data-start-date="${startOfWeek.format('YYYY-MM-DD')}" data-end-date="${moment(startOfWeek).endOf('week').format('YYYY-MM-DD')}">
    `);
    let currentDate = moment(startOfWeek.startOf('week'));
    const selectedDateStr = this.selected.format('YYYY-MM-DD');

    for (let i = 0; i < 7; i++) {
      const currentDateStr = currentDate.format('YYYY-MM-DD');
      html += (`
        <div class="week-selector-block-item${currentDateStr === selectedDateStr ? ' active' : ''}" data-date="${currentDateStr}">
          <div class="weekday">${currentDate.format('dd')}</div>
          <div class="day">${currentDate.format('D')}</div>
        </div>
      `);
      currentDate = currentDate.add('day', 1);
    }
    html += '</div>';
    return html;
  }

  init() {
    if (this.dataset.locale) {
      moment.locale(this.dataset.locale);
    }
    this.selected = this.dataset.date ? moment(this.dataset.date) : moment();
    this.startDate = moment(this.selected).startOf('week');
    this.endDate = moment(this.selected).endOf('week');

    const startOfWeek = moment(this.selected).startOf('week');
    const startOfPrevWeek = moment(startOfWeek).add('days', -7);
    const startOfNextWeek = moment(startOfWeek).add('days', 7);
    this.innerHTML = `
      <div class="week-selector-description">
        <div class="range">
          ${this.startDate.format('D MMM YYYY')} - ${this.endDate.format('D MMM YYYY')}
        </div>
        <div class="selected">${this.selected.format('D MMM YYYY')}</div>
      </div>
      <div class="week-selector-main">
        ${this.generateBlock(startOfWeek)}
        ${this.generateBlock(startOfNextWeek)}
        ${this.generateBlock(startOfPrevWeek)}
      </div>
    `;
    const main = this.querySelector('.week-selector-main');

    this.slick = $(main).slick();
    this.slick.on('afterChange', this.slickAfterChange.bind(this));
    this.querySelectorAll('.week-selector-block-item').forEach((element) => {
      element.addEventListener('click', this.handleItemClick.bind(this, element));
    });
  }

  slickAfterChange(event, slick, currentSlideIndex) {
    const slide = slick.$slides[currentSlideIndex];
    const blockInner = slide.querySelector('.week-selector-block-inner');

    this.startDate = moment(blockInner.dataset.startDate);
    this.endDate = moment(blockInner.dataset.endDate);

    const previousStartDate = moment(this.startDate).add('days', -7);
    const nextStartDate = moment(this.startDate).add('days', 7);

    this.querySelector('.week-selector-description').innerHTML = (`
      <div class="range">${this.startDate.format('D MMM YYYY')} - ${this.endDate.format('D MMM YYYY')}</div>
      <div class="selected">${this.selected.format('D MMM YYYY')}</div>
    `);
    this.changeBlockContent(slide, this.startDate);

    const previousSlideIndex = (currentSlideIndex + 2) % 3;
    const previousSlide = slick.$slides[previousSlideIndex];
    this.changeBlockContent(previousSlide, previousStartDate);

    const nextSlideIndex = (currentSlideIndex + 1) % 3;
    const nextSlide = slick.$slides[nextSlideIndex];
    this.changeBlockContent(nextSlide, nextStartDate);

    this.querySelectorAll('.week-selector-block-item').forEach((element) => {
      element.addEventListener('click', this.handleItemClick.bind(this, element));
    });
  }

  handleItemClick(element) {
    this.querySelectorAll('.week-selector-block-item').forEach((item) => {
      item.classList.remove('active');
    });
    element.classList.add('active');

    this.selected = moment(element.dataset.date);
    this.querySelector('.week-selector-description .selected').innerHTML = this.selected.format('D MMM YYYY');
    this.dispatchEvent(new CustomEvent('valueChange', { detail: this.selected.toDate() }));
  }

  changeBlockContent(slide, targetStartDate) {
    const blockInner = slide.querySelector('.week-selector-block-inner');
    const currentStartDate = blockInner.dataset.startDate;
    const blockInners = this.querySelectorAll(`.week-selector-block-inner[data-start-date="${currentStartDate}"]`);
    for (let i = 0, ni = blockInners.length; i < ni; i++) {
      const blockItemInner = blockInners[i];
      blockItemInner.parentElement.innerHTML = this.generateBlockInner(targetStartDate);
    }
  }
}

export default WeekSelector;
