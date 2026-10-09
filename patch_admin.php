<?php
$file = 'resources/views/layouts/admin.blade.php';
$content = file_get_contents($file);

$js = <<<'HTML'
    // Initialize tooltips globally
    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            // dispose old instance if exists
            var instance = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
            if (instance) instance.dispose();
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        initTooltips();
    });

    $(document).on('draw.dt', function() {
        initTooltips();
    });
</script>
HTML;

$content = str_replace("</script>\r\n\r\n@stack('scripts')", $js . "\r\n\r\n@stack('scripts')", $content);
$content = str_replace("</script>\n\n@stack('scripts')", $js . "\n\n@stack('scripts')", $content);
file_put_contents($file, $content);
echo "Done";
?>
