<?php if(!$postComments): ?>
<script>
    $("#commentForm").parent().parent().remove();
    $(".panel-actions.pull-right").remove();
</script>
<?php endif; ?>
