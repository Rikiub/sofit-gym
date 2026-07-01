<?php

use function App\Core\encodeToJson;

$this->pushJs("components/statsList.js");

// Props
$items ??= [];
$itemsCount = count($items);

$mapKey ??= "";
$params = encodeToJson(["params" => $params ?? []]);
$xData = htmlspecialchars("stat({$params})");

$statCard = function (
    int $listLength,
    string $title = "",
    string $mapKey = "",
    string $iconClass = "",
    string $iconContainer = "",
) {
    $class = $listLength === 2
        ? "col-lg-6"
        : "col-lg-4";

    return <<<HTML
        <article class="col-12 col-sm-6 {$class}">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold small mb-1">{$title}</h6>
                            <h2 class="fw-bold mb-0" x-text="{$mapKey}"></h2>
                        </div>
                        
                        <div class="rounded-3 p-3 {$iconContainer}">
                            <i class="fa {$iconClass} fs-3"></i>
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
            <?= $statCard($itemsCount, ...$i) ?>
        <?php endforeach ?>
    </div>
</div>