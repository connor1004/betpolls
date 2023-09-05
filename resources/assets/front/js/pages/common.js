/* eslint-disable no-undef */
import moment from 'moment';
import QueryString from 'qs';

class Common {
  constructor() {
    this.resize();
    this.initialize();
    this.initSelect2();
    this.initWeekSelector();
    this.tabPolls();
  }

  initialize() {
    $(window).resize(() => {
      this.resize();
    });

    $('.site-main').click(() => {
      let more = $('.navigation-more');
      if (!more.hasClass('d-none')) {
        more.addClass('d-none');
      }
    });

    $('.burger-toggler').click(() => {
      let more = $('.navigation-more');
      if (more.hasClass('d-none')) {
        more.removeClass('d-none');
      } else {
        more.addClass('d-none');
      }
    });
  }

  resize() {
    const area = $('.navigation').width() - $('.navigation-brand').width() - 200;

    let i = 0;
    let sum = 0;
    $('.navigation-main .navigation-nav>li').each(function() {
      if (i < $('.navigation-main .navigation-nav>li').length - 2) {
        if (!$(this).hasClass('hide-menu')) {
          $(this).removeClass('d-lg-none');
        }

        sum += $(this).width();
        if (sum > area) {
          $(this).addClass('d-lg-none');
        }

        i++;
      }
    });

    const right = ($('.site-main').width() - $('.container').width()) / 2;

    $('.navigation-more').css('right', right - 15);
  }

  initSelect2() {
    $('.select2').select2({
      theme: 'bootstrap4'
    });
  }

  initWeekSelector() {
    const weekSelectors = document.querySelectorAll('app-week-selector');
    weekSelectors.forEach((element) => {
      element.addEventListener('valueChange', async (event) => {
        const start_at = moment(event.detail).format('YYYY-MM-DD');
        const { action } = element.dataset;
        const params = {
          start_at,
          ajax: true
        };

        const $resultLayoutElement = $('#results-layout');
        $resultLayoutElement.html(
          `
            <div class="text-center">
              <i class="fa fa-spin fa-spinner"></i>
            </div>
          `
        );
        const response = await fetch(`${action}${QueryString.stringify(params, { addQueryPrefix: true })}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        });
        const text = await response.text();
        $resultLayoutElement.html(text);
      });
    });
  }

  tabPolls() {
    $('.tabLink').click(function(event) {
      event.preventDefault();
      event.stopPropagation();

      let id = $(this).attr('id');

      $('.tabLink, .tabPanel').removeClass('active');
      
      $(this).addClass('active');
      $('.' + id).addClass('active');
    });
  }
}

export default Common;
