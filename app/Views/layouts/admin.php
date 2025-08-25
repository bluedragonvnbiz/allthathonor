<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=no">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div class="d-flex wrap-management">
    <div class="main-sidebar bg-white flex-shrink-0<?php if(!empty($_COOKIE['sidebar-collapsed']) && $_COOKIE['sidebar-collapsed'] == "true") echo " sidebar-collapsed"; ?>">
        <?php $this->partial('common/admin-sidebar') ?>		
    </div><!--end sidebar-->

    <div class="w-100">
        <?php $this->partial('common/admin-header') ?>
        
        <div class="main-content container-fluid">
            <?php echo $content; ?>
        </div>
    </div><!--end main_content-->

    <?php wp_footer(); ?>
    <?php $this->partial('common/admin-footer') ?>
</div>
</body>
</html> 