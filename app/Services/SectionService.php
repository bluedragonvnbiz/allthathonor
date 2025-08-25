<?php

namespace App\Services;

use App\Database\SectionDatabase;

class SectionService {
    private const CACHE_PREFIX = 'section:';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get section data with cache
     */
    public function getSectionData(string $sectionKey, string $page = 'main') {
        // Try to get from cache first
        $cached = wp_cache_get($this->getCacheKey($sectionKey, $page));
        
        if ($cached !== false) {
            return json_decode($cached, true);
        }

        global $wpdb;
        $table_name = SectionDatabase::getTableName();
        
        // Get from database with page parameter
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT data FROM $table_name WHERE section_key = %s AND page = %s",
                $sectionKey,
                $page
            )
        );

        $data = $result ? json_decode($result->data, true) : $this->getDefaultData($sectionKey, $page);
        
        // Store in cache
        wp_cache_set($this->getCacheKey($sectionKey, $page), json_encode($data), '', self::CACHE_TTL);

        return $data;
    }

    /**
     * Update section data
     */
    public function updateSectionData(string $sectionKey, array $data, string $formType = '', string $page = 'main') {
        // Validate data with config
        $this->validateWithConfig($sectionKey, $data, $formType, $page);

        global $wpdb;
        $table_name = SectionDatabase::getTableName();

        // Get existing data
        $existing = $this->getSectionData($sectionKey, $page);
        
        // Merge new data with existing data based on form type
        if ($formType === 'section_info') {
            $existing['section_info'] = $data;
            
            // Remove old root-level section fields to avoid conflicts
            foreach ($data as $key => $value) {
                if (isset($existing[$key])) {
                    unset($existing[$key]);
                }
            }
        } elseif ($formType === 'content_info') {
            $existing['content_info'] = $data;
            
            // Remove old root-level content fields to avoid conflicts
            foreach ($data as $key => $value) {
                if (isset($existing[$key])) {
                    unset($existing[$key]);
                }
            }
        } else {
            // If no form type specified, merge all data
            $existing = array_merge($existing, $data);
        }

        // Check if record exists with page parameter
        $existingRecord = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table_name} WHERE section_key = %s AND page = %s", $sectionKey, $page)
        );

        if ($existingRecord) {
            // Update existing record
            $result = $wpdb->update(
                $table_name,
                ['data' => json_encode($existing, JSON_UNESCAPED_UNICODE)],
                ['section_key' => $sectionKey, 'page' => $page],
                ['%s'],
                ['%s', '%s']
            );
        } else {
            // Insert new record
            $result = $wpdb->insert(
                $table_name,
                [
                    'section_key' => $sectionKey,
                    'page' => $page,
                    'data' => json_encode($existing, JSON_UNESCAPED_UNICODE)
                ],
                ['%s', '%s', '%s']
            );
        }

        if ($result === false) {
            throw new \Exception("Failed to update section data: {$sectionKey} for page: {$page}");
        }

        // Clear cache
        wp_cache_delete($this->getCacheKey($sectionKey, $page));

        return true;
    }

    /**
     * Get all sections for a specific page
     */
    public function getAllSectionsByPage(string $page = 'main') {
        global $wpdb;
        $table_name = SectionDatabase::getTableName();
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT section_key, data FROM $table_name WHERE page = %s",
                $page
            )
        );

        $sections = [];
        foreach ($results as $result) {
            $sections[$result->section_key] = json_decode($result->data, true);
        }

        return $sections;
    }

    /**
     * Check if section has data in database
     */
    public function hasSectionData(string $sectionKey, string $page = 'main'): bool {
        global $wpdb;
        $table_name = SectionDatabase::getTableName();
        
        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE section_key = %s AND page = %s",
            $sectionKey,
            $page
        ));
        
        return (int)$result > 0;
    }

    /**
     * Get section last modified date
     */
    public function getSectionLastModified(string $sectionKey, string $page = 'main'): ?string {
        global $wpdb;
        $table_name = SectionDatabase::getTableName();
        
        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT updated_at FROM $table_name WHERE section_key = %s AND page = %s",
            $sectionKey,
            $page
        ));
        
        return $result ? $result : null;
    }
    
    /**
     * Clear cache for a specific section
     */
    public function clearCache(string $sectionKey, string $page = 'main') {
        wp_cache_delete($this->getCacheKey($sectionKey, $page));
    }

    /**
     * Get default data from config
     */
    private function getDefaultData(string $sectionKey, string $sectionPage = 'main') {
        $configPath = THEME_PATH . "/config/sections/{$sectionPage}/{$sectionKey}.php";
        if (!file_exists($configPath)) {
            throw new \Exception("Section config not found: {$sectionKey} for {$sectionPage}");
        }

        $config = require $configPath;
        $defaultData = [];

        // Extract default values from section_info
        if (isset($config['section_info']['fields'])) {
            foreach ($config['section_info']['fields'] as $field) {
                if (isset($field['key'], $field['default'])) {
                    $defaultData['section_' . $field['key']] = $field['default'];
                }
            }
        }

        // Extract default values from content_info
        if (isset($config['content_info']['sections'])) {
            foreach ($config['content_info']['sections'] as $section) {
                foreach ($section['fields'] as $field) {
                    if (isset($field['key'], $field['default'])) {
                        $defaultData['content_' . $field['key']] = $field['default'];
                    }
                }
            }
        }

        return $defaultData;
    }

    /**
     * Validate data with config
     */
    private function validateWithConfig(string $sectionKey, array $data, string $formType = '', string $sectionPage = 'main') {
        $configPath = THEME_PATH . "/config/sections/{$sectionPage}/{$sectionKey}.php";
        if (!file_exists($configPath)) {
            throw new \Exception("Section config not found: {$sectionKey} for {$sectionPage}");
        }

        $config = require $configPath;

        // Validate based on form type
        if ($formType === 'section_info' && isset($config['section_info']['fields'])) {
            foreach ($config['section_info']['fields'] as $field) {
                $this->validateField($field, $data, 'section_');
            }
        } elseif ($formType === 'content_info' && isset($config['content_info']['fields'])) {
            foreach ($config['content_info']['fields'] as $field) {
                $this->validateField($field, $data, 'content_');
            }
        } elseif ($formType === 'content_info' && isset($config['content_info']['sections'])) {
            foreach ($config['content_info']['sections'] as $section) {
                foreach ($section['fields'] as $field) {
                    $this->validateField($field, $data, 'content_');
                }
            }
        } else {
            // If no form type specified, validate all fields
            if (isset($config['section_info']['fields'])) {
                foreach ($config['section_info']['fields'] as $field) {
                    $this->validateField($field, $data, 'section_');
                }
            }
            if (isset($config['content_info']['fields'])) {
                foreach ($config['content_info']['fields'] as $field) {
                    $this->validateField($field, $data, 'content_');
                }
            }
            if (isset($config['content_info']['sections'])) {
                foreach ($config['content_info']['sections'] as $section) {
                    foreach ($section['fields'] as $field) {
                        $this->validateField($field, $data, 'content_');
                    }
                }
            }
        }
    }

    /**
     * Validate individual field
     */
    private function validateField(array $field, array $data, string $prefix) {
        $key = $prefix . $field['key'];

        // Skip validation for display-only fields
        if (in_array($field['type'], ['html', 'display_text', 'field_group', 'text_group'])) {
            return;
        }

        // Check required fields
        if (!isset($data[$key]) && !isset($field['default'])) {
            throw new \Exception("Missing required field: {$key}");
        }

        // Validate by type
        if (isset($data[$key])) {
            switch ($field['type']) {
                case 'select':
                    if (!isset($field['options'][$data[$key]])) {
                        throw new \Exception("Invalid option for field: {$key}");
                    }
                    break;

                case 'radio':
                    if (!isset($field['options'][$data[$key]])) {
                        throw new \Exception("Invalid option for field: {$key}");
                    }
                    break;

                // Add more type validations as needed
            }
        }
    }

    /**
     * Get cache key for section
     */
    private function getCacheKey(string $sectionKey, string $page = 'main'): string {
        return self::CACHE_PREFIX . $page . ':' . $sectionKey;
    }

    /**
     * Load section config from file
     */
    public function loadSectionConfig(string $sectionKey, string $sectionPage = 'main'): array {
        $configPath = THEME_PATH . "/config/sections/{$sectionPage}/{$sectionKey}.php";
        if (!file_exists($configPath)) {
            throw new \Exception("Section config not found: {$sectionKey} for {$sectionPage}");
        }
        
        return require $configPath;
    }
    
    /**
     * Get available sections from config directory with database status
     */
    public function getAvailableSections(array $sectionPages = ['main']): array {
        $sections = [];
        
        foreach ($sectionPages as $sectionPage) {
            $configDir = THEME_PATH . '/config/sections/'.$sectionPage.'/';
        
        if (is_dir($configDir)) {
            $files = glob($configDir . '*.php');
            foreach ($files as $file) {
                $sectionKey = basename($file, '.php');
                    
                    // Skip if section already exists (priority to first found)
                    $exists = false;
                    foreach ($sections as $existingSection) {
                        if ($existingSection['key'] === $sectionKey) {
                            $exists = true;
                            break;
                        }
                    }
                    
                    if ($exists) {
                        continue;
                    }
                    
                    try {
                        $config = $this->loadSectionConfig($sectionKey, $sectionPage);
                        
                        // Check if section has data in database
                        $hasData = $this->hasSectionData($sectionKey, $sectionPage);
                        
                        $sections[] = [
                            'key' => $sectionKey,
                            'name' => $config['name'] ?? ucfirst($sectionKey),
                            'config' => $config,
                            'has_data' => $hasData,
                            'status' => $hasData ? 'configured' : 'empty',
                            'directory' => $sectionPage
                        ];
                    } catch (\Exception $e) {
                    // Skip invalid config files
                    continue;
                    }
                }
            }
        }
        
        return $sections;
    }
}