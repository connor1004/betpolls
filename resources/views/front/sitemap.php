<?xml version="1.0" encoding="UTF-8"?>
<?php
  use App\Facades\Utils;
?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="https://www.w3.org/1999/xhtml">
  <url>
    <loc><?php echo url('/'); ?></loc>
    <xhtml:link rel="alternate" hreflang="es" href="<?php echo url('/es'); ?>" />
  </url>
  <url>
    <loc><?php echo url('/chat'); ?></loc>
    <xhtml:link rel="alternate" hreflang="es" href="<?php echo url('/es/chat'); ?>" />
  </url>
  <url>
    <loc><?php echo url('/login'); ?></loc>
    <xhtml:link rel="alternate" hreflang="es" href="<?php echo url('/es/entrada'); ?>" />
  </url>
  <url>
    <loc><?php echo url('/register'); ?></loc>
    <xhtml:link rel="alternate" hreflang="es" href="<?php echo url('/es/registro'); ?>" />
  </url>
  <url>
    <loc><?php echo url('/forgot'); ?></loc>
    <xhtml:link rel="alternate" hreflang="es" href="<?php echo url('/es/olvido'); ?>" />
  </url>
  <?php foreach($menus as $menu) : ?>
  <url>
    <loc><?php echo $menu->real_url; ?></loc>
    <xhtml:link rel="alternate" hreflang="es" href="<?php echo $menu->real_url_es; ?>" />
  </url>
  <?php endforeach; ?>
  <?php foreach($users as $user) : ?>
  <url>
    <loc><?php echo $user->url; ?></loc>
    <xhtml:link rel="alternate" hreflang="es" href="<?php echo $user->url_es; ?>" />
  </url>
  <?php endforeach; ?>
</urlset>