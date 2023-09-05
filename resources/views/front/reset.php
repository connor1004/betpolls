<?php
  use Illuminate\Support\Facades\Request;
  // use App\Facades\Constants;
  use App\Facades\Utils;
?>
<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>
<div class="container">
  <div class="row mt-8">
    <div class="offset-xl-4 offset-lg-3 offset-md-2 col-xl-4 col-lg-6 col-md-8">
      <div class="card">
      <div class="card-header text-uppercase"><?php echo trans('app.reset_password'); ?></div>
        <div class="card-body">
          <form action="<?php echo Utils::localeUrl('reset') ?>" method="post">
            
            <?php if (isset($alert)) : ?>
              <div class="alert alert-<?php echo $alert->status; ?> show" role="alert">
                <?php echo $alert->message; ?>
              </div>
            <?php else: ?>
              <?php if (isset($user) && $user) : ?>
                <div class="form-group">
                  <label for="username"><?php echo trans('app.email'); ?></label>
                  <div class="form-control">
                    <?php echo $user->email; ?>
                  </div>
                </div>
              <?php endif; ?>
              <input type="hidden" name="code" value="<?php echo Request::input('code'); ?>" />
              <div class="form-group">
                <label for="password"><?php echo trans('app.password'); ?></label>
                <input type="password" name="password" id="password" placeholder="<?php echo trans('app.password'); ?>"
                  value="<?php echo Request::input('password'); ?>" required min="6"
                  class="form-control<?php if (isset($errors) && $errors->has('password')) { echo " is-invalid"; } ?>"
                >
                <?php if (isset($errors) && $errors->has('password')) : ?>
                  <div class="invalid-feedback">
                    <?php echo $errors->first('password') ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-primary text-uppercase"><?php echo trans('app.reset_password'); ?></button>
              </div>

            <?php endif; ?>

            <div class="form-group">
              <a href="<?php echo url('login'); ?>"><?php echo trans('app.back') ?></a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>