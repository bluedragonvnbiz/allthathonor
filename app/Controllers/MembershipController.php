<?php
/**
 * Membership Controller
 * Handle membership page logic
 */
use App\Helpers\FieldRenderer;
use App\Services\VoucherService;
use App\Services\MembershipService;
use App\Services\SectionService;

class MembershipController extends BaseController {
    private $sectionService;
    public function __construct() {
        parent::__construct();
        $this->sectionService = new SectionService();
    }
    
    public function index() {
        // Set page title
        $this->setPageTitle('All That Honors Club - Membership');
        
        // Get data for inquiry page
        $data = [
        ];

        // Register CSS files for home page
        $this->view->addCSS([
            'global-css'
        ]);

        // Register JS files for home page
        $this->view->addJS([
            'home'
        ]);

        $sectionData = $this->loadSectionData('our_membership', 'membership');

        // Render home page
        $this->view->render('pages/membership', [
            'sectionData' => $sectionData
        ]);
    }

    public function management() {
        $id = $this->getGet('id');

        // Register CSS files for membership management page
        $this->view->addCSS([
            'pages/management'
        ]);

        if (empty($id)) {
            // Set page title
            $this->setPageTitle('All That Honors Club - Membership Management');

            // Get memberships data
            $membershipService = new MembershipService();
            $memberships = $membershipService->getAllMemberships();
            $totalMemberships = count($memberships);

            // Render membership management page
            $this->view->render('membership/index', [
                'memberships' => $memberships,
                'totalMemberships' => $totalMemberships
            ]);
        } else {
            // Set page title
            $this->setPageTitle('All That Honors Club - Membership Management');

            $membershipService = new MembershipService();
            $card_info = $membershipService->getMembership($id);

            if (empty($card_info)) {
               return;
            }

            // Get vouchers by category
            $voucherService = new VoucherService();
            $vouchers = $voucherService->getVouchersByCategory($card_info['membership_name']);

            // Define categories mapping
            $categories = [
                'travel_care' => ['name' => '트래블케어'],
                'lifestyle' => ['name' => '라이프스타일'],
                'special_benefit' => ['name' => '스페셜베네핏'],
                'welcome_gift' => ['name' => '웰컴기프트']
            ];

            // Load membership field configuration
            $membershipFieldsConfig = require THEME_PATH . '/config/membership_fields.php';

            $renderer = new FieldRenderer();
    
            $formHtml = $renderer->renderSectionView($membershipFieldsConfig, sectionData: $card_info, sectionKey: 'voucher');

            // Render membership management page
            $this->view->render('membership/view', [
                'categories' => $categories,
                'card_info' => $card_info,
                'vouchers' => $vouchers,
                'formHtml' => $formHtml
            ]);
        }
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