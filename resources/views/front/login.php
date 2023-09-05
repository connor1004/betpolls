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
      <div class="card-header text-uppercase"><?php echo trans('app.login'); ?></div>
        <div class="card-body">
          <form method="post">
            <?php if ($alert && $alert->status == 'danger') : ?>
              <div class="alert alert-danger">
                <?php echo $alert->message; ?>
              </div>
            <?php endif; ?>
            <div class="form-group">
              <label for="username"><?php echo trans('app.username'); ?></label>
              <input type="text" name="username" id="username" placeholder="<?php echo trans('app.username_or_email'); ?>"
                value="<?php echo isset($values['username']) ? $values['username'] : ''; ?>" required
                class="form-control<?php if (isset($errors) && $errors->has('username')) { echo " is-invalid"; } ?>"
              >
              <?php if (isset($errors) && $errors->has('username')) : ?>
                <div class="invalid-feedback">
                  <?php echo $errors->first('username') ?>
                </div>
              <?php endif; ?>
            </div>
  
            <div class="form-group">
              <label for="password"><?php echo trans('app.password'); ?></label>
              <input type="password" name="password" id="password" placeholder="<?php echo trans('app.password'); ?>"
                value="<?php echo isset($values['password']) ? $values['password'] : ''; ?>" required min="6"
                class="form-control<?php if (isset($errors) && $errors->has('password')) { echo " is-invalid"; } ?>"
              >
              <?php if (isset($errors) && $errors->has('password')) : ?>
                <div class="invalid-feedback">
                  <?php echo $errors->first('password') ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary text-uppercase"><?php echo trans('app.login'); ?></button>
            </div>
            <div class="form-group">
              <a href="<?php echo Utils::localeUrl('register'); ?>"><?php echo trans('app.register') ?></a>
              |
              <a href="<?php echo Utils::localeUrl('forgot'); ?>"><?php echo trans('app.forgot_password_quiz') ?></a>
            </div>
            <div class="form-group">
              
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>