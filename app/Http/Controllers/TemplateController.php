<?php

namespace App\Http\Controllers;

use App\Models\Template;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::query()
            ->where('is_active', true)
            ->orderByRaw("FIELD(slug, 'bronze', 'silver', 'gold')")
            ->get();

        return view('templates.index', compact('templates'));
    }
}