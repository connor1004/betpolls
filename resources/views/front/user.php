<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>
<div class="container user-page">
  <?php 
    $banner_settings = (object)[
      'banner_name' => 'top',
      'style' => 'display: inline-block; width: 100%; height: 90px;',
    ];
    require(dirname(__FILE__) . '/widgets/banner.php');
  ?>
  <div class="row">
    <div class="col-12 col-xl-8 col-lg-7" id="result-layout">
      <?php require(dirname(__FILE__) . '/snippets/user-voted-games.php'); ?>
    </div>
    <div class="col-12 col-xl-4 col-lg-5">
      <?php require(dirname(__FILE__) . '/partials/sidebar-secondary.php'); ?>
    </div>
  </div>
</div>
<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>