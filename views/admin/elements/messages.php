<?php
$errors = Herisson\Message::i()->getErrors();
$success = Herisson\Message::i()->getSuccess();
?>

<?php if (Herisson\Message::i()->hasErrors()) { ?>
<p class="herisson-errors">
    <?php foreach (Herisson\Message::i()->getErrors() as $error) { ?>
    <?php echo $error; ?><br />
    <?php } ?>
</p>
<div style="clear: both"></div>
<?php } ?>


<?php if (Herisson\Message::i()->hasSuccess()) { ?>
<p class="herisson-success">
    <?php foreach (Herisson\Message::i()->getSuccess() as $succes) { ?>
    <?php echo $succes; ?><br />
    <?php } ?>
</p>
<?php } ?>

