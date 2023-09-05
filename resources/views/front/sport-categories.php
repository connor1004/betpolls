<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>

<div class="container">
  <?php 
    $banner_settings = (object)[
      'banner_name' => 'top',
      'style' => 'display: inline-block; width: 100%; height: 90;',
    ];
    require(dirname(__FILE__) . '/widgets/banner.php');
  ?>
  <div class="row">
    <div class="col-12 col-xl-8 col-lg-7">
      <div id="action-layout" class="mb-4">
        <app-week-selector
          class="week-selector"
          id="leagues-week-selector"
          data-date="<?php echo $start_at; ?>"
          data-locale="<?php echo app('translator')->getLocale(); ?>"
          data-action="<?php echo $sport_category->locale_url ?>"
        />
      </div>
      <div id="results-layout">
        <?php require(dirname(__FILE__) . '/snippets/leagues-groups.php'); ?>
      </div>
      <?php if (!empty($sport_category->locale_content)) : ?>
        <div class="card widget widget-html">
          <div class="card-header">
            <strong><?php echo $sport_category->locale_name; ?></strong>
          </div>
          <div class="card-body">
            <?php echo $sport_category->locale_content; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <div class="col-12 col-xl-4 col-lg-5">
      <?php require(dirname(__FILE__) . '/partials/sidebar-main.php'); ?>
    </div>
  </div>
</div>

<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>