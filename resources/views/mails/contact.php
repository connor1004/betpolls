<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="width: 100%; padding: 0; margin: 0;">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta name="x-apple-disable-message-reformatting">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="telephone=no" name="format-detection">
  <title>Betpolls contact request</title>
</head>

<body style="width:100%;font-family:'open sans', 'helvetica neue', helvetica, arial, sans-serif;font-size: 16px; padding:0;margin:0;">
  <div style="background-color:#000000; color: #ffffff; margin: 0; padding: 0;">
    <div style="min-width: 280px; max-width: 640px; margin-left: auto; margin-right: auto; padding: 0 20px;">
      <div style="padding: 20px 0; margin: 0;">
        <a href="<?php echo url('/'); ?>" style="font-size: 28px; text-decoration: none; color: #ffffff">BetPolls</a>
      </div>
      <div style="background-color: #ffffff; color: #000000; margin: 0; padding:1px 20px;">
        <p style="display: block; color: #000000">[Name]: <?php echo $contact->name; ?></p>
        <p style="display: block; color: #000000">[Email]: <?php echo $contact->email; ?></p>
        <p style="display: block; color: #000000">[Phone]: <?php echo $contact->phone; ?></p>
        <p style="display: block; color: #000000">[Subject]: <?php echo $contact->subject; ?></p>
        <p style="display: block; color: #000000">[Message]: </p>
        <p style="display: block; color: #000000"><?php echo $contact->message; ?> </p>
      </div>
      <div style="padding: 20px 0; margin: 0;">
        <span style="color: #ffffff;">&copy;2019 All rights reserved</span>
      </div>
    </div>
  </div>
</body>

</html>