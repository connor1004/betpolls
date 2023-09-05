<?php

namespace App\Helpers;

use App\Option;

class OptionsHelper
{
    private $options = [];

    public function getSettingsOption()
    {
        $settings = $this->getOption('settings', []);
        array_merge([
            'verify_tag' => '',
            'analytics_code' => '',
            'google_analytics' => '',
            'title' => 'Betpolls',
            'meta_keywords' => '',
            'meta_description' => '',
            'ads_txt' => '',
            'robots_txt' => '',
        ], $settings);
        return (object) $settings;
    }

    public function getSocialMediaLinkOption() {
        $socials = $this->getOption('social-medias', []);
        array_merge([
            'facebook',
            'instagram',
            'twitter'
        ], $socials);
        return (object) $socials;
    }

    public function getBannersOption()
    {
        $banners = $this->getOption('banners', []);
        array_merge([
            'top_type' => '',
            'top_main' => '',
            'top_small' => '',
            'top_ads' => '',
            'top_link' => '',
            'middle_type' => '',
            'middle_main' => '',
            'middle_small' => '',
            'middle_ads' => '',
            'middle_link' => '',
            'bottom_type' => '',
            'bottom_main' => '',
            'bottom_small' => '',
            'bottom_ads' => '',
            'bottom_link' => '',
            'side_type' => '',
            'side_main' => '',
            'side_small' => '',
            'side_ads' => '',
            'side_link' => ''
        ], $banners);
        return (object) $banners;
    }

    public function getBannerOption($name) {
        $banners = $this->getOption('banners', []);
        if (empty($banners[$name.'_type'])) {
            return false;
        }
        return (object)[
            'type' => $banners[$name.'_type'],
            'main' => $banners[$name.'_main'],
            'small' => $banners[$name.'_small'],
            'ads' => $banners[$name.'_ads'],
            'link' => $banners[$name.'_link'] ?? ''
        ];
    }

    public function getOption($name, $default = false)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        $option = Option::where('name', $name)->first();
        if ($option == null) {
            $this->options[$name] = $default;
            return $default;
        }
        $value = $option->value;
        $data = @unserialize($value);
        $value = $data === false ? $value : $data;

        if ($name == 'settings') {
            $file = fopen('ads.txt', 'r');
            if ($file) {
                if (filesize('ads.txt') > 0) {
                    $value['ads_txt'] = fread($file, filesize('ads.txt'));
                }
                else {
                    $value['ads_txt'] = '';
                }
                fclose($file);
            }
            $file = fopen('robots.txt', 'r');
            if ($file) {
                if (filesize('robots.txt') > 0) {
                    $value['robots_txt'] = fread($file, filesize('robots.txt'));
                }
                else {
                    $value['robots_txt'] = '';
                }
                fclose($file);
            }
        }

        $this->options[$name] = $value;
        return $value;
    }

    public function setOption($name, $value)
    {
        $option = Option::where('name', $name)->first();
        $data = is_array($value) ? @serialize($value) : $value;
        if ($option == null) {
            $option = Option::create([
                'name' => $name,
                'value' => $data,
            ]);
        } else {
            $option->fill(['value' => $data]);
        }

        return $option->save();
    }

    public function removeOption($name)
    {
        $option = Option::where('name', $name)->first();
        if ($option) {
            return $option->delete();
        }

        return false;
    }

    public function getNamespaceOption($namespace, $name, $default = false)
    {
        $field = "{$namespace}_{$name}";

        return $this->getOption($field, $default);
    }

    public function setNamespaceOption($namespace, $name, $value)
    {
        $field = "{$namespace}_{$name}";

        return $this->setOption($field, $value);
    }

    public function removeNamespaceOption($namespace, $name)
    {
        $field = "{$namespace}_{$name}";

        return $this->removeOption($field);
    }
}
