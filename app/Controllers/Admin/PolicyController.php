<?php
/**
 * Membership Controller
 * Handle membership page logic
 */
namespace Admin;
use BaseController;

class PolicyController extends BaseController {
    public function __construct() {
        parent::__construct();
    }
    public function index() {
        // Set page title
        $this->setPageTitle('Policy Pages Management');

        // Enqueue WordPress Media Library
        wp_enqueue_media();

        // Register CSS files for policy management page
        $this->view->addCSS([
            'pages/management'
        ]);

        // Get policy files from Media Library
        $policyFiles = $this->getPolicyFiles();

        // Render policy management page
        $this->view->render('admin/policy/index', [
            'policyFiles' => $policyFiles,
            'totalPolicies' => count($policyFiles)
        ]);
    }

    /**
     * Get default policy types
     */
    private function getDefaultPolicyTypes() {
        return [
            'terms_of_service' => '이용약관',
            'privacy_policy' => '개인정보 처리방침',
            'travel_terms' => '국내/외 여행 표준약관',
            'sub_consent' => '부속 동의서',
            'marketing_consent' => '마케팅 목적 활용 동의',
            'third_party_consent' => '제 3자 정보 제공 동의',
        ];
    }

    /**
     * Get policy files from WordPress Media Library with default types
     */
    private function getPolicyFiles() {
        $defaultTypes = $this->getDefaultPolicyTypes();
        $policyFiles = [];

        // Get existing attachments with policy metadata
        $args = [
            'post_type' => 'attachment',
            'post_mime_type' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'post_status' => 'inherit',
            'numberposts' => -1,
            'meta_query' => [
                [
                    'key' => '_policy_type',
                    'compare' => 'EXISTS'
                ]
            ]
        ];

        $attachments = get_posts($args);
        $existingPolicies = [];

        foreach ($attachments as $attachment) {
            $filePath = get_attached_file($attachment->ID);
            $fileUrl = wp_get_attachment_url($attachment->ID);
            $policyTypes = get_post_meta($attachment->ID, '_policy_type', false); // Get all values
            
            // If no policy types, skip
            if (empty($policyTypes)) {
                continue;
            }

            foreach ($policyTypes as $policyType) {
                // Get display name from default types, fallback to stored value
                $displayName = isset($defaultTypes[$policyType]) ? $defaultTypes[$policyType] : $policyType;

                $existingPolicies[$policyType] = [
                    'id' => $attachment->ID,
                    'title' => $attachment->post_title,
                    'policy_name' => $displayName,
                    'policy_type_key' => $policyType,
                    'file_url' => $fileUrl,
                    'file_name' => basename($filePath),
                    'file_size' => size_format(filesize($filePath)),
                    'mime_type' => $attachment->post_mime_type,
                    'updated_date' => $attachment->post_modified,
                    'updated_date_formatted' => date('Y.m.d H:i', strtotime($attachment->post_modified))
                ];
            }
        }

        // Create policy entries for default types (with or without files)
        foreach ($defaultTypes as $typeKey => $typeName) {
            if (isset($existingPolicies[$typeKey])) {
                $policyFiles[] = $existingPolicies[$typeKey];
            } else {
                // Default entry without file
                $policyFiles[] = [
                    'id' => null,
                    'title' => '',
                    'policy_name' => $typeName,
                    'policy_type_key' => $typeKey,
                    'file_url' => '',
                    'file_name' => '',
                    'file_size' => '',
                    'mime_type' => '',
                    'updated_date' => '',
                    'updated_date_formatted' => '-'
                ];
            }
        }

        return $policyFiles;
    }
} 