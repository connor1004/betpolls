<?php
  use App\Facades\Data;
  use App\Facades\Options;
  $socials = Options::getSocialMediaLinkOption();
?>
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
      <?php
        $top_picks_settings = (object)[
          'title' => trans('app.todays_top_picks'),
          'picks' => Data::getTopPicks()
        ];
        require(dirname(__FILE__) . '/widgets/top-picks.php');
      ?>
      <?php 
        $banner_settings = (object)[
          'banner_name' => 'bottom',
          'style' => 'display: inline-block; width: 100%; height: 90px;',
        ];
        require(dirname(__FILE__) . '/widgets/banner.php');
      ?>
      <?php
        if (isset($homepage)) {
          $html_settings = (object)[
            'title' => "<h1>{$homepage->locale_title}</h1>",
            'content' => $homepage->locale_content,
            'socials' => $socials
          ];
          require(dirname(__FILE__) . '/widgets/html.php');
        }
      ?>
    </div>
    <div class="col-12 col-xl-4 col-lg-5">
      <?php require(dirname(__FILE__) . '/partials/sidebar-home.php'); ?>
    </div>
  </div>
</div>

</div <?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>