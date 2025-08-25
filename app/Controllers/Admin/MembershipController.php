<?php
/**
 * Membership Controller
 * Handle membership page logic
 */
namespace Admin;
use App\Helpers\FieldRenderer;
use App\Services\VoucherService;
use App\Services\MembershipService;
use App\Services\SectionService;
use BaseController;

class MembershipController extends BaseController {
    private $sectionService;
    public function __construct() {
        parent::__construct();
        $this->sectionService = new SectionService();
    }
    public function index() {
        // Set page title
        $this->setPageTitle('Membership Management');

        // Enqueue WordPress Media Library
        wp_enqueue_media();

        // Register CSS files for membership management page
        $this->view->addCSS([
            'pages/management'
        ]);

        // Register JS files for membership management page
        $this->view->addJS([
            'membership-management'
        ]);

        // Get memberships data
        $membershipService = new MembershipService();
        $memberships = $membershipService->getAllMemberships();
        $totalMemberships = count($memberships);

        // Render membership management page
        $this->view->render('admin/membership/index', [
            'memberships' => $memberships,
            'totalMemberships' => $totalMemberships
        ]);
    }

    public function view(){
        // Set page title
        $this->setPageTitle('Membership Management');

        $id = $this->getGet('id');

        // Enqueue WordPress Media Library
        wp_enqueue_media();

        // Register CSS files for membership management page
        $this->view->addCSS([
            'pages/management'
        ]);

        // Register JS files for membership management page
        $this->view->addJS([
            'membership-management'
        ]);

        $membershipService = new MembershipService();
        $card_info = $membershipService->getMembership($id);

        if (empty($card_info)) {
            return;
        }

        // Get vouchers by category
        $voucherService = new VoucherService();
        $vouchers = $voucherService->getVouchersByCategory($card_info['id']);

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
        $this->view->render('admin/membership/view', [
            'categories' => $categories,
            'card_info' => $card_info,
            'vouchers' => $vouchers,
            'formHtml' => $formHtml
        ]);
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