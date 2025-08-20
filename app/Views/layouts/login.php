<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('login-page'); ?>>
    <div id="login-container">
        <?php echo $content; ?>
    </div>
    <?php wp_footer(); ?>
</body>
</html> 