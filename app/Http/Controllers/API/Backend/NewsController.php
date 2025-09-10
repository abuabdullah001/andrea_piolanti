<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\News;


class NewsController extends Controller
{
    use apiresponse;

    public function index(){
        $news = News::all();
        return $this->success([
            'news' => $news
        ], 'News fetched successfully', 200);
    }
}
