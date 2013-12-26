<?php
$errors = HerissonMessage::i()->getErrors();
$success = HerissonMessage::i()->getSuccess();
?>

<?php if (HerissonMessage::i()->hasErrors()) { ?>
<p class="herisson-errors">
    <?php foreach (HerissonMessage::i()->getErrors() as $error) { ?>
    <?php echo $error; ?><br />
    <?php } ?>
</p>
<div style="clear: both"></div>
<?php } ?>


<?php if (HerissonMessage::i()->hasSuccess()) { ?>
<p class="herisson-success">
    <?php foreach (HerissonMessage::i()->getSuccess() as $succes) { ?>
    <?php echo $succes; ?><br />
    <?php } ?>
</p>
<?php } ?>

