<?php
$file = 'resources/views/admin/jurusan/index.blade.php';
$content = file_get_contents($file);

$jsLogic = <<<JS

    // Modal Edit logic
    var editModalEl = document.getElementById('editJurusanModal');
    if (editModalEl) {
        document.body.appendChild(editModalEl);
        var editModal = new bootstrap.Modal(editModalEl);
    }

    $('#jurusan-table').on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var code = $(this).data('code');
        var name = $(this).data('name');

        $('#edit_code').val(code);
        $('#edit_name').val(name);

        var actionUrl = "/admin/jurusan/" + id;
        $('#editJurusanForm').attr('action', actionUrl);

        if(editModal) {
            editModal.show();
        }
    });

</script>
JS;

if (strpos($content, '.btn-edit') === false) {
    // Replace the closing </script> with our logic + closing script
    $content = str_replace('</script>', $jsLogic, $content);
    file_put_contents($file, $content);
    echo "JS injected successfully.\n";
} else {
    echo "JS already exists.\n";
}
?>
