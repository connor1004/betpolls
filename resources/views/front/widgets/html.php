<div class="card widget widget-html">
  <div class="card-header">
    <?php echo $html_settings->title; ?>
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
    <?php echo $html_settings->content; ?>
  </div>
</div>