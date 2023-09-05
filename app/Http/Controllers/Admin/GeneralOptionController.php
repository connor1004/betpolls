<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\Options;

class GeneralOptionController extends AdminController
{
    public function index(Request $request, $name)
    {
        $option = Options::getOption($name, []);
        if ($name == 'settings') {
            
        }
        return $option;
    }

    public function update(Request $request, $name)
    {
        if ($name == 'settings') {
            Options::setOption('settings', $request->except(['ads_txt', 'robots_txt']));
            $file = fopen('ads.txt', 'w');
            fwrite($file, $request->input('ads_txt'));
            fclose($file);
            $file = fopen('robots.txt', 'w');
            fwrite($file, $request->input('robots_txt'));
            fclose($file);
        } elseif ($name === 'home_leagues') {
            $data = $request->all();
            for ($i = 0, $ni = count($data); $i < $ni; $i++) {
                $data[$i] = intval($data[$i]);
            }
            Options::setOption($name, $data);
        } else {
            Options::setOption($name, $request->all());
        }
        $option = Options::getOption($name, []);
        return $option;
    }
}
