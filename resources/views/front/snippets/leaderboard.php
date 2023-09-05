<?php
  namespace App;
  use Illuminate\Support\Facades\Request;
  use App\Facades\Constants;
  use App\Leaderboard;
  use App\Facades\Utils;
?>
<div class="card leaderboard-card">
  <div id="action-layout" class="card-header">
    <div>
      <div>
        <strong><?php echo trans('app.leaderboard'); ?></strong>
      </div>
      <div>
        <form action="<?php echo Utils::localeUrl('leaderboard'); ?>" method="get">
          <div class="row">
            <div class="col-12 col-md-3 col-sm-6 my-1">
              <?php $selected = $type; ?>
              <select
                class="select2 form-control"
                name="type"
                <?php if ($period_type === Leaderboard::$PERIOD_TYPE_RANKING) : ?>disabled<?php endif; ?>
              >
                <option value="0"><?php echo trans('app.type'); ?></option>
                <option value="1" <?php if ($selected == 1) { echo "selected"; } ?>><?php echo trans('app.main_poll'); ?></option>
                <option value="2" <?php if ($selected == 2) { echo "selected"; } ?>><?php echo trans('app.event_poll'); ?></option>
                <option value="3" <?php if ($selected == 3) { echo "selected"; } ?>><?php echo trans('app.future_poll'); ?></option>
              </select>
            </div>
            <div class="col-12 col-md-3 col-sm-6 my-1">
              <?php $selected = $sport_category_id; ?>
              <select
                class="select2 form-control"
                name="sport_category_id"
                <?php if ($period_type === Leaderboard::$PERIOD_TYPE_RANKING) : ?>disabled<?php endif; ?>
              >
                <option value="0"><?php echo trans('app.select_a_category'); ?></option>
                <?php foreach($sport_categories as $sport_category) : ?>
                  <option value="<?php echo $sport_category->id; ?>" <?php if ($sport_category->id == $selected) { echo "selected"; } ?>>
                    <?php echo app('translator')->getLocale() === 'es' ? $sport_category->name_es : $sport_category->name; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 col-md-3 col-sm-6 my-1">
              <?php $selected = $league_id; ?>
              <select
                class="select2 form-control"
                name="league_id"
                <?php if ($period_type === Leaderboard::$PERIOD_TYPE_RANKING) : ?>disabled<?php endif; ?>
              >
                <option value="0"><?php echo trans('app.select_a_league'); ?></option>
                <?php foreach($leagues as $league) : ?>
                  <option value="<?php echo $league->id; ?>" <?php if ($league->id == $selected) { echo "selected"; } ?>>
                    <?php echo $league->name; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-2 col-sm-4 my-1">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="dynamic_period" name="dynamic_period" value="true"
                  <?php if ($dynamic_period) : ?>checked<?php endif; ?>
                >
                <label class="custom-control-label" for="dynamic_period"><?php echo trans('app.period'); ?></label>
              </div>
            </div>
            <div class="col-12 col-md-4 col-sm-8 my-1 static-period-layout">
              <?php $period_types = Constants::getSecondPeriodTypes(); $selected = $period_type; ?>
              <select class="select2 form-control" name="period_type">
                <?php foreach($period_types as $key => $value) : ?>
                  <option value="<?php echo $key; ?>" <?php if ($key == $selected) { echo "selected"; } ?>>
                    <?php echo $value; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 col-md-4 col-sm-8 my-1 dynamic-period-layout">
              <app-daterange-picker
                data-locale="<?php echo app('translator')->getLocale(); ?>"
                data-start-name="start_at"
                data-end-name="end_at"
                data-start-date="<?php echo $start_at; ?>"
                data-end-date="<?php echo $end_at; ?>"
                data-cancel-label="<?php echo trans('app.cancel'); ?>"
                data-apply-label="<?php echo trans('app.apply'); ?>"
              />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div id="results-layout" class="card-body">
    <?php require(dirname(__FILE__) . '/leaderboard-table.php'); ?>
  </div>
</div>