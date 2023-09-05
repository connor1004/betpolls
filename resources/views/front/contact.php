<?php
  use Illuminate\Support\Facades\Request;
  $errors = Request::session()->get('errors');
  $values = Request::session()->get('values');
  $alert = Request::session()->get('alert');
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
    <div class="col-12 col-xl-8 col-lg-7">
      <h1><?php echo $page->locale_title; ?></h1>
      <div class="post-content">
        <?php echo $page->locale_content; ?>
        <div class="card">
          <div class="card-body">
            <form method="POST" action="">
              <?php if (isset($alert)) : ?>
                <div class="alert alert-<?php echo $alert->status; ?> show" role="alert">
                  <?php echo $alert->message; ?>
                </div>
              <?php endif; ?>
              <div class="form-group">
                <label for="name"><?php echo trans('app.name'); ?></label>
                <input type="text" name="name" id="name" placeholder="<?php echo trans('app.name'); ?>"
                  value="<?php echo isset($values['name']) ? $values['name'] : ''; ?>" required
                  class="form-control<?php if (isset($errors) && $errors->has('name')) { echo " is-invalid"; } ?>"
                >
                <?php if (isset($errors) && $errors->has('name')) : ?>
                  <div class="invalid-feedback">
                    <?php echo $errors->first('name') ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="form-group">
                <label for="phone"><?php echo trans('app.phone'); ?></label>
                <input type="text" name="phone" id="phone" placeholder="<?php echo trans('app.phone'); ?>"
                  value="<?php echo isset($values['phone']) ? $values['phone'] : ''; ?>"
                  class="form-control<?php if (isset($errors) && $errors->has('phone')) { echo " is-invalid"; } ?>"
                >
                <?php if (isset($errors) && $errors->has('phone')) : ?>
                  <div class="invalid-feedback">
                    <?php echo $errors->first('phone') ?>
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
                <label for="subject"><?php echo trans('app.subject'); ?></label>
                <input type="text" name="subject" id="subject" placeholder="<?php echo trans('app.subject'); ?>"
                  value="<?php echo isset($values['subject']) ? $values['subject'] : ''; ?>" required
                  class="form-control<?php if (isset($errors) && $errors->has('subject')) { echo " is-invalid"; } ?>"
                >
                <?php if (isset($errors) && $errors->has('subject')) : ?>
                  <div class="invalid-feedback">
                    <?php echo $errors->first('subject') ?>
                  </div>
                <?php endif; ?>
              </div>
              <div class="form-group">
                <label for="message"><?php echo trans('app.message'); ?></label>
                <textarea type="text" name="message" id="message" placeholder="<?php echo trans('app.message'); ?>"
                  class="form-control<?php if (isset($errors) && $errors->has('subject')) { echo " is-invalid"; } ?>"
                  required rows="5"
                ><?php echo isset($values['subject']) ? $values['subject'] : ''; ?></textarea>
                <?php if (isset($errors) && $errors->has('message')) : ?>
                  <div class="invalid-feedback">
                    <?php echo $errors->first('message') ?>
                  </div>
                <?php endif; ?>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary"><?php echo trans('app.submit'); ?></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-4 col-lg-5">
      <?php require(dirname(__FILE__) . '/partials/sidebar-home.php'); ?>
    </div>
  </div>
</div>

</div <?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>