<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>
<div class="container future-page">
  <?php 
    $banner_settings = (object)[
      'banner_name' => 'top',
      'style' => 'display: inline-block; width: 100%; height: 90px;',
    ];
    require(dirname(__FILE__) . '/widgets/banner.php');
  ?>
  <div class="row">
    <div class="col-12 col-xl-8 col-lg-7">
      <?php require_once(dirname(__FILE__) . '/snippets/future.php'); ?>
      <?php if (!empty($page->locale_content)) : ?>
        <div class="card widget widget-html">
          <div class="card-header">
            <strong>
            <?php if ($cur_subcategory) {
              echo $cur_subcategory->locale_name;
            } else if ($cur_category) {
              echo $cur_category->locale_name;
            } else {
              echo trans('app.futures');
            }?>
            </strong>
          </div>
          <div class="card-body">
            <?php echo $page->locale_content; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <div class="col-12 col-xl-4 col-lg-5">
      <?php require(dirname(__FILE__) . '/partials/sidebar-secondary.php'); ?>
    </div>
  </div>
</div>
<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>