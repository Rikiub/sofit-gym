<?php

/** @var \App\Helpers\Auth\UsuarioSessionDTO $sesion_usuario */

$link = function (
	string $page,
	string $icon,
	string $title,
	string|null $permiso = null,
) use ($sesion_usuario): string {
	if ($permiso === null || $sesion_usuario->hasPermiso($permiso)) {
		return <<<HTML
            <a href="?page={$page}" class="nav-single">
                <i class="fas {$icon}"></i>
                <span>{$title}</span>
            </a>
        HTML;
	} else {
		return "";
	}
};

$dropdown = function (
	string $icon,
	string $title,
	array $items,
) use ($sesion_usuario): string {
	$htmlItems = "";
	foreach ($items as $i) {
		$itemPermiso = $i["permiso"] ?? null;
		$itemPage = $i["page"] ?? "";
		$itemIcon = $i["icon"] ?? "";
		$itemTitle = $i["title"] ?? "";

		if ($itemPermiso === null || $sesion_usuario->hasPermiso($itemPermiso)) {
			$htmlItems .= <<<HTML
                <a href="?page={$itemPage}">
                    <i class="fas {$itemIcon}"></i>
                    <span>{$itemTitle}</span>
                </a>
            HTML;
		}
	}

	// Si ninguna de las sub-páginas tiene permiso, evitamos renderizar el menú vacío
	if (empty(trim($htmlItems))) {
		return "";
	}

	return <<<HTML
        <details class="nav-group">
            <summary class="group-title">
                <i class="fas {$icon}"></i>
                <span>{$title}</span>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </summary>

            <div class="group-items">
                {$htmlItems}
            </div>
        </details>
    HTML;
};
?>

<script type="module">
	import Alpine from "alpinejs";
	import {
		fetchApi
	} from "@/js/api.js";

	Alpine.data("sidebar", () => ({
		collapsed: localStorage.getItem("sidebarCollapsed") === "true",

		toggle() {
			this.collapsed = !this.collapsed;
			localStorage.setItem("sidebarCollapsed", this.collapsed);
		},
	}));
</script>

<aside x-data="sidebar" class="sidebar" :class="{ collapsed }">
	<script>
		// Sincronizar clase 'collapsed' antes de renderizar
		(() => {
			const sidebar = document.currentScript.parentElement;
			const collapsed = localStorage.getItem("sidebarCollapsed") === "true";
			if (collapsed) sidebar.classList.add("collapsed");
		})()
	</script>

	<div class="pb-5 d-flex justify-content-end">
		<div class="logo-container" style="height: 100px;" x-show="!collapsed" x-transition>
			<img src="assets/logo.webp" class="img-fluid">
		</div>

		<button class="sidebar-toggle" @click="toggle" aria-label="Colapsar menú">
			<i class="fas fa-chevron-left"></i>
		</button>
	</div>

	<nav class="sidebar-nav">
		<a href="?page=dashboard" class="active">
			<i class="fas fa-home"></i>
			<span>Panel de control</span>
		</a>

		<hr>

		<?= $link(
			permiso: "clientes:ver",
			page: "clientes",
			icon: "fa-id-card",
			title: "Gestión de Clientes"
		) ?>

		<?= $link(
			permiso: "trabajadores:ver",
			page: "trabajadores",
			icon: "fa-id-card",
			title: "Gestión de Trabajadores"
		) ?>

		<hr>

		<?= $link(
			permiso: "facturacion:ver",
			page: "facturacion",
			icon: "fa-coins",
			title: "Facturación y Control de Pagos"
		) ?>

		<?= $link(
			permiso: "asistencia:ver",
			page: "asistencia",
			icon: "fa-fingerprint",
			title: "Control de Asistencia"
		) ?>

		<?= $link(
			permiso: "clasesGrupales:ver",
			page: "clasesGrupales",
			icon: "fa-calendar",
			title: "Horarios de clases"
		) ?>

		<?= $dropdown(
			icon: "fa-dumbbell",
			title: "Rutinas de Entrenamiento",
			items: [
				[
					"permiso" => "rutinas:ver",
					"page" => "rutinas&action=index",
					"icon" => "fa-pen-ruler",
					"title" => "Planes de entrenamiento"
				],
				[
					"permiso" => "rutinas:ver",
					"page" => "rutinas&action=asignadas",
					"icon" => "fa-user-check",
					"title" => "Asignación de rutinas"
				]
			]
		) ?>

		<hr>

		<?= $dropdown(
			icon: "fa-microchip",
			title: "Equipos y Maquinaria",
			items: [
				[
					"permiso" => "equipos:ver",
					"page" => "equipos",
					"icon" => "fa-tools",
					"title" => "Inventario de equipos"
				],
				[
					"permiso" => "equipos:ver",
					"page" => "equiposMantenimiento",
					"icon" => "fa-history",
					"title" => "Historial de mantenimientos"
				]
			]
		) ?>

		<?= $link(
			permiso: "productos:ver",
			page: "productos",
			icon: "fa-boxes",
			title: "Inventario de Productos"
		) ?>

		<?= $link(
			permiso: "asistente:ver",
			page: "asistente",
			icon: "fa-robot",
			title: "Asistente de Entrenamiento"
		) ?>

		<hr>

		<?= $dropdown(
			icon: "fa-shield-alt",
			title: "Auditoría y seguridad",
			items: [
				[
					"permiso" => "usuarios:ver",
					"page" => "usuarios",
					"icon" => "fa-user",
					"title" => "Usuarios"
				],
				[
					"permiso" => "roles:ver",
					"page" => "roles",
					"icon" => "fa-lock",
					"title" => "Roles y Permisos"
				],
				[
					"permiso" => "bitacora:ver",
					"page" => "bitacora",
					"icon" => "fa-history",
					"title" => "Bitácora"
				]
			]
		) ?>

		<?= $dropdown(
			icon: "fa-database",
			title: "Soporte y Datos",
			items: [
				[
					"permiso" => "roles:ver",
					"page" => "reportes",
					"icon" => "fa-chart-bar",
					"title" => "Reportes estadísticos"
				],
				[
					"permiso" => "roles:ver",
					"page" => "sistema",
					"icon" => "fa-database",
					"title" => "Mantenimiento del sistema"
				],
				[
					"permiso" => "roles:ver",
					"page" => "sistema",
					"icon" => "fa-question-circle",
					"title" => "Manual de usuario"
				]
			]
		) ?>
	</nav>

	<div class="sidebar-footer text-center">
		<span>SOFIT GYM&copy;</span>
	</div>
</aside>

<style>
	.sidebar {
		--sidebar-bg: rgba(30, 41, 59, 0.85);
		--sidebar-text: #e2e8f0;
		--sidebar-accent: var(--primary-bg);
		--sidebar-muted: #cbd5e1;
		--sidebar-border: rgba(255, 255, 255, 0.1);
		--sidebar-hover: rgba(255, 255, 255, 0.1);
		--sidebar-radius: 12px;

		width: 280px;
		background: var(--sidebar-bg);
		color: var(--sidebar-text);
		display: flex;
		flex-direction: column;
		height: 100%;
		overflow-y: auto;
		border-right: 1px solid var(--sidebar-border);
		transition: width 0.3s ease;
		scrollbar-width: none;

		/* ===== ESTADO COLAPSADO ===== */
		&.collapsed {
			width: 80px;

			.logo-container h2,
			.sidebar-actions span,
			.sidebar-nav a span,
			.group-title span,
			.group-items a span,
			.sidebar-footer span {
				display: none;
			}

			.group-items {
				padding-left: 0;
			}

			.group-title {
				justify-content: center;

				.toggle-icon {
					display: none;
				}
			}

			.sidebar-nav>a,
			.sidebar-nav>a.nav-single {
				justify-content: center;
			}

			.sidebar-nav a i {
				margin: 0;
			}

			.sidebar-toggle i {
				rotate: 180deg;
			}
		}

		.sidebar-toggle {
			background: transparent;
			border: none;
			color: #94a3b8;
			cursor: pointer;
			font-size: 0.9rem;
			padding: 6px;
			border-radius: 50%;
			transition: background 0.2s, color 0.2s;
			display: flex;
			align-items: center;
			justify-content: center;

			&:hover {
				background: var(--sidebar-hover);
				color: var(--sidebar-muted);
			}
		}

		/* ===== NAVEGACIÓN ===== */
		.sidebar-nav {
			padding: 0 0.8rem;
			flex: 1;

			>a {
				display: flex;
				align-items: center;
				gap: 12px;
				padding: 0.5rem 0.8rem;
				color: var(--sidebar-text);
				text-decoration: none;
				border-radius: var(--sidebar-radius);
				margin: 0.3rem 0;

				&:hover,
				&.active {
					background: var(--sidebar-accent);
					color: white;
				}
			}

			/* Single‑item link style (mimics group‑title appearance) */
			>a.nav-single {
				font-size: 0.75rem;
				font-weight: 600;
				text-transform: uppercase;
				color: var(--sidebar-muted);
				gap: 15px;
				margin-bottom: 0.4rem;

				>i:first-child {
					min-width: 20px;
					text-align: center;
				}

				span {
					flex: 1;
					white-space: normal;
					line-height: 1.2;
				}

				&:hover {
					background-color: var(--sidebar-hover);
					color: var(--sidebar-muted);
				}
			}
		}

		/* ===== GRUPOS COLAPSABLES (details/summary) ===== */
		.nav-group {
			margin-bottom: 0.4rem;
		}

		.group-title {
			display: flex;
			align-items: center;
			gap: 15px;
			font-size: 0.75rem;
			font-weight: 600;
			text-transform: uppercase;
			color: var(--sidebar-muted);
			padding: 0.5rem 0.8rem;
			cursor: pointer;
			border-radius: var(--sidebar-radius);

			>i:first-child {
				min-width: 20px;
				text-align: center;
			}

			span {
				flex: 1;
				white-space: normal;
				line-height: 1.2;
			}

			&:hover {
				background: var(--sidebar-hover);
			}

			.toggle-icon {
				transition: rotate 0.2s ease;
				font-size: 0.7rem;
			}
		}

		/* Rota el chevrón cuando el acordeón está abierto */
		.nav-group[open] .toggle-icon {
			rotate: 180deg;
		}

		.group-items {
			padding-left: 1.2rem;
			display: flex;
			flex-direction: column;

			a {
				display: flex;
				align-items: center;
				gap: 12px;
				padding: 0.4rem 0.8rem;
				color: var(--sidebar-muted);
				text-decoration: none;
				border-radius: var(--sidebar-radius);
				margin-bottom: 2px;

				&:hover {
					background: var(--sidebar-hover);
					color: var(--sidebar-muted);
				}
			}
		}

		/* ===== FOOTER Y DIVISOR ===== */
		.sidebar-footer {
			padding: 0.8rem;
			border-top: 1px solid var(--sidebar-border);
			font-size: 0.7rem;
			text-align: left;

			i {
				margin-right: 6px;
			}
		}

		.sidebar-divider {
			height: 1px;
			background: var(--sidebar-border);
			margin: 0.6rem 0;
		}
	}
</style>