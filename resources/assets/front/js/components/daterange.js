/* eslint-disable no-useless-constructor */
/* eslint-disable no-undef */

import moment from 'moment';
import './daterange-picker';

class DateRange extends HTMLElement {
  constructor() {
    super();
    this.init();
  }

  init() {
    if (this.dataset.locale) {
      moment.locale(this.dataset.locale);
    }
    const startDate = moment(this.dataset.startDate).format('YYYY-MM-DD');
    const endDate = moment(this.dataset.endDate).format('YYYY-MM-DD');
    this.innerHTML = `
      <input type="text" class="form-control" />
      <input type="hidden" name="${this.dataset.startName}" value="${startDate}" />
      <input type="hidden" name="${this.dataset.endName}" value="${endDate}" />
    `;

    $(this).find('input.form-control').daterangepicker({
      startDate: moment(startDate),
      endDate: moment(endDate),
      locale: {
        format: 'D MMM YYYY',
        cancelLabel: this.dataset.cancelLabel,
        applyLabel: this.dataset.applyLabel
      }
    });

    $(this).find('input.form-control').on('apply.daterangepicker', (event, picker) => {
      $(this).find(`input[name="${this.dataset.startName}"]`).val(picker.startDate.format('YYYY-MM-DD'));
      $(this).find(`input[name="${this.dataset.endName}"]`).val(picker.endDate.format('YYYY-MM-DD'));
      this.dispatchEvent(new CustomEvent('valueChange', picker));
    });
  }
}

export default DateRange;
