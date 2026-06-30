<?php

use function App\Core\encodeToJson;

$this->pushJs("components/statsList.js");

$items ??= [];
$params = encodeToJson([
    "page" => $paramPage ?? "",
    "action" => $paramAction ?? "",
    "valueKey" => $valueKey ?? "",
]);
$xData = htmlspecialchars("stat({$params})");

$statCard = function (
    string $title = "",
    string $valueKey = "",
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
                            <h2 class="fw-bold mb-0" x-text="data[<?= $valueKey ?>]"></h2>
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

<div class="container py-3" x-data="{$xData}" x-init="refresh()">
    <?php foreach ($items as $i): ?>
        <div class="row g-4">
            <?= $statCard(
                valueKey: "total_clientes",
                title: "Clientes Totales",
                iconClass: "fa-user",
                iconContainer: "bg-success-subtle text-success",
            ) ?>

            <?= $statCard(
                valueKey: "total_clientes",
                title: "Suscripciones Mensuales",
                iconClass: "fa-user",
                iconContainer: "bg-primary-subtle text-primary",
            ) ?>
        </div>
    <?php endforeach ?>
</div>