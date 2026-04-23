<?php

$article = $article ?? 'overview';
$section = $section ?? '';
$url = route('larecipe.show', ['version' => 'de', 'page' => $article]) . ($section ? '#' . $section : '');

?>

<div class="my-2 d-flex justify-content-end">
    <a href="{{ $url }}" target="_blank">
        <span class="material-icons text-dark opacity-3 cursor-pointer mi-2x" data-toggle="tooltip" data-placement="top"
              title="Dokumentation">help_outline </span>
    </a>
</div>
