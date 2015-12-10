<?php

use Laracasts\Utilities\JavaScript\Facades\JavaScript;
use Symfony\Component\Filesystem\Filesystem;

class BaseController extends Controller {

    protected $filesystem;
    public $settings;
    public $portfolio;
    protected $banner = FALSE;

    public function __construct(Setting $settings = NULL, Portfolio $portfolio = NULL, Filesystem $filesystem = NULL) {
        $this->settings   = ($settings == NULL) ? Setting::first() : $settings;
        $this->portfolio  = ($portfolio == NULL) ? Portfolio::all() : $portfolio;
        $this->filesystem = ($filesystem == NULL) ? new Filesystem : $filesystem;
        \View::share('settings', $this->settings);
    }

    public function show($array = NULL) {
        $portfolios      = Portfolio::published()->orderByOrder()->get();
        $portfolio_links = array();
        if ($this->settings->theme == FALSE) {
            if ($portfolios) {
                foreach ($portfolios as $key => $portfolio) {
                    $portfolio_links[$portfolio->title] = $portfolio->slug;
                }
            }
            $static_menu_items = array('About Page' => '/about', 'Contact Page' => '/contact', 'Blog' => '/posts', 'All Projects' => '/all_projects', 'Home' => '/index',);

        }
        else {
            $static_menu_items = array('Our Company' => '/about', 'Portfolio' => '/portfolio', 'News & Awards' => '/news_awards', 'Builder’s Notebook' => '/posts', 'Contact Us' => '/contact');
        }
        $shared_links = array_merge($portfolio_links, $static_menu_items);

        View::share('shared_links', $shared_links);
        View::share('portfolio_links', $portfolio_links);

        //links for the top nav
        $top_menu_items = array(
          'Home' => '/',
          'Portfolios' => '/about#',
          'About Page' => '/about',
          'Contact Page' => '/contact',
        );
        View::share('top_links', $top_menu_items);
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout() {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    public function json_response($status, $message, $data, $code) {
        return Response::json(['status' => $status, 'message' => $message, 'data' => $data], $code);
    }

    public function respond($results, $view, $view_options, $message = NULL) {
        if (Request::format() == 'html') {
            if (!$results) {
                return View::make('404');
            }

            return View::make($view, $view_options);
        }
        else {
            if (!$results) {
                return Response::json(NULL, 404);
            }

            return Response::json(array('data' => $results->toArray(), 'status' => 'success', 'message' => "Success"), 200);
        }
    }

    public function bannerSet($page) {
        if (isset($page) && $page->slug === '/home') {
            $banner = TRUE;
        }
        else {
            $banner = FALSE;
        }

        return $banner;
    }

    public function getPortfolioBlock() {
        return Portfolio::allActiveSorted();
    }

    public function uploadFile($data, $field_name) {
        //Only run when an image
        if ($data[$field_name]) {
            $image       = $data[$field_name];
            $filename    = $image->getClientOriginalName();
            $destination = $this->save_to;
            if (!$this->filesystem->exists($destination)) {
                $this->filesystem->mkdir($destination);
            }
            try {
                $image->move($destination, $filename);
                $data[$field_name] = $filename;
            }
            catch (\Exception $e) {
                throw new \Exception("Error uploading file $field_name" . $e->getMessage());
            }
        }

        return $data;
    }


    /**
     * @param $all
     * @param $model
     * @param $rules Portfolios::$rules
     * @return \Illuminate\Validation\Validator
     */
    public function validateSlugEdit($all, $model, $rules) {
        $messages = [];
        if (isset($all['slug']) && $all['slug'] != $model->slug) {
            $messages = array(
                'slug.unique' => 'The url is not unique.',
                'slug.regex'  => 'The url must start with a slash and contain only letters and numbers, no spaces.'
            );
        }
        else {
            unset($rules['slug']);
        }
        $validator = Validator::make($data = Input::all(), $rules, $messages);

        return $validator;
    }

    public function validateSlugOnCreate($all, $rules) {
        $messages  = array(
            'slug.unique' => 'The url is not unique .',
            'slug.regex'  => 'The url must start with a slash and contain only letters and numbers, no spaces.'
        );
        $validator = Validator::make($data = Input::all(), $rules, $messages);

        return $validator;
    }

    public function checkPublished($data) {
        if (!isset($data['published'])) {
            $data['published'] = 0;
        }

        return $data;
    }

    // put in sort to have images come in order assigned by file name br -2015/11/16
    public function getSlides() {

        $slides    = [];
        $directory = public_path() . "/slideshow/";
        $files     = File::allFiles($directory);
        foreach ($files as $key => $file) {
            $slides[$key] = "/slideshow/" . $file->getRelativePathname();
        }
		krsort($slides) ;  // br 2015/11/16
        JavaScript::put(compact('slides'));
    }

    public function checkForSlideshow($id) {
        $slideshow = FALSE;
        $slide_ids = [2, 5];
        $id == in_array($id, $slide_ids) ? $slideshow = TRUE : $tags = FALSE;
        if ($this->settings->theme == FALSE) {
            $slideshow = FALSE;
        }
        \View::share('slideshow', $slideshow);
    }

    protected function updateImagesCaption($image_captions) {
        foreach ($image_captions as $key => $image_caption) {
            //@TODO add catch here
            $image_id = intval($key);
            $caption  = NULL;
            if (isset($image_caption)) {
                $caption = $image_caption[0];
            }
            if ($caption != NULL) {
                $new_data = array("image_caption" => $caption);
                Image::where("id", "=", $image_id)->update($new_data);
            }
        }
    }

    protected function updateImagesOrder($image_order_values) {
        foreach ($image_order_values as $key => $image_order) {
            //@TODO add catch here
            $image_id = intval($key);
            $order    = $image_order[0];
            if ($order != NULL) {
                $new_data = array("order" => $order);
                Image::where("id", "=", $image_id)->update($new_data);
            }
        }
    }


}
