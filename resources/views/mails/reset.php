<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="width: 100%; padding: 0; margin: 0;">

<?php 
  $resetPasswordUrl = $user->reset_password_url;
?>
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta name="x-apple-disable-message-reformatting">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="telephone=no" name="format-detection">
  <title>Betpolls forgot password email</title>
</head>

<body style="width:100%;font-family:'open sans', 'helvetica neue', helvetica, arial, sans-serif;font-size: 16px; padding:0;margin:0;">
  <div style="background-color:#000000; color: #ffffff; margin: 0; padding: 0;">
    <div style="min-width: 280px; max-width: 640px; margin-left: auto; margin-right: auto; padding: 0 20px;">
      <div style="padding: 20px 0; margin: 0;">
        <a href="<?php echo url('/'); ?>" style="font-size: 28px; text-decoration: none; color: #ffffff">BetPolls</a>
      </div>
      <div style="background-color: #ffffff; color: #000000; margin: 0; padding:1px 20px;">
        <p style="display: block; color: #000000">Hi <strong><?php echo $user->firstname; ?></strong>,</p>
        <p style="display: block; color: #000000">Seems like you forgot your password. If this is true, click below to reset your password</p>
        <p style="text-align: center;">
          <a href="<?php echo $resetPasswordUrl; ?>" style="display: inline-block; background-color: #000000; color: #ffffff; padding: 15px 30px; text-decoration: none;">
            Reset Password
          </a>
        </p>
      </div>
      <div style="padding: 20px 0; margin: 0;">
        <span style="color: #ffffff;">&copy;2019 All rights reserved</span>
      </div>
    </div>
  </div>
</body>

</html>