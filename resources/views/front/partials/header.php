<?php
  use Illuminate\Support\Facades\Auth;
  use App\Facades\Data;
  use App\Facades\Utils;
  use App\Facades\Options;

  $page_title = '';
  $page_keywords = '';
  $page_description = '';
  $page_canonical_link = '';
  if(isset($page)) {
    if (isset($page->locale_title) && !empty($page->locale_title)) {
      $page_title = $page->locale_title;
    } else if(isset($page->title) && !empty($page->title)) {
      $page_title = $page->title;
    }

    if (isset($page->locale_meta_keywords) && !empty($page->locale_meta_keywords)) {
      $page_keywords = $page->locale_meta_keywords;
    } else if(isset($page->meta_keywords) && !empty($page->meta_keywords)) {
      $page_keywords = $page->meta_keywords;
    }

    if (isset($page->locale_meta_description) && !empty($page->locale_meta_description)) {
      $page_description = $page->locale_meta_description;
    } else if(isset($page->meta_description) && !empty($page->meta_description)) {
      $page_description = $page->meta_description;
    }
  }
  $settings = Options::getSettingsOption();

  $locale = app('translator')->getLocale();
  if ($locale != 'es') {
    $locale = 'en';
  }
?>
<!DOCTYPE html>
<html lang="<?php echo $locale; ?>">
<head>
  <?php
    if (isset($settings->analytics_code) && $settings->analytics_code != '') {
      echo $settings->analytics_code;
    }
  ?>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="alternate" href="<?php echo url('/es'); ?>" hreflang="es" />
  <link rel="alternate" href="<?php echo url('/'); ?>" hreflang="en" />
  <title><?php echo Utils::filterTitle($page_title); ?></title>
  <?php if (isset($settings->verify_tag) && $settings->verify_tag != '') : ?>
    <meta name="google-site-verification" content="<?php echo $settings->verify_tag; ?>">
  <?php endif; ?>
  <meta name="description" content="<?php echo $page_description; ?>">
  <meta name="keywords" content="<?php echo $page_keywords; ?>">
  <?php if (isset($tab) || !empty($_REQUEST)) : ?>
    <meta name="robots" content="noindex, nofollow">
  <?php endif; ?>
  <?php if (isset($canonical_link) && !empty($canonical_link)) : ?>
    <link rel="canonical" href="<?php echo $canonical_link; ?>">
  <?php endif; ?>
  <link rel="apple-touch-icon" sizes="57x57" href="<?php echo url('/media/apple-icon-57x57.png'); ?>">
  <link rel="apple-touch-icon" sizes="60x60" href="<?php echo url('/media/apple-icon-60x60.png'); ?>">
  <link rel="apple-touch-icon" sizes="72x72" href="<?php echo url('/media/apple-icon-72x72.png'); ?>">
  <link rel="apple-touch-icon" sizes="76x76" href="<?php echo url('/media/apple-icon-76x76.png'); ?>">
  <link rel="apple-touch-icon" sizes="114x114" href="<?php echo url('/media/apple-icon-114x114.png'); ?>">
  <link rel="apple-touch-icon" sizes="120x120" href="<?php echo url('/media/apple-icon-120x120.png'); ?>">
  <link rel="apple-touch-icon" sizes="144x144" href="<?php echo url('/media/apple-icon-144x144.png'); ?>">
  <link rel="apple-touch-icon" sizes="152x152" href="<?php echo url('/media/apple-icon-152x152.png'); ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo url('/media/apple-icon-180x180.png'); ?>">
  <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo url('/media/android-icon-192x192.png'); ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo url('/media/favicon-32x32.png'); ?>">
  <link rel="icon" type="image/png" sizes="96x96" href="<?php echo url('/media/favicon-96x96.png'); ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo url('/media/favicon-16x16.png'); ?>">
  <link rel="manifest" href="<?php echo url('/media/manifest.json'); ?>">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="<?php echo url('/media/ms-icon-144x144.png'); ?>">
  <meta name="theme-color" content="#ffffff">
  
  <link rel="stylesheet" type="text/css" href="<?php echo url('/dist/front.css'); ?>">
</head>
<body class="<?php if(isset($page) && isset($page->page_class)) : echo $page->page_class; endif ?>">
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <script src="<?php echo url('/dist/vendors~admin~front.js') ?>"></script>
  <script src="<?php echo url('/dist/vendors~front.js') ?>"></script>
  
  <div class="site">
    <div class="site-header">
      <app-navigation class="navigation">
        <a class="navigation-brand" href="<?php echo app('translator')->getLocale() === 'en' ? url('/') : url('/es'); ?>">
          <img src="<?php echo url('/media/logo.png'); ?>" alt="Betpolls">
        </a>
        <button class="navigation-toggler">
          <span class="navigation-toggler-item"></span>
          <span class="navigation-toggler-item"></span>
          <span class="navigation-toggler-item"></span>
        </button>
        <div class="navigation-main">
          <ul class="navigation-nav">
            <?php 
              $menus = Data::getMenus();

              foreach($menus as $menu) :
                if ($menu->burger_menu == 1) :
            ?>
              <li
                class="<?php
                  echo $menu->children->count() > 0 ? "has-submenu " : "";
                  echo $menu->top_menu == 1 ? "" : "hide-menu d-lg-none";
                ?>"
              >
                <!-- <a href="<?php //echo $menu->children->count() > 0 ? '#' : $menu->locale_url; ?>"> -->
                <a href="<?php echo $menu->locale_url; ?>">
                  <?php if (!empty($menu->league_id) && $menu->league) : ?>
                    <img class="logo d-inline d-lg-none" src="<?php echo $menu->league->logo; ?>" />
                  <?php endif; ?>
                  <?php echo $menu->locale_title; ?>
                </a>
                <?php if ($menu->children->count() > 0) : ?>
                  <ul class="navigation-submenu">
                    <?php foreach($menu->children as $sub_menu) : ?>
                      <?php if ($sub_menu->burger_menu == 1) : ?>
                      <li>
                        <a href="<?php echo $sub_menu->locale_url; ?>">
                          <?php if (empty($sub_menu->icon_url)) : ?>
                            <?php if (!empty($sub_menu->league_id) && $sub_menu->league) : ?>
                              <img class="logo" src="<?php echo $sub_menu->league->logo; ?>" />
                            <?php endif; ?>
                          <?php else : ?>
                            <img class="logo" src="<?php echo $sub_menu->icon_url; ?>" />
                          <?php endif; ?>
                          <?php echo $sub_menu->locale_title; ?>
                        </a>
                      </li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </li>
            <?php
                endif;
              endforeach;
            ?>
            <li class="has-submenu d-block d-lg-none">
              <a><?php echo trans('app.user'); ?></a>
              <ul class="navigation-submenu">
                <?php if (!Auth::check()) : ?>
                <li>
                  <a href="<?php echo Utils::localeUrl('login'); ?>">
                    <span class="fa fa-sign-in"></span> <?php echo trans('app.login'); ?>
                  </a>
                </li>
                <li>
                  <a href="<?php echo Utils::localeUrl('register'); ?>">
                    <span class="fa fa-sign-in"></span> <?php echo trans('app.register'); ?>
                  </a>
                </li>
                <?php else: $user = Auth::user(); ?>
                <li>
                  <a href="<?php echo Utils::localeUrl('logout'); ?>">
                    <span class="fa fa-sign-out"></span> <?php echo trans('app.logout'); ?>
                  </a>
                </li>
                <li>
                  <a href="<?php echo Utils::localeUrl('profile'); ?>">
                    <span class="fa fa-user-circle-o"></span> <?php echo $user->firstname; ?>
                  </a>
                </li>
                <?php endif; ?>
                <li>
                  <a href="<?php echo Utils::localeUrl('chat'); ?>">
                    <span class="fa fa-wechat"></span> <?php echo trans('app.chat'); ?>
                  </a>
                </li>
              </ul>
            </li>
            <li class="has-submenu d-block d-lg-none">
              <a><?php echo trans('app.language'); ?></a>
              <ul class="navigation-submenu navigation-submenu-small">
                <li>
                  <a href="<?php echo url('/'); ?>">
                    <span class="flag-icon flag-icon-us"></span> English
                  </a>
                </li>
                <li>
                  <a href="<?php echo url('es'); ?>">
                  <span class="flag-icon flag-icon-es"></span> Español
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
        <div class="navigation-side">
          <ul class="navigation-nav">
            <li class="has-submenu">
              <a><span class="fa fa-user-circle-o fa-2x"></span></a>
              <ul class="navigation-submenu">
                <?php if (!Auth::check()) : ?>
                <li>
                  <a href="<?php echo Utils::localeUrl('login'); ?>">
                    <span class="fa fa-sign-in"></span> <?php echo trans('app.login'); ?>
                  </a>
                </li>
                <li>
                  <a href="<?php echo Utils::localeUrl('register'); ?>">
                    <span class="fa fa-sign-in"></span> <?php echo trans('app.register'); ?>
                  </a>
                </li>
                <?php else: $user = Auth::user(); ?>
                <li>
                  <a href="<?php echo Utils::localeUrl('logout'); ?>">
                    <span class="fa fa-sign-out"></span> <?php echo trans('app.logout'); ?>
                  </a>
                </li>
                <li>
                  <a href="<?php echo Utils::localeUrl('profile'); ?>">
                    <span class="fa fa-user-circle-o"></span> <?php echo $user->firstname; ?>
                  </a>
                </li>
                <?php endif; ?>
                <li>
                  <a href="<?php echo Utils::localeUrl('chat'); ?>">
                    <span class="fa fa-wechat"></span> <?php echo trans('app.chat'); ?>
                  </a>
                </li>
              </ul>
            </li>
            <li class="has-submenu">
              <a>
                <span class="fa fa-globe fa-2x"></span>
              </a>
              <ul class="navigation-submenu">
                <li>
                  <a href="<?php echo url('/'); ?>">
                    <span class="flag-icon flag-icon-us"></span> English
                  </a>
                </li>
                <li>
                  <a href="<?php echo url('es'); ?>">
                  <span class="flag-icon flag-icon-es"></span> Español
                  </a>
                </li>
              </ul>
            </li>
            <li class="d-none d-lg-block">
              <button class="burger-toggler">
                <span class="burger-toggler-item"></span>
                <span class="burger-toggler-item"></span>
                <span class="burger-toggler-item"></span>
              </button>
            </li>
          </ul>
        </div>

        <div class="navigation-more d-none">
          <ul>
            <?php 
              $menus = Data::getMenus();

              foreach($menus as $menu) :
                if ($menu->burger_menu == 1) :
            ?>
              <li>
              <!-- <li class="<?php //echo $menu->children->count() > 0 ? "has-submenu" : "" ?>"> -->
                <!-- <a href="<?php //echo $menu->children->count() > 0 ? '#' : $menu->locale_url; ?>"> -->
                <a href="<?php echo $menu->locale_url; ?>">
                  <?php if (empty($menu->icon_url)) : ?>
                    <?php if (!empty($menu->league_id) && $menu->league) : ?>
                      <img class="logo d-inline" src="<?php echo $menu->league->logo; ?>" />
                    <?php endif; ?>
                  <?php else : ?>
                    <img class="logo d-inline" src="<?php echo $menu->icon_url; ?>" />
                  <?php endif; ?>
                  <?php echo $menu->locale_title; ?>
                </a>
                <?php if ($menu->children->count() > 0) : ?>
                  <ul class="navigation-submenu">
                    <?php foreach($menu->children as $sub_menu) : ?>
                      <?php if ($sub_menu->burger_menu == 1) : ?>
                      <li>
                        <a href="<?php echo $sub_menu->locale_url; ?>">
                          <?php if (empty($sub_menu->icon_url)) : ?>
                            <?php if (!empty($sub_menu->league_id) && $sub_menu->league) : ?>
                              <img class="logo" src="<?php echo $sub_menu->league->logo; ?>" />
                            <?php endif; ?>
                          <?php else : ?>
                            <img class="logo d-inline" src="<?php echo $sub_menu->icon_url; ?>" />
                          <?php endif; ?>
                          <?php echo $sub_menu->locale_title; ?>
                        </a>
                      </li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </li>
            <?php
                endif;
              endforeach;
            ?>
          </ul>
        </div>
        
      </app-navigation>
    </div>
    <div class="site-main">