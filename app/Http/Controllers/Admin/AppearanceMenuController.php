<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AdminController;
use App\Menu;

class AppearanceMenuController extends AdminController
{
    public function index(Request $request, $menu_type)
    {
        $menu_type = $request->input('menu_type', $menu_type);
        $menu = Menu::with('post')->where('menu_type', $menu_type)->orderBy('display_order')->get();
        return $menu;
    }

    public function store(Request $request, $menu_type)
    {
        $this->validate($request, [
            'url' => 'required|url',
            'title' => 'required',
        ]);
        $data = $request->all();
        $data['menu_type'] = $menu_type;
        $menu = Menu::create($data);
        $menu = Menu::find($menu->id);
        return $menu;
    }

    public function update(Request $request, $menu_type, $id)
    {
        $menu = Menu::findOrFail($id);
        $this->validate($request, [
            'url' => 'required|url',
            'title' => 'required',
        ]);
        $data = $request->all();
        $data['menu_type'] = $menu_type;
        $menu->update($data);
        $menu = Menu::find($id);
        return $menu;
    }

    public function reorder(Request $request)
    {
        $menusData = $request->all();
        if (!$menusData || count($menusData) === 0) {
            return [];
        }

        $index = 0;
        foreach ($menusData as $menuData) {
            $id = $menuData['id'];
            $menu = Menu::find($id);
            $menu->parent_id = $menuData['parent_id'];
            $menu->display_order = $index;
            $index++;
            $menu->save();
        }
        return $menusData;
    }

    public function destroy($id)
    {
        Menu::find($id)->delete();
    }
}
