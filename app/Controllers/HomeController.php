<?php
/**
 * Home Controller
 * Handle home page logic
 */
use App\Services\SectionService;
class HomeController extends BaseController {
    private $sectionService;
    public function __construct() {
        parent::__construct();
        $this->sectionService = new SectionService();
    }
    
    public function index() {
        // Set page title
        $this->setPageTitle('All That Honors Club - Premium Service');
        
        // Register CSS files for home page
        $this->view->addCSS([
            'global-css'
        ]);

        // Register JS files for home page
        $this->view->addJS([
            'home'
        ]);

        $data = [
            'banner' => $this->loadSectionData('banner', 'main'),
            'company_intro' => $this->loadSectionData('company_intro', 'main'),
            'service' => $this->loadSectionData('service', 'main'),
            'voucher' => $this->loadSectionData('voucher', 'main'),
            'membership' => $this->loadSectionData('membership', 'main')
        ];
        
        // Render home page
        $this->view->render('pages/home', $data);
    }

    private function loadSectionData($sectionKey, $sectionPage) {
        try {
            return $this->sectionService->getSectionData($sectionKey, $sectionPage);
        } catch (\Exception $e) {
            error_log("Error loading section {$sectionKey}: " . $e->getMessage());
            return [];
        }
    }
    
    
} 