<?php require_once(dirname(__FILE__) . '/partials/header.php'); ?>
<div class="container">
  <div>
    <div class="alert alert-success" role="alert">
      <h4 class="alert-heading"><?php echo trans('app.email_address_verification_needed'); ?></h4>
      <p><?php echo trans('app.please_verify_the_email_address_for'); ?> <?php echo $user->email; ?>.</p>
    </div>
    <p>
      <a href="<?php echo url('send-confirmation'); ?>" class="btn btn-primary">
        <?php echo trans('app.resend') ?>
      </a>
    </p>
  </div>
</div
<?php require_once(dirname(__FILE__) . '/partials/footer.php'); ?>