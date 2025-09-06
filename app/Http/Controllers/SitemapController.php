<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\BlogArticle;
use App\Models\Admin\BlogCategory;

class SitemapController extends Controller
{
    public function index()
    {
        $articles = BlogArticle::published()
            ->with(['category', 'tags'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = BlogCategory::active()->get();

        return response()->view('sitemap', compact('articles', 'categories'))
            ->header('Content-Type', 'application/xml');
    }
}
