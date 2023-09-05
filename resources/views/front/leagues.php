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
      <div id="action-layout" class="mb-4">
        <app-week-selector
          class="week-selector"
          id="leagues-week-selector"
          data-date="<?php echo $start_at; ?>"
          data-locale="<?php echo app('translator')->getLocale(); ?>"
          data-action="<?php echo $league->locale_url; ?>"
        />
      </div>
      <div id="results-layout">
        <?php require(dirname(__FILE__) . '/snippets/league-games.php'); ?>
      </div>
      <?php if (!empty($league->locale_content)) : ?>
        <div class="card widget widget-html">
          <div class="card-header">
            <strong><?php echo $league->locale_name; ?></strong>
            <?php if (isset($socials)) : ?>
              <div class="social-medias">
                <?php if(isset($socials->facebook) && $socials->facebook !== '') :?>
                  <a href="<?php echo $socials->facebook?>">
                    <i class="fa fa-facebook"></i>
                  </a>
                <?php endif; ?>
                <?php if(isset($socials->twitter) && $socials->twitter !== '') :?>
                  <a href="<?php echo $socials->twitter?>">
                    <i class="fa fa-twitter"></i>
                  </a>
                <?php endif; ?>
                <?php if(isset($socials->instagram) && $socials->instagram !== '') :?>
                  <a href="<?php echo $socials->instagram?>">
                    <i class="fa fa-instagram"></i>
                  </a>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <?php echo $league->locale_content; ?>
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