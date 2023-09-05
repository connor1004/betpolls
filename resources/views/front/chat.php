<?php 
use Illuminate\Support\Facades\Auth;
use App\Facades\Constants;
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
    <div class="col-12 chat-area">
      <?php if (isset($page)) : ?>
        <h1><?php echo $page->locale_title; ?></h1>
      <?php endif; ?>
      <?php if (app('translator')->getLocale() === 'es') : ?>
        <iframe src="https://www6.cbox.ws/box/?boxid=852409&boxtag=8EV0eV&tid=2&tkey=ebe8920c189b13de" width="100%" height="650" allowtransparency="yes" allow="autoplay" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
      <?php else : ?>
        <iframe src="https://www6.cbox.ws/box/?boxid=852409&boxtag=8EV0eV&tid=1&tkey=be6cc2f34788e269" width="100%" height="650" allowtransparency="yes" allow="autoplay" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
      <?php endif; ?>
      <?php if (isset($page)) : ?>
        <div class="mt-2">
          <?php echo $page->locale_content; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <?php 
    $banner_settings = (object)[
      'banner_name' => 'bottom',
      'style' => 'display: inline-block; width: 100%; height: 90px;',
    ];
    require(dirname(__FILE__) . '/widgets/banner.php');
  ?>
</div>
<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>