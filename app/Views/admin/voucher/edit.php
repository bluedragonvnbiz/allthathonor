<h1 class="page-title"><?= $sectionName ?></h1>
<?= $formHtml ?>

<script>
// Add return URL to voucher form after it's rendered
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[data-voucher-form], .voucher-form, form');
    if (form) {
        // Add hidden field for return URL
        const returnUrlInput = document.createElement('input');
        returnUrlInput.type = 'hidden';
        returnUrlInput.name = 'return_url';
        returnUrlInput.value = '<?= htmlspecialchars($returnUrl ?? '/admin/voucher/') ?>';
        form.appendChild(returnUrlInput);
    }
});
</script>