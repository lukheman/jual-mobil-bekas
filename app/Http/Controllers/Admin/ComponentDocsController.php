<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComponentDocsController extends Controller
{
    /**
     * Component documentation sections.
     *
     * @var array<string, array{label: string, icon: string, count: int}>
     */
    private const SECTIONS = [
        'buttons' => ['label' => 'Buttons', 'icon' => 'fas fa-hand-pointer', 'count' => 2],
        'badges' => ['label' => 'Badges', 'icon' => 'fas fa-tag', 'count' => 1],
        'alerts' => ['label' => 'Alerts', 'icon' => 'fas fa-exclamation-circle', 'count' => 1],
        'forms' => ['label' => 'Form Inputs', 'icon' => 'fas fa-edit', 'count' => 3],
        'selects' => ['label' => 'Select & Dropdown', 'icon' => 'fas fa-list', 'count' => 3],
        'toggles' => ['label' => 'Toggle & Checkbox', 'icon' => 'fas fa-toggle-on', 'count' => 3],
        'cards' => ['label' => 'Cards', 'icon' => 'fas fa-square', 'count' => 3],
        'tables' => ['label' => 'Tables', 'icon' => 'fas fa-table', 'count' => 1],
        'navigation' => ['label' => 'Navigation', 'icon' => 'fas fa-compass', 'count' => 2],
        'feedback' => ['label' => 'Feedback', 'icon' => 'fas fa-comment', 'count' => 4],
        'media' => ['label' => 'Media & Upload', 'icon' => 'fas fa-image', 'count' => 2],
    ];

    /**
     * Display the component documentation.
     */
    public function __invoke(Request $request)
    {
        $activeSection = (string) $request->query('section', 'buttons');

        if (! array_key_exists($activeSection, self::SECTIONS)) {
            $activeSection = 'buttons';
        }

        return view('admin.component-docs', [
            'sections' => self::SECTIONS,
            'activeSection' => $activeSection,
        ]);
    }
}
