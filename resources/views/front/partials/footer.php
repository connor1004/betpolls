<?php
  use App\Facades\Data;
  use App\Menu;
  use App\Facades\Options;
  $socials = Options::getSocialMediaLinkOption();
?>
  </div>
    <div class="site-footer">
      <div class="container">
        <div class="d-flex justify-content-between flex-wrap align-items-center">
          <div>
            <ul class="footer-menu">
              <?php $menus = Data::getMenus(Menu::$MENU_TYPE_FOOTER);
                foreach ($menus as $menu) : 
              ?>
                <li>
                  <a href="<?php echo $menu->locale_url ?>">
                    <?php echo $menu->locale_title; ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php if (isset($socials)) : ?>
            <div class="social-menu">
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
          <div>
            <div class="d-flex flex-wrap align-items-center">
              <div class="credit">
                @2020 <?php echo trans('app.all_rights_reserved'); ?> Betpolls.com
              </div>
            </div>
          </div>
        </div>
      </div>
      <a href="<?php echo app('translator')->getLocale() === 'es' ? '/es/chat' : '/chat' ?>">
        <div class="chat-icon"></div>
      </a>
    </div>
  </div>
  <script src="<?php echo url('/dist/front.js') ?>"></script>
</body>
</html>