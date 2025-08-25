<h1 class="page-title"><a href="/admin/section/">웹사이트 관리</a><span class="spreader"></span>콘텐츠 상세</h1>
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