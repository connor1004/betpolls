<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Post;
use App\Contact;
use App\Mails\ContactMail;
use App\Facades\Options;

class PostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        // $this->middleware('auth');
    }

    public function show($slug)
    {
        $locale = app('translator')->getLocale();
        $page = null;
        if ($locale === 'es') {
            $page = Post::where('slug_es', $slug)->first();
        }
        if (!$page) {
            $page = Post::where('slug', $slug)->firstOrFail();
        }

        $view = "front.{$page->post_type}";
        return view($view, [
            'page' => $page
        ]);
    }

    public function store(Request $request, $slug)
    {
        $locale = app('translator')->getLocale();
        $page = null;
        if ($locale === 'es') {
            $page = Post::where('slug_es', $slug)->first();
        }
        if (!$page) {
            $page = Post::where('slug', $slug)->firstOrFail();
        }

        if ($page->post_type === Post::$POST_TYPE_CONTACT) {
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => 'required|max:255',
                'email' => 'required|max:255',
                'subject' => 'required|max:255',
                'message' => 'required'
            ]);
    
            if ($validator->fails()) {
                $request->session()->flash('errors', $validator->errors());
                $request->session()->flash('values', $request->all());
                return redirect($slug);
            }
            $contact = Contact::create($data);
            $settings = Options::getSettingsOption();
            try {
                if ($contact) {
                    Mail::to($settings->contacts)->send(new ContactMail($contact));
                    $request->session()->flash('alert', (object)[
                        'status' => 'success',
                        'message' => trans('app.your_request_has_been_sent_successfully')
                    ]);
                }
            } catch (\Exception $e) {
                $request->session()->flash('alert', (object)[
                    'status' => 'danger',
                    'message' => trans('app.unable_to_process_your_request')
                ]);
            }
        }
        if ($locale === 'es') {
            return redirect('es/'.$slug);
        }
        return redirect($slug);
    }
}
