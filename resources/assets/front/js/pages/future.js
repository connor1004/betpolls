/* eslint-disable no-undef */
class Future {
  constructor() {
    this.init();
    
  }
  
  init() {
    const future = document.querySelector('.future-page');
    if (future === null) {
      return;
    }

    $('.future-page select.category').on('change', function() {
      // var url = new URL(window.location.href);
      // var query_string = url.search;
      // var search_params = new URLSearchParams(query_string); 
      // search_params.set('category_id', $(this).val());
      // search_params.delete('subcategory_id');
      // search_params.delete('page1');
      // search_params.delete('page2');
      // url.search = search_params.toString();
      // var new_url = url.toString();
      window.location.href = $(this).val();
    });
    $('.future-page select.subcategory').on('change', function() {
      // var url = new URL(window.location.href);
      // var query_string = url.search;
      // var search_params = new URLSearchParams(query_string);
      // search_params.set('subcategory_id', $(this).val());
      // search_params.delete('page1');
      // search_params.delete('page2');
      // url.search = search_params.toString();
      // var new_url = url.toString();
      window.location.href = $(this).val();
    });
    $('.future-page select.period').on('change', function() {
      var url = new URL(window.location.href);
      var query_string = url.search;
      var search_params = new URLSearchParams(query_string);
      search_params.set('period', $(this).val());
      search_params.delete('page2');
      url.search = search_params.toString();
      var new_url = url.toString();
      window.location.href = new_url;
    });
  }
}

export default Future;
