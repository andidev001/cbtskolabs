<?php
$file = 'resources/views/admin/religion/index.blade.php';
$content = file_get_contents($file);

$jsLogic = <<<JS

    // Modal Create manual logic
    var btnOpenCreate = document.querySelector('[data-bs-target="#createReligionModal"]');
    if(btnOpenCreate) {
        btnOpenCreate.addEventListener('click', function(e) {
            e.preventDefault();
            var modal = new bootstrap.Modal(document.getElementById('createReligionModal'));
            modal.show();
        });
        btnOpenCreate.removeAttribute('data-bs-toggle');
    }

</script>
JS;

if (strpos($content, 'btnOpenCreate') === false) {
    // Replace the closing </script> with our logic + closing script
    $content = preg_replace('/<\/script>/', $jsLogic, $content, 1);
    file_put_contents($file, $content);
    echo "Create modal JS injected successfully.\n";
} else {
    echo "Create modal JS already exists.\n";
}
?>
