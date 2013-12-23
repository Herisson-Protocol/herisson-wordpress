<?
$errors = HerissonMessage::i()->getErrors();
$success = HerissonMessage::i()->getSuccess();
?>

<? if (HerissonMessage::i()->hasErrors()) { ?>
<p class="herisson-errors">
    <? foreach (HerissonMessage::i()->getErrors() as $error) { ?>
    <? echo $error; ?><br />
    <? } ?>
</p>
<? } ?>


<? if (HerissonMessage::i()->hasSuccess()) { ?>
<p class="herisson-success">
    <? foreach (HerissonMessage::i()->getSuccess() as $succes) { ?>
    <? echo $succes; ?><br />
    <?} ?>
</p>
<? } ?>

