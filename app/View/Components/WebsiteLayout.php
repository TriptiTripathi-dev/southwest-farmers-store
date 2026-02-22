<?php
namespace App\View\Components;

use Illuminate\View\Component;

class WebsiteLayout extends Component
{
    public $title;

    public function __construct($title = null)
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('layouts.website'); // Maps to resources/views/layouts/website.blade.php
    }
}