<?php

namespace App\Views;

use App\Admin\Repositories\Section\SectionRepositoryInterface;
use Illuminate\View\Component;

class Section extends Component
{
    private $sectionRepository;
    public function __construct(SectionRepositoryInterface $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    public function render()
    {
        $sections = $this->sectionRepository->getQueryBuilderOrderBy('position', 'asc')->where('is_active', 1)->get();
        return view('components.layouts.section', compact('sections'));
    }
}
