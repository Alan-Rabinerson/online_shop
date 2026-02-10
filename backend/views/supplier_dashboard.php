<?php
	include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php';
?>

<main class="bg-azul-oscuro min-h-screen p-6 text-beige">
	<div class="max-w-6xl mx-auto">
		<div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
			<div>
				<h1 class="text-3xl font-bold mb-2 text-beige">Dashboard de Proveedores</h1>
				<p class="text-azul-claro">Actualiza el catálogo con los productos recibidos desde las APIs de cada tienda.</p>
			</div>
			<div class="flex gap-3">
				<a href="/student024/Shop/backend/views/products.php" class="boton-azul">Ver productos</a>
			</div>
		</div>

		<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
			<section class="bg-azul-claro text-beige rounded-lg p-6 shadow-md border border-azul-oscuro">
				<h2 class="text-xl font-semibold mb-2">Teamwear</h2>
				<p class="text-sm opacity-80">Sincroniza los productos del proveedor Teamwear.</p>
				<p class="text-xs mt-2 opacity-80">Nota: se reemplazan los productos existentes de este proveedor.</p>
				<form action="/student024/Shop/backend/suppliers/teamwear.php" method="POST" class="mt-4">
					<button type="submit" class="boton-rojo">Actualizar productos</button>
				</form>
			</section>

			<section class="bg-azul-claro text-beige rounded-lg p-6 shadow-md border border-azul-oscuro">
				<h2 class="text-xl font-semibold mb-2">Shift&Go</h2>
				<p class="text-sm opacity-80">Sincroniza los productos del proveedor Shift&Go.</p>
				<p class="text-xs mt-2 opacity-80">Nota: se reemplazan los productos existentes de este proveedor.</p>
				<form action="/student024/Shop/backend/suppliers/shift_and_go.php" method="POST" class="mt-4">
					<button type="submit" class="boton-rojo">Actualizar productos</button>
				</form>
			</section>

			<section class="bg-azul-claro text-beige rounded-lg p-6 shadow-md border border-azul-oscuro">
				<h2 class="text-xl font-semibold mb-2">BRAND2</h2>
				<p class="text-sm opacity-80">Sincroniza los productos del proveedor BRAND2.</p>
				<p class="text-xs mt-2 opacity-80">Nota: se reemplazan los productos existentes de este proveedor.</p>
				<form action="/student024/Shop/backend/suppliers/brand2.php" method="POST" class="mt-4">
					<button type="submit" class="boton-rojo">Actualizar productos</button>
				</form>
			</section>
		</div>
	</div>

<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php'; ?>
