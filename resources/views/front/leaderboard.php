<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>
<div class="container leaderboard-page">
  <?php 
    $banner_settings = (object)[
      'banner_name' => 'top',
      'style' => 'display: inline-block; width: 100%; height: 90px;',
    ];
    require(dirname(__FILE__) . '/widgets/banner.php');
    $not_show_leaderboard = true;
  ?>
  <div class="row">
    <div class="col-12 col-xl-8 col-lg-7">
      <?php require_once(dirname(__FILE__) . '/snippets/leaderboard.php'); ?>
    </div>
    <div class="col-12 col-xl-4 col-lg-5">
      <?php require(dirname(__FILE__) . '/partials/sidebar-secondary.php'); ?>
    </div>
  </div>
</div>
<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>