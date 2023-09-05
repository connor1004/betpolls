<?php
  use Illuminate\Support\Facades\Request;
  use App\Facades\Constants;
  use App\Facades\Utils;
  $errors = Request::session()->get('errors');
  $values = Request::session()->get('values');
?>
<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>
<div class="container">
  <?php if (isset($page)) : ?>
    <h1><?php echo $page->locale_title; ?></h1>
  <?php endif; ?>
  <div class="row mt-8">
    <?php if (!isset($page) || empty($page->locale_content)) : ?>
    <div class="offset-xl-4 offset-lg-3 offset-md-2 col-xl-4 col-lg-6 col-md-8">
    <?php else : ?>
    <div class="col-xl-4 col-lg-6 col-md-8 col-sm-12">
    <?php endif; ?>
      <div class="card">
        <div class="card-header"><?php echo trans('app.register'); ?></div>
        <div class="card-body">
          <form action="<?php echo Utils::localeUrl('register') ?>" method="post">
            
            <div class="form-group">
              <label for="firstname"><?php echo trans('app.firstname'); ?></label>
              <input type="text" name="firstname" id="firstname" placeholder="<?php echo trans('app.firstname'); ?>"
                value="<?php echo isset($values['firstname']) ? $values['firstname'] : ''; ?>" required
                class="form-control<?php if (isset($errors) && $errors->has('firstname')) { echo " is-invalid"; } ?>"
              >
              <?php if (isset($errors) && $errors->has('firstname')) : ?>
                <div class="invalid-feedback">
                  <?php echo $errors->first('firstname') ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="form-group">
              <label for="lastname"><?php echo trans('app.lastname'); ?></label>
              <input type="text" name="lastname" id="lastname" placeholder="<?php echo trans('app.lastname'); ?>"
                value="<?php echo isset($values['lastname']) ? $values['lastname'] : ''; ?>" required
                class="form-control<?php if (isset($errors) && $errors->has('lastname')) { echo " is-invalid"; } ?>"
              >
              <?php if (isset($errors) && $errors->has('lastname')) : ?>
                <div class="invalid-feedback">
                  <?php echo $errors->first('lastname') ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <label for="username"><?php echo trans('app.username'); ?></label>
              <input type="text" name="username" id="username" placeholder="<?php echo trans('app.username'); ?>"
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
              <label for="email"><?php echo trans('app.email'); ?></label>
              <input type="email" name="email" id="email" placeholder="<?php echo trans('app.email'); ?>"
                value="<?php echo isset($values['email']) ? $values['email'] : ''; ?>" required
                class="form-control<?php if (isset($errors) && $errors->has('email')) { echo " is-invalid"; } ?>"
              >
              <?php if (isset($errors) && $errors->has('email')) : ?>
                <div class="invalid-feedback">
                  <?php echo $errors->first('email') ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <label><?php echo trans('app.country'); ?></label>
              <?php $countries = Constants::getCountries(); $selected = isset($values['country']) ? $values['country'] : 'DO'; ?>
              <select class="select2 form-control" name="country" placeholder="<?php echo trans('app.countries'); ?>">
                <?php foreach($countries as $key => $value) : ?>
                  <option value="<?php echo $key; ?>" <?php if ($key === $selected) { echo "selected"; } ?>>
                    <?php echo $value; ?>
                  </option>
                <?php endforeach; ?>
              </select>
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
              <label for="password_confirmation"><?php echo trans('app.confirm_password'); ?></label>
              <input type="password" name="password_confirmation" id="password_confirmation" placeholder="<?php echo trans('app.confirm_password'); ?>"
                value="<?php echo isset($values['password_confirmation']) ? $values['password_confirmation'] : ''; ?>" required min="6"
                class="form-control<?php if (isset($errors) && $errors->has('password_confirmation')) { echo " is-invalid"; } ?>"
              >
              <?php if (isset($errors) && $errors->has('password_confirmation')) : ?>
                <div class="invalid-feedback">
                  <?php echo $errors->first('password_confirmation') ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <div class="g-recaptcha" 
                data-sitekey="<?php echo env('GOOGLE_RECAPTCHA_KEY') ?>">
              </div>
              <?php if (isset($errors) && $errors->has('g-recaptcha-response')) : ?>
                <small class="text-danger">
                  <?php echo $errors->first('g-recaptcha-response') ?>
                </small>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary text-uppercase"><?php echo trans('app.register'); ?></button>
            </div>
            <div class="form-group">
              <a href="<?php echo Utils::localeUrl('login'); ?>"><?php echo trans('app.back') ?></a>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php if (isset($page) && !empty($page->locale_content)) : ?>
    <div class="col-xl-8 col-lg-6 col-md-4 col-sm-12">
      <?php echo $page->locale_content; ?>
    <?php endif; ?>
  </div>
</div>
<script src='https://www.google.com/recaptcha/api.js'></script>
<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>
