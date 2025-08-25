<?php
/**
 * Field Renderer Helper
 * Renders form fields based on configuration
 */

namespace App\Helpers;

class FieldRenderer {
    /**
     * Static method to render a single field
     */
    public static function renderSingleField($field, $data = []) {
        $renderer = new self();
        return $renderer->renderField($field, $data, 'product', false);
    }
    
    /**
     * Render complete section with both section_info and content_info blocks
     */
    public function renderSection($sectionConfig, $sectionData = [], $sectionKey = '') {
        $html = '';
        $oneSubmit = isset($sectionConfig['one_submit']) && $sectionConfig['one_submit'];
        if ($oneSubmit) {
            $html .= '<style>
                .section-form .card-header .btn[type="submit"] {
                    display: none !important;
                }
            </style>';
        }

        // Render Section Info block
        if (isset($sectionConfig['section_info'])) {
            $html .= $this->renderFormBlock($sectionConfig['section_info'], $sectionData, 'section', $sectionKey . '_section_info');
        }
        
        // Render Content Info block
        if (isset($sectionConfig['content_info'])) {
            $html .= $this->renderFormBlock($sectionConfig['content_info'], $sectionData, 'content', $sectionKey . '_content_info');
        }

        if ($oneSubmit) {
            $html .= '<div class="card-footer d-flex justify-content-end mb-4">';
            if($sectionData['id']) {
                $html .= '<input type="hidden" name="voucher_id" value="' . $sectionData['id'] . '">';
            }
            $html .= '<button type="submit" class="btn btn-primary btn-submit-voucher">저장</button>';
            $html .= '</div>';
            $html .= '</form>';
        }
        
        return $html;
    }

     /**
     * Render complete section with both section_info and content_info blocks
     */
    public function renderSectionView($sectionConfig, $sectionData = [], $sectionKey = '') {
        $html = '';
        
        // Render Section Info block
        if (isset($sectionConfig['section_info'])) {
            $html .= $this->renderFormBlock($sectionConfig['section_info'], $sectionData, 'section_view', $sectionKey . '_section_info');
        }
        
        // Render Content Info block
        if (isset($sectionConfig['content_info'])) {
            $html .= $this->renderFormBlock($sectionConfig['content_info'], $sectionData, 'content_view', $sectionKey . '_content_info');
        }
        
        return $html;
    }
    
    /**
     * Render product section with custom layout (like the image)
     */
    public function renderProductSection($sectionConfig, $sectionData = [], $action = 'create') {
        $html = '<form class="product-form" data-action="' . $action . '">';
        $html .= '<div class="card">';
        if ($action === 'update') {
            $html .= '<input type="hidden" name="product_id" value="' . $sectionData['id'] . '">';
        }
        $html .= wp_nonce_field('product_nonce', 'nonce', true, false);
        
        $html .= '<div class="card-body p-0">';
        $html .= '<div class="card-header d-flex align-items-center justify-content-between">';
        $html .= '<strong class="title fw-bolder letter-spacing-1">상품 내용</strong>';
        if ($action === 'update') {
            $html .= '<button type="submit" class="btn btn-primary">저장</button>';
        }
        $html .= '</div>';
        

        // Render all fields in a single form
        if (isset($sectionConfig['product_content'])) {
            $html .= $this->renderFieldGroup($sectionConfig['product_content']['fields'], $sectionData, 'product');
        }
                
        $html .= '</div>';
        $html .= '</div>';

        if ($action === 'create') {
            $html .= '<div class="card-footer d-flex justify-content-end mb-4">';
            $html .= '<button type="submit" class="btn btn-primary">저장</button>';
            $html .= '</div>';
        }

        $html .= '</form>';
        
        return $html;
    }
    
    /**
     * Render product view (display only, no form)
     */
    public function renderProductView($sectionConfig, $sectionData = []) {
        $html = '<div class="card infor-box">';
        $html .= '<div class="card-body p-0">';
        $html .= '<div class="card-header d-flex align-items-center justify-content-between">';
        $html .= '<strong class="title fw-bolder letter-spacing-1">상품 내용</strong>';
        $html .= '<a href="/admin/product/edit/?id=' . $sectionData['id'] . '" class="btn btn-primary">수정</a>';
        $html .= '</div>';
        
        // Render all fields in view mode
        if (isset($sectionConfig['product_content'])) {
            $html .= $this->renderFieldGroup($sectionConfig['product_content']['fields'], $sectionData, 'product_view');
        }
                
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render product fields with proper separators
     */
    private function renderProductFields($fields, $data, $type) {
        $html = '';
        
        foreach ($fields as $index => $field) {
            // Add separator between sections (except first)
            if ($index > 0) {
                $html .= '<div class="field-separator"></div>';
            }
            
            $html .= '<div class="field-group">';
            $html .= $this->renderField($field, $data, $type, false);
            $html .= '</div>';
        }
        
        return $html;
    }
    
    /**
     * Render a form block (section_info or content_info)
     */
    public function renderFormBlock($blockConfig, $data, $type, $sectionKey = '') {
        $nonce = wp_create_nonce('update_section_nonce');
        if (strpos($type, '_view') !== false) {
            $form_class = 'infor-box';
            $button_action = '<button class="btn btn-primary btn-edit-section lh-1" type="button">수정</button>';
        } else {
            $form_class = 'section-form';
            $button_action = '<button class="btn btn-primary lh-1" type="submit">저장</button>';
        }

        // Extract block type from section key
        $blockType = '';
        if (strpos($sectionKey, '_section_info') !== false) {
            $blockType = 'section_info';
        } elseif (strpos($sectionKey, '_content_info') !== false) {
            $blockType = 'content_info';
        } else {
            $blockType = $type . '_info';
        }
        
        // Extract base section key (remove _section_info or _content_info suffix)
        $baseSectionKey = $sectionKey;
        if (strpos($sectionKey, '_section_info') !== false) {
            $baseSectionKey = str_replace('_section_info', '', $sectionKey);
        } elseif (strpos($sectionKey, '_content_info') !== false) {
            $baseSectionKey = str_replace('_content_info', '', $sectionKey);
        }
        
        $html = '<form class="card ' . $form_class . '" data-section="' . $baseSectionKey . '" data-block="' . $blockType . '">';
        $html .= '<input type="hidden" name="nonce" value="' . $nonce . '">';
        if($data['id']) {
            $html .= '<input type="hidden" name="id" value="' . $data['id'] . '">';
        }
        $html .= '<input type="hidden" name="action" value="update_section">';
        $html .= '<div class="card-header d-flex align-items-center justify-content-between">';
        $html .= '<strong class="title fw-bolder letter-spacing-1">' . $blockConfig['title'] . '</strong>';
        $html .= $button_action;
        $html .= '</div>';
        $html .= '<div class="card-body p-0">';

        // Check if the structure is new
        if (isset($blockConfig['sections'])) {
            foreach ($blockConfig['sections'] as $section) {
                $html .= '<div class="section-box">';
                $html .= $this->renderFieldGroup($section['fields'], $data, $type);
                $html .= '</div>';
            }
        } else {
            // Backwards compatibility - render tất cả fields trong một section-box
            $html .= '<div class="section-box">';
            $html .= $this->renderFieldGroup($blockConfig['fields'], $data, $type);
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</form>';
        
        return $html;
    }

    /**
     * Render a group of fields with proper layout
     */
    private function renderFieldGroup($fields, $data, $type) {
        $html = '';
        $useTwoColumns = ($type === 'section' || $type === 'product' || $type === 'product_view' || $type === 'section_view');
        
        if ($useTwoColumns) {
            $totalFields = count($fields);
            $i = 0;
            
            while ($i < $totalFields) {
                $currentField = $fields[$i];

                if($type === 'product' || $type === 'product_view') {
                    $html .= '<div class="section-box">';
                }
                
                if ((isset($currentField['full_width']) && $currentField['full_width']) || $currentField['type'] === 'editor') {
                    // Full width fields và editor không cần wrapper divs
                    $html .= $this->renderField($currentField, $data, $type, false);
                    $i++;

                    if($type === 'product' || $type === 'product_view') {
                        $html .= '</div>';
                    }
                    continue;
                }
                
                // Xử lý layout 2 cột bình thường
                $html .= '<div class="d-flex gap-40">';
                
                // First field
                $html .= '<div class="d-flex column-gap-30 w-100 align-items-center">';
                $html .= $this->renderField($currentField, $data, $type, true);
                $html .= '</div>';
                
                // Second field (if exists and not full width)
                if ($i + 1 < $totalFields && 
                    (!isset($fields[$i + 1]['full_width']) || 
                     !$fields[$i + 1]['full_width'])) {
                    $html .= '<div class="d-flex column-gap-30 w-100 align-items-center">';
                    $html .= $this->renderField($fields[$i + 1], $data, $type, true);
                    $html .= '</div>';
                    $i += 2;
                } else {
                    // Nếu là field cuối cùng và không phải full width, thêm div trống để giữ layout
                    if (!isset($currentField['full_width']) || !$currentField['full_width']) {
                        $html .= '<div class="d-flex column-gap-30 w-100 align-items-center"></div>';
                    }
                    $i++;
                }
                
                $html .= '</div>';

                if($type === 'product' || $type === 'product_view') {
                    $html .= '</div>';
                }
            }
        } else {
            // Single column layout
            foreach ($fields as $field) {
                $html .= $this->renderField($field, $data, $type, false);
            }
        }
        
        return $html;
    }
    
    /**
     * Render individual field based on type
     */
    private function renderField($field, $data, $type, $isTwoColumnLayout = false) {
        // For product_view type, convert field types to display mode
        if ($type === 'product_view' || $type === 'section_view' || $type === 'content_view') {
            $field = $this->convertFieldToDisplayMode($field);
        }
        
        $type = str_replace('_view', '', $type);

        // Try to find value with proper key structure
        $fieldKey = $type . '_' . $field['key'];

        // Check multiple possible locations for the value
        $value = $data[$fieldKey] 
              ?? $data[$type . '_info'][$fieldKey] 
              ?? $data[$field['key']] 
              ?? $data[$type . '_info'][$field['key']] 
              ?? $field['default'] 
              ?? '';
        
        $name = $fieldKey;
        
        switch ($field['type']) {
            case 'text':
                return $this->renderTextInput($field, $value, $name, $isTwoColumnLayout);
            case 'textarea':
                return $this->renderTextarea($field, $value, $name, $isTwoColumnLayout);
            case 'radio':
                return $this->renderRadio($field, $value, $name, $isTwoColumnLayout);
            case 'image':
                return $this->renderImageUpload($field, $value, $name, $isTwoColumnLayout);
            case 'select':
                return $this->renderSelect($field, $value, $name, $isTwoColumnLayout);
            case 'checkbox':
                return $this->renderCheckbox($field, $value, $name, $isTwoColumnLayout);
            case 'text_group':
                return $this->renderTextGroup($field, $value, $name, $isTwoColumnLayout);
            case 'display_text':
                return $this->renderDisplayText($field, $value, $name, $isTwoColumnLayout);
            case 'html':
                return $this->renderHtml($field, $value, $name, $isTwoColumnLayout);
            case (preg_match('/^search_/', $field['type']) ? $field['type'] : null):
                $id_value = $data[$type . '_info'][$name . '_id'] ?? '';
                return $this->renderSearch($field, $value, $name, $isTwoColumnLayout, $id_value);
            case 'editor':
                return $this->renderRichTextEditor($field, $value, $name, $isTwoColumnLayout);
            case 'field_group':
                return $this->renderFieldPair($field, $data, $type, $isTwoColumnLayout);
            case 'display':
                // For field_group converted to display, pass $data instead of $value
                if ($field['original_type'] === 'field_group') {
                    return $this->renderDisplayField($field, $data, $name, $isTwoColumnLayout);
                }
                return $this->renderDisplayField($field, $value, $name, $isTwoColumnLayout);
            default:
                return '';
        }
    }
    
    /**
     * Render text input field
     */
    private function renderTextInput($field, $value, $name, $isTwoColumnLayout = false) {
        $readonly = isset($field['readonly']) && $field['readonly'] ? 'readonly' : '';
        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
        $gap = isset($field['gap']) ? $field['gap'] : 'gap-40';
        $width = isset($field['width']) ? $field['width'] : '';

        $content = '<label class="form-label">' . $field['label'] . '</label>
            <input type="text" class="form-control" name="' . $name . '" value="' . htmlspecialchars($value) . '" ' . $readonly . ' placeholder="' . $placeholder . '">';

        if (!$isTwoColumnLayout) {
            return '<div class="d-flex ' . $gap . ' align-items-center">' . $content . '</div>';
        }
        
        return $content;
    }
    
    /**
     * Render textarea field
     */
    private function renderTextarea($field, $value, $name, $isTwoColumnLayout = false) {
        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
        $rows = isset($field['rows']) ? $field['rows'] : '';
        $style = isset($field['style']) ? 'style="' . $field['style'] . '"' : '';
        $gap = isset($field['gap']) ? $field['gap'] : 'gap-40';
        $align = isset($field['align']) ? $field['align'] : 'align-items-start';
        
        $content = '<label class="form-label">' . $field['label'] . '</label>
            <textarea style="height: ' . $rows . 'em;" class="form-control" name="' . $name . '" rows="' . $rows . '" ' . $style . ' placeholder="' . $placeholder . '">' . htmlspecialchars($value) . '</textarea>';

        if (!$isTwoColumnLayout) {
            if (strpos($name, 'product_') !== false) {
                return '<div class="d-flex ' . $gap . ' ' . $align . '"><div class="d-flex column-gap-30 w-100 align-items-center">' . $content . '</div></div>';
            }
            
            return '<div class="d-flex ' . $gap . ' ' . $align . '">' . $content . '</div>';
        }
        
        return $content;
    }
    
    /**
     * Render radio buttons field
     */
    private function renderRadio($field, $value, $name, $isTwoColumnLayout = false) {
        $width = isset($field['width']) ? $field['width'] : 'w-100';
        $content = '<label class="form-label">' . $field['label'] . '</label>
            <div class="d-flex gap-20 w-100">';
        
        foreach ($field['options'] as $optionValue => $optionLabel) {
            $checked = ($value == $optionValue) ? 'checked' : '';
            $disabled = '';
            
            // Check if field is disabled (for view mode)
            if (isset($field['disabled']) && $field['disabled']) {
                $disabled = 'disabled';
            } elseif (isset($field['disabled']) && in_array($optionValue, $field['disabled'])) {
                $disabled = 'disabled';
            }
            
            $id = $name . '_' . $optionValue;
            
            $content .= '
            <div class="form-check">
                <input class="form-check-input" type="radio" name="' . $name . '" 
                       id="' . $id . '" value="' . $optionValue . '" ' . $checked . ' ' . $disabled . '>
                <label class="form-check-label" for="' . $id . '">
                    ' . $optionLabel . '
                </label>
            </div>';
        }
        
        $content .= '</div>';
        
        if (!$isTwoColumnLayout) {
            return '<div class="d-flex gap-40 align-items-center">' . $content . '</div>';
        }
        
        return $content;
    }
    
    /**
     * Render image upload field
     */
    private function renderImageUpload($field, $value, $name, $isTwoColumnLayout = false) {
        $accept = isset($field['accept']) ? $field['accept'] : '.jpg,.jpeg,.png,.webp';
        $width = isset($field['width']) ? $field['width'] : 'w-100';
        
        // Extract filename from URL if it's a full URL
        $displayValue = $value;
        if (!empty($value) && filter_var($value, FILTER_VALIDATE_URL)) {
            $displayValue = basename($value);
        }
        
        $content = '<label class="form-label">' . $field['label'] . '</label>
            <div class="upload-box w-100">
                <input type="text" class="form-control file-name" readonly value="' . htmlspecialchars($displayValue) . '" placeholder="파일을 선택해주세요.">
                <input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '">
                <label class="btn btn-primary">
                    파일 선택
                    <input type="file" class="d-none select-file" accept="' . $accept . '" data-input-target="' . $name . '" data-field-name="' . $name . '">
                </label>
            </div>';

        if (!$isTwoColumnLayout) {
            return '<div class="d-flex gap-40 align-items-center">' . $content . '</div>';
        }
        
        return $content;
    }
    
    /**
     * Render select dropdown field
     */
    private function renderSelect($field, $value, $name, $isTwoColumnLayout = false) {        
        $content = '<label class="form-label">' . $field['label'] . '</label>
            <select class="form-select w-100" name="' . $name . '">';
        
        foreach ($field['options'] as $optionValue => $optionLabel) {
            $selected = ($value == $optionValue) ? 'selected' : '';
            $content .= '<option value="' . $optionValue . '" ' . $selected . '>' . $optionLabel . '</option>';
        }
        
        $content .= '</select>';
        
        if (!$isTwoColumnLayout) {
            return '<div class="d-flex gap-40 align-items-center">' . $content . '</div>';
        }
        
        return $content;
    }
    
    /**
     * Render checkbox field
     */
    private function renderCheckbox($field, $value, $name, $isTwoColumnLayout = false) {
        $content = '<label class="form-label">' . $field['label'] . '</label>
            <div class="d-flex gap-20">';
        
        foreach ($field['options'] as $optionValue => $optionLabel) {
            // Handle comma-separated string values for checkbox arrays
            $checked = '';
            if (!empty($value)) {
                if (is_string($value) && strpos($value, ',') !== false) {
                    // Split comma-separated string into array
                    $valueArray = array_map('trim', explode(',', $value));
                    $checked = in_array($optionValue, $valueArray) ? 'checked' : '';
                } else {
                    $checked = in_array($optionValue, (array)$value) ? 'checked' : '';
                }
            }
            
            $disabled = '';
            
            // Check if field is disabled (for view mode)
            if (isset($field['disabled']) && $field['disabled']) {
                $disabled = 'disabled';
            }
            
            $id = $name . '_' . $optionValue;
            $textClass = isset($field['text_uppercase']) && $field['text_uppercase'] ? 'text-uppercase' : '';
            
            $content .= '
            <div class="form-check checkbox">
                <input class="form-check-input" type="checkbox" name="' . $name . '[]" 
                       value="' . $optionValue . '" id="' . $id . '" ' . $checked . ' ' . $disabled . '>
                <label class="form-check-label ' . $textClass . '" for="' . $id . '">' . $optionLabel . '</label>
            </div>';
        }
        
        $content .= '</div>';
        
        if (!$isTwoColumnLayout) {
            return '<div class="d-flex gap-40 align-items-center">' . $content . '</div>';
        }
        
        return $content;
    }
    
    /**
     * Render text group (multiple text inputs in one row)
     */
    private function renderTextGroup($field, $value, $name, $isTwoColumnLayout = false) {
        $content = '<label class="form-label">' . $field['label'] . '</label>
            <div class="d-flex column-gap-3 w-100">';
        
        foreach ($field['inputs'] as $inputKey => $inputConfig) {
            $inputValue = is_array($value) ? ($value[$inputKey] ?? '') : '';
            $inputName = $name . '[' . $inputKey . ']';
            $placeholder = isset($inputConfig['placeholder']) ? $inputConfig['placeholder'] : '';
            
            $content .= '<input type="text" class="form-control" name="' . $inputName . '" value="' . htmlspecialchars($inputValue) . '" placeholder="' . $placeholder . '">';
        }
        
        $content .= '</div>';
        
        if (!$isTwoColumnLayout) {
            return '<div class="d-flex gap-40 align-items-center">' . $content . '</div>';
        }
        
        return $content;
    }
    
    /**
     * Render display text field (readonly text display)
     */
    private function renderDisplayText($field, $value, $name, $isTwoColumnLayout = false) {
        $gap = isset($field['gap']) ? $field['gap'] : 'gap-40';
        $align = isset($field['align']) ? $field['align'] : 'align-items-center';
        $textClass = isset($field['text_class']) ? $field['text_class'] : '';
        $width = isset($field['width']) ? $field['width'] : '';
        
        $content = '<label class="form-label">' . $field['label'] . '</label>
            <span class="' . $textClass . '">' . htmlspecialchars($value) . '</span>';

        if (!$isTwoColumnLayout) {
            return '<div class="d-flex ' . $gap . ' ' . $align . '">' . $content . '</div>';
        }
        
        return $content;
    }

    /**
     * Render HTML content
     */
    private function renderHtml($field, $value, $name, $isTwoColumnLayout = false) {
        $gap = isset($field['gap']) ? $field['gap'] : 'gap-40';
        $align = isset($field['align']) ? $field['align'] : 'align-items-center';
        
        // Nếu label là HTML, không cần wrap trong form-label
        $label = isset($field['is_label_html']) && $field['is_label_html'] 
            ? $field['label']
            : '<label class="form-label">' . $field['label'] . '</label>';
        
        $content = $label;

        if (!$isTwoColumnLayout) {
            return '<div class="d-flex ' . $gap . ' ' . $align . '">' . $content . '</div>';
        }
        
        return $content;
    }

    /**
     * Render rich text editor
     */
    private function renderRichTextEditor($field, $value, $name, $isTwoColumnLayout = false) {
        $label = isset($field['label']) ? $field['label'] : '';
        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
        $rows = isset($field['rows']) ? $field['rows'] : '10';
        
        $content = '<label class="form-label">' . $label . '</label>
            <textarea class="form-control rich-text-editor" name="' . $name . '" id="' . $name . '" rows="' . $rows . '" placeholder="' . $placeholder . '">' . htmlspecialchars($value) . '</textarea>';

        if (!$isTwoColumnLayout) {
            return $content;
        }
        
        return $content;
    }

    /**
     * Render field pair (two fields with labels in one row)
     */
    private function renderFieldPair($field, $data, $type, $isTwoColumnLayout = false) {
        // Render inputs row
        $html = '<div class="d-flex gap-40">';
        foreach ($field['fields'] as $subField) {
            $html .= '<div class="d-flex gap-40 w-100 align-items-center">';
            $html .= $this->renderField($subField, $data, $type, true);
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }
    
    /**
     * Convert field to display mode for product view
     */
    private function convertFieldToDisplayMode($field) {
        $displayField = $field;
        $displayField['original_type'] = $field['type'];
        
        switch ($field['type']) {
            case 'text':
            case 'text_group':
            case 'textarea':
            case (preg_match('/^search_/', $field['type']) ? $field['type'] : null):
            case 'select':
            case 'image':
            case 'editor':
            case 'field_group':
                $displayField['type'] = 'display';
                break;
            case 'radio':
                // Keep radio as is but add disabled attribute
                $displayField['disabled'] = true;
                break;
            case 'checkbox':
                // Keep checkbox as is but add disabled attribute
                $displayField['disabled'] = true;
                break;
        }
        
        return $displayField;
    }
    
    /**
     * Render display field (for view mode)
     */
    private function renderDisplayField($field, $value, $name, $isTwoColumnLayout = false) {
        $isFieldGroup = $field['original_type'] === 'field_group';
        if(!$isFieldGroup) {
            $content = '<label class="form-label">' . $field['label'] . '</label>';
        }
        
        // Handle different original field types
        $originalType = $field['original_type'] ?? 'text';
        
        switch ($originalType) {
            case 'textarea':
                $content .= '<div class="content">' . nl2br(htmlspecialchars($value)) . '</div>';
                break;
            case 'select':
                // Get label from options array
                $label = $value;
                if (isset($field['options'][$value])) {
                    $label = $field['options'][$value];
                }
                $content .= '<div class="content">' . htmlspecialchars($label) . '</div>';
                break;
            case 'image':
                if (!empty($value)) {
                    $filename = basename($value);
                    $content .= '<div class="content text-decoration-underline"><a href="' . $value . '" target="_blank">' . htmlspecialchars($filename) . '</a></div>';
                } else {
                    $content .= '<div class="content text-muted">파일이 선택되지 않았습니다.</div>';
                }
                break;
            case 'editor':
                $content .= '<div class="content">' . $value . '</div>';
                break;
            case 'checkbox':
                if (is_array($value)) {
                    $content .= '<div class="content">' . implode(', ', $value) . '</div>';
                } else {
                    $content .= '<div class="content">' . htmlspecialchars($value) . '</div>';
                }
                break;
            case 'text_group':
                $content .= '<div class="content">';
                $content .= implode(' / ', array_map('htmlspecialchars', $value));
                $content .= '</div>';
                break;
            case 'field_group':
                foreach ($field['fields'] as $subField) {
                    $subFieldKey = 'content_' . $subField['key'];
                    
                    // Extract value from content_info array, fallback to default
                    $subValue = $value['content_info'][$subFieldKey] ?? $subField['default'] ?? '';
                    
                    // If subValue is an image URL, show only filename
                    $displayValue = $subValue;
                    if ($subField['type'] === 'image' && filter_var($subValue, FILTER_VALIDATE_URL)) {
                        $displayValue = basename($subValue);
                    }
                    
                    $content .= '<div class="d-flex gap-40 w-100 align-items-center">';
                    $content .= '<label class="form-label">' . $subField['label'] . '</label> ';
                    $content .= '<div class="content">' . htmlspecialchars($displayValue) . '</div>';
                    $content .= '</div>';
                }
                break;
            default: // text
                $content .= '<div class="content">' . (is_numeric($value) ? number_format($value) : htmlspecialchars($value)) . '</div>';
                break;
        }
        
        if (!$isTwoColumnLayout) {
            if (strpos($name, 'product_') !== false) {
                $display = $originalType === 'editor' ? 'block' : 'flex';
                return '<div class="d-' . $display . ' column-gap-30 w-100">' . $content . '</div>';
            }

            if($isFieldGroup) {
                return '<div class="d-flex">' . $content . '</div>';
            }

            return '<div class="d-flex gap-40">' . $content . '</div>';            
        }
        
        return $content;
    }
    
    /**
     * Render search field with AJAX functionality
     */
    private function renderSearch($field, $value, $name, $isTwoColumnLayout = false, $id_value = '') {
        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '검색어를 입력하세요...';
        $gap = isset($field['gap']) ? $field['gap'] : 'gap-40';

        
        $content = '<label class="form-label">' . $field['label'] . '</label>
            <div class="search-container w-100">
                <input type="text" class="form-control search-input" search-type="' . $field['type'] . '" name="' . $name . '" 
                       value="' . htmlspecialchars($value) . '" placeholder="' . $placeholder . '" autocomplete="off">
                <input type="hidden" name="' . $name . '_id" value="' . htmlspecialchars($id_value) . '" title="' . htmlspecialchars($value) . '">
                <div class="search-results" style="display: none;"></div>
            </div>';

        if (!$isTwoColumnLayout) {
            return '<div class="d-flex ' . $gap . ' align-items-center">' . $content . '</div>';
        }
        
        return $content;
    }
}
