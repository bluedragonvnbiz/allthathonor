<?php
/**
 * Management Controller
 * Handle management page logic
 */

namespace Admin;

use App\Services\SectionService;
use App\Helpers\FieldRenderer;
use BaseController;

class SectionController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->setPageTitle('Section Management');
        
        // Load available sections
        $sectionService = new SectionService();
        $sections = $sectionService->getAvailableSections(['main', 'membership']);
        
        // Register CSS files for management page
        $this->view->addCSS([
            'pages/management'
        ]);
        
        // Render management view
        $this->view->render('admin/section/index', [
            'user_info' => $this->user_info,
            'sections' => $sections
        ]);
    }

    public function edit($sectionKey = 'banner') {
        $this->setPageTitle('Edit Section');
        
        $sectionKey = $this->getGet('section');
        $sectionPage = $this->getGet('section_page', default: 'main');
        
        // Validate section key
        if (empty($sectionKey)) {
            wp_redirect('/admin/section/');
            exit;
        }
        
        $sectionService = new SectionService();
        
        // Clear cache to ensure fresh data
        $sectionService->clearCache($sectionKey, $sectionPage);
        
        // Load section config and data
        try {
            $sectionConfig = $sectionService->loadSectionConfig($sectionKey, $sectionPage);
        } catch (\Exception $e) {
            // Redirect to management page if section config not found
            wp_redirect('/admin/section/');
            exit;
        }

        $sectionData = $sectionService->getSectionData($sectionKey, $sectionPage);

        $renderer = new FieldRenderer();
        $formHtml = $renderer->renderSectionView($sectionConfig, $sectionData, $sectionKey);

        // Register CSS files for management page
        $this->view->addCSS([
            'pages/management',
            'search-field'
        ]);

        // Register JS files for management page
        $this->view->addJS([
            'section-form',
            'search-field'
        ]);
        
        // Enqueue WordPress Media Library
        wp_enqueue_media();
        
        $this->view->render('admin/section/edit', [
            'sectionKey' => $sectionKey,
            'sectionName' => $sectionConfig['name'],
            'formHtml' => $formHtml,
            'sectionPage' => $sectionPage
        ]);
    }
} 