<?php
  use Illuminate\Support\Facades\Request;
  use App\Facades\Constants;
  use App\Leaderboard;

  $locale = app('translator')->getLocale();
  $urlPrefix = $locale == 'es' ? "/es" : '';
  if ($is_future) {
    $urlPrefix .= $locale == 'es' ? '/futuros' : '/futures';
  }
  else {
    $urlPrefix .= $locale == 'es' ? '/deporte' : '/sport';
  }
  $subUrlPrefix = $cur_category ? $cur_category->getLocaleUrl($is_future) : $urlPrefix;
?>
<form action="" method="get" class="future-header">
  <?php if ($is_future) : ?>
    <div class="header-container mr-4 mb-2">
      <strong><?php echo trans('app.futures'); ?></strong>
    </div>
  <?php endif; ?>
  <div class="select-container mr-2 mb-2">
    <?php $selected = $category_id ? $cur_category->getLocaleUrl($is_future) : $urlPrefix; ?>
    <select
      class="select2 form-control category"
      name="category_url"
      <?php if (!$is_future) { echo "disabled"; } ?>
    >
      <option value="<?php echo $urlPrefix; ?>"><?php echo trans('app.select_a_category'); ?></option>
      <?php foreach($categories as $category) : ?>
        <option
          value="<?php echo $category->getLocaleUrl($is_future); ?>"
          <?php if ($category->getLocaleUrl($is_future) == $selected) { echo "selected"; } ?>

        >
          <?php echo $category->locale_name; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="select-container mb-2">
    <?php $selected = $subcategory_id ? $cur_subcategory->getLocaleUrl($is_future) : $subUrlPrefix; ?>
    <select
      class="select2 form-control subcategory"
      name="subcategory_url"
    >
      <option value="<?php echo $subUrlPrefix; ?>"><?php echo trans('app.subcategory'); ?></option>
      <?php foreach($subcategories as $subcategory) : ?>
        <option value="<?php echo $subcategory->getLocaleUrl($is_future); ?>" <?php if ($subcategory->getLocaleUrl($is_future) == $selected) { echo "selected"; } ?>>
          <?php echo $subcategory->locale_name; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
</form>
<div class="card poll-card">
  <div class="card-header">
    <strong><?php echo trans('app.active_polls'); ?></strong>
  </div>
  <div class="card-body">
    <table class="table">
      <?php if ($upcoming_polls->count() > 0) : ?>
      <?php foreach($upcoming_polls as $poll) : ?>
        <tr>
          <td>
            <a href="<?php echo $poll->locale_url; ?>"><?php echo $poll->locale_name; ?></a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php else : ?>
        <tr>
          <td>
            <?php echo trans('app.no_polls'); ?>
          </td>
        </tr>
      <?php endif; ?>
    </table>
  </div>
</div>
<?php echo $upcoming_polls->appends(array_except(Request::query(), 'page1'))->links('pagination::simple-bootstrap-4'); ?>
<div class="card poll-card">
  <div class="card-header with-action">
    <div class="action-header-container">
      <div>
        <strong>
          <?php if ($period == 0) {
            echo trans('app.recent_polls');
          } else if ($period == 1) {
            echo trans('app.all_past_polls');
          } else {
            echo $period;
          }
          ?>
        </strong>
      </div>
      <div>
        <?php $selected = $period; ?>
        <select
          class="select2 form-control period"
          name="period"
        >
          <option value="0" <?php if ($selected == 0) { echo "selected"; } ?>>
            <?php echo trans('app.recent_polls'); ?>
          </option>
          <option value="1" <?php if ($selected == 1) { echo "selected"; } ?>>
            <?php echo trans('app.all_past_polls'); ?>
          </option>
          <?php for($i = $max_year; $i >= $min_year; $i--) : ?>
            <option value="<?php echo $i; ?>" <?php if ($i == $selected) { echo "selected"; } ?>>
              <?php echo $i; ?>
            </option>
          <?php endfor; ?>
        </select>
      </div>
    </div>
  </div>
  <div class="card-body">
    <table class="table">
      <?php if ($past_polls->count() > 0) : ?>
      <?php foreach($past_polls as $poll) : ?>
        <tr>
          <td>
            <a href="<?php echo $poll->locale_url; ?>"><?php echo $poll->locale_name; ?></a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php else : ?>
        <tr>
          <td>
            <?php echo trans('app.no_polls'); ?>
          </td>
        </tr>
      <?php endif; ?>
    </table>
  </div>
</div>
<?php echo $past_polls->appends(array_except(Request::query(), 'page2'))->links('pagination::simple-bootstrap-4'); ?>

