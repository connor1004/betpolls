<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Post;

class BlogPostController extends AdminController
{
    public function toggleActive($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        if ($post->trashed()) {
            $post->restore();
        } else {
            $post->delete();
        }

        return $post;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $inactive = filter_var($request->input('inactive', false), FILTER_VALIDATE_BOOLEAN);
        $post_type = $request->input('post_type', Post::$POST_TYPE_POST);
        $posts = Post::where(function ($query) use ($search) {
            $query
                ->orWhere('title', 'LIKE', "%$search%")
                ->orWhere('slug', 'LIKE', "%$search%")
                ->orWhere('title_es', 'LIKE', "%$search%")
                ->orWhere('slug_es', 'LIKE', "%$search%");
        });
        if ($inactive) {
            $posts->onlyTrashed();
        }
        $posts->where('post_type', $post_type);
        return $posts->paginate(10);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $posts = Post::where(function ($query) use ($search) {
            $query
                ->orWhere('title', 'LIKE', "%$search%")
                ->orWhere('slug', 'LIKE', "%$search%")
                ->orWhere('title_es', 'LIKE', "%$search%")
                ->orWhere('slug_es', 'LIKE', "%$search%");
        })->limit(10)->get();
        return $posts;
    }

    public function show($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        return $post;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'meta_keywords' => 'required',
            'meta_description' => 'required'
        ]);
        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = str_slug($data['title']);
        }
        if (empty($data['slug_es'])) {
            if (!empty($data['title_es'])) {
                $data['slug_es'] = str_slug($data['title_es']);
            }
        }
        $post = Post::create($data);

        return $post;
    }

    public function update(Request $request, $id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $this->validate($request, [
            'title' => 'required',
            'meta_keywords' => 'required',
            'meta_description' => 'required'
        ]);
        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = str_slug($data['title']);
        }
        if (empty($data['slug_es'])) {
            if (!empty($data['title_es'])) {
                $data['slug_es'] = str_slug($data['title_es']);
            }
        }
        $post->fill($data);
        $post->save();

        return $post;
    }

    public function destroy($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->forceDelete();

        return $post;
    }
}
