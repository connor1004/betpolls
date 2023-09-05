<?php

namespace App\Helpers;

use App\Facades\Options;

class UtilsHelper
{
    public function arrayRemoveEmpty($haystack)
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = $this->arrayRemoveEmpty($haystack[$key]);
            }

            if (empty($haystack[$key])) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }

    public function localeUrl($path)
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (strpos($path, 'chat') === 0) {
                $path = str_replace('chat', 'chat', $path);
            } elseif (strpos($path, 'confirm') === 0) {
                $path = str_replace('confirm', 'confirmado', $path);
            } elseif (strpos($path, 'forgot') === 0) {
                $path = str_replace('forgot', 'olvido', $path);
            } elseif (strpos($path, 'leaderboard') === 0) {
                $path = str_replace('leaderboard', 'tabla-de-posiciones', $path);
            } elseif (strpos($path, 'login') === 0) {
                $path = str_replace('login', 'entrada', $path);
            } elseif (strpos($path, 'logout') === 0) {
                $path = str_replace('logout', 'cerrar-session', $path);
            } elseif (strpos($path, 'pending-confirmation') === 0) {
                $path = str_replace('pending-confirmation', 'confirmacion-pendiente', $path);
            } elseif (strpos($path, 'profile') === 0) {
                $path = str_replace('profile', 'perfil', $path);
            } elseif (strpos($path, 'register') === 0) {
                $path = str_replace('register', 'registro', $path);
            } elseif (strpos($path, 'reset') === 0) {
                $path = str_replace('reset', 'reiniciar', $path);
            } elseif (strpos($path, 'send-confirmation') === 0) {
                $path = str_replace('send-confirmation', 'enviar-confirmacion', $path);
            } elseif (strpos($path, 'users') === 0) {
                $path = str_replace('users', 'usuarios', $path);
            } elseif (strpos($path, 'top-picks') === 0) {
                $path = str_replace('top-picks', 'mejores-picks', $path);
            }
        }
        return $locale === 'en' ? url($path) : url("{$locale}/{$path}");
    }

    public function filterTitle($title) {
        $settings = Options::getSettingsOption();
        if (!isset($settings->title)) {
            return $title;
        }
        
        if ($title === $settings->title || empty($title)) {
            return $settings->title;
        } else {
            return $title;
        }
    }
}
