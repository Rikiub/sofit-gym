<?php

use function App\Core\encodeToJson;

$this->pushJs("components/statsList.js");

// Props
$items ??= [];
$mapKey ??= "";
$params = encodeToJson(["params" => $params ?? []]);
$xData = htmlspecialchars("stat({$params})");

$statCard = function (
    string $title = "",
    string $mapKey = "",
    string $iconClass = "",
    string $iconContainer = "",
) {
    return <<<HTML
        <article class="col-12 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold small mb-1">{$title}</h6>
                            <h2 class="fw-bold mb-0" x-text="{$mapKey}"></h2>
                        </div>
                        
                        <div class="rounded-3 p-3 {$iconContainer}">
                            <i class="fa {$iconClass}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    HTML;
}
?>

<div class="container py-3" x-data="<?= $xData ?>" x-init="refresh()">
    <div class="row g-4">
        <?php foreach ($items as $i): ?>
            <?= $statCard(...$i) ?>
        <?php endforeach ?>
    </div>
</div>