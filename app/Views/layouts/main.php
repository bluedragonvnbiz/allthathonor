<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=no">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!-- Header -->
<?php $this->partial('common/header'); ?>

<!-- Main Content -->
<main>
    <?php echo $content; ?>
</main>

<!-- Footer -->
<?php $this->partial('common/footer'); ?>

<?php wp_footer(); ?>
</body>
</html> 