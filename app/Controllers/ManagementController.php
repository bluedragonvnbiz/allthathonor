<?php
/**
 * Management Controller
 * Handle management page logic
 */
use App\Services\SectionService;
use App\Helpers\FieldRenderer;

class ManagementController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        // Load available sections
        $sectionService = new SectionService();
        $sections = $sectionService->getAvailableSections(['main', 'membership']);
        
        // Register CSS files for management page
        $this->view->addCSS([
            'pages/management'
        ]);
        
        // Render management view
        $this->view->render('management/index', [
            'user_info' => $this->user_info,
            'page_title' => 'Management Dashboard',
            'sections' => $sections
        ]);
    }

    public function edit($sectionKey = 'banner') {
        $sectionKey = $this->getGet('section');
        $sectionPage = $this->getGet('section_page', default: 'main');
        
        // Validate section key
        if (empty($sectionKey)) {
            wp_redirect('/management/');
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
            wp_redirect('/management/');
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
        
        $this->view->render('management/section-edit', [
            'sectionKey' => $sectionKey,
            'sectionName' => $sectionConfig['name'],
            'formHtml' => $formHtml,
            'sectionPage' => $sectionPage
        ]);
    }
} 