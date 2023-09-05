<?php
  use App\Facades\Options;
  $settings = Options::getSettingsOption();
  $bannerOption = Options::getBannerOption($banner_settings->banner_name);
?>
<?php if (!empty($bannerOption) && $bannerOption->type != 'none') : ?>
<?php if (($bannerOption->type == 'image' && ($bannerOption->main != '' || $bannerOption->small != '')) || ($bannerOption->type == 'ads' && $bannerOption->ads != '')) : ?>
  <div
    class="widget adsense-widget <?php echo isset($banner_settings->cls) ? $banner_settings->cls : ''?>"
  >
    <?php if ($bannerOption->type == 'ads') : ?>
      <div
        class="ads-container"
        <?php if (isset($banner_settings->style)) : ?>style="<?php echo $banner_settings->style; ?>"<?php endif; ?>
      >
        <?php echo $bannerOption->ads; ?>
      </div>
    <?php else : ?>
      <a href="<?php echo $bannerOption->link; ?>">
        <picture>
          <source srcset="<?php echo $bannerOption->small; ?>" media="(max-width: 767px)">
          <img src="<?php echo $bannerOption->main; ?>" alt="top banner">
        </picture>
      </a>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php endif; ?>