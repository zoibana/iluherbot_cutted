
<h2>Поиск по комментам Илюхеров</h2>
<form>
    <input type="text" name="term" value="<?=$term?>"?> <button type="submit">Искать</button>
</form>
<hr>
<h4>Результаты</h4>
<?php
if(!empty($models)){
	foreach($models as $model){
		?>
	<div>
		<a href="https://tjournal.ru/<?=$model['post_id']?>#comment-<?=$model['comment_id']?>" target="_blank"><?=$model['comment_id']?></a>:
		<?= preg_replace('#('.preg_quote($term, '#').')#ui','<strong>$1</strong>', $model['comment_text']) ?>
	</div>
<?
	}
}