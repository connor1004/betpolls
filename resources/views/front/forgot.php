<?php
  use Illuminate\Support\Facades\Request;
  use App\Facades\Utils;
  
  $errors = Request::session()->get('errors');
  $values = Request::session()->get('values');
  $alert = Request::session()->get('alert');
?>
<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>
<div class="container">
  <div class="row mt-8">
    <div class="offset-xl-4 offset-lg-3 offset-md-2 col-xl-4 col-lg-6 col-md-8">
      <div class="card">
      <div class="card-header text-uppercase"><?php echo trans('app.forgot_password_quiz'); ?></div>
        <div class="card-body">
          <form action="<?php echo Utils::localeUrl('forgot') ?>" method="post">
            <?php if (isset($alert)) : ?>
              <div class="alert alert-<?php echo $alert->status; ?> show" role="alert">
                <?php echo $alert->message; ?>
              </div>
            <?php endif; ?>
            <div class="form-group">
              <label for="email"><?php echo trans('app.email'); ?></label>
              <input type="text" name="email" id="email" placeholder="<?php echo trans('app.email'); ?>"
                value="" required
                class="form-control<?php if (isset($errors) && $errors->has('email')) { echo " is-invalid"; } ?>"
              >
              <?php if (isset($errors) && $errors->has('email')) : ?>
                <div class="invalid-feedback">
                  <?php echo $errors->first('email') ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary text-uppercase"><?php echo trans('app.submit'); ?></button>
            </div>
            <div class="form-group">
              <a href="<?php echo Utils::localeUrl('login'); ?>"><?php echo trans('app.back') ?></a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>