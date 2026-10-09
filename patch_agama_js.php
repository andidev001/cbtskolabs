<?php
$file = 'resources/views/admin/religion/index.blade.php';
$content = file_get_contents($file);

$jsLogic = <<<JS

    // Modal Edit & Create logic
    var editModalEl = document.getElementById('editReligionModal');
    if (editModalEl) {
        document.body.appendChild(editModalEl);
    }
    var createModalEl = document.getElementById('createReligionModal');
    if (createModalEl) {
        document.body.appendChild(createModalEl);
    }

    $('#religion-table').on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#edit_name').val(name);

        var actionUrl = "/admin/religion/" + id;
        $('#editReligionForm').attr('action', actionUrl);

        var editModal = new bootstrap.Modal(document.getElementById('editReligionModal'));
        editModal.show();
    });

</script>
JS;

if (strpos($content, '.btn-edit') === false) {
    // Replace the closing </script> with our logic + closing script
    $content = preg_replace('/<\/script>/', $jsLogic, $content, 1);
    file_put_contents($file, $content);
    echo "JS injected successfully.\n";
} else {
    echo "JS already exists.\n";
}
?>
