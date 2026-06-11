<?php
$this->layout('base', ['title' => 'Error']);

// Props
$message ??= "";
?>

<h1>Error</h1>
<p><?= $this->e($message) ?></p>