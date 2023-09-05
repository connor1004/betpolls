<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>

<div class="container">
  <?php 
    $banner_settings = (object)[
      'banner_name' => 'top',
      'style' => 'display: inline-block; width: 100%; height: 90px;',
    ];
    require(dirname(__FILE__) . '/widgets/banner.php');
  ?>
  <div class="row">
    <div class="col-12 col-xl-8 col-lg-7">
      <h1><?php echo $page->locale_title; ?></h1>
      <div class="post-content">
        <?php echo $page->locale_content; ?>
      </div>
    </div>
    <div class="col-12 col-xl-4 col-lg-5">
      <?php require(dirname(__FILE__) . '/partials/sidebar-home.php'); ?>
    </div>
  </div>
</div>

</div <?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>