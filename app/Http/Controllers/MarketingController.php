<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ModuleDefinition;
use Illuminate\Contracts\View\View;

class MarketingController extends Controller
{
    public function home(): View
    {
        return view('marketing.home');
    }

    public function features(): View
    {
        $modules = ModuleDefinition::query()->orderBy('sort_order')->get();

        return view('marketing.features', compact('modules'));
    }

    public function pricing(): View
    {
        return view('marketing.pricing');
    }

    public function demo(): View
    {
        return view('marketing.demo');
    }

    public function docs(): View
    {
        return view('marketing.docs');
    }

    public function about(): View
    {
        return view('marketing.about');
    }

    public function contact(): View
    {
        return view('marketing.contact');
    }

    public function terms(): View
    {
        return view('marketing.legal', ['document' => 'terms']);
    }

    public function privacy(): View
    {
        return view('marketing.legal', ['document' => 'privacy']);
    }
}
