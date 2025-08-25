<h1 class="page-title"><?= $sectionName ?></h1>
<?= $formHtml ?>

<script>
// Set section_page parameter for forms
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.section-form');
    forms.forEach(form => {
        form.dataset.sectionPage = '<?= $sectionPage ?? 'main' ?>';
    });
});
</script>