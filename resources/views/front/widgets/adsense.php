<div
  class="widget adsense-widget <?php echo isset($adsense_settings->cls) ? $adsense_settings->cls : ''?>"
>
  <ins class="adsbygoogle"
    <?php if (isset($adsense_settings->style)) : ?>style="<?php echo $adsense_settings->style; ?>"<?php endif; ?>
    data-ad-client="<?php echo isset($adsense_settings->google_data_ad_client) ? $adsense_settings->google_data_ad_client : '' ?>"
    data-ad-slot="<?php echo isset($adsense_settings->google_data_ad_slot) ? $adsense_settings->google_data_ad_slot : '' ?>"></ins>
</div>