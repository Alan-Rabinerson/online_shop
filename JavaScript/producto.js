let divproductImages = document.querySelector(".product-images");
let mainImage = divproductImages.querySelector("img");
let additionalImages = divproductImages.querySelectorAll(
  ".additional-images img",
);
let anadirCarritoBtn = document.getElementById("añadir-carrito-btn");
let crtCount = document.querySelector(".cart-count");

additionalImages.forEach((image) => {
  image.addEventListener("click", () => {
    imagenPrincipal = mainImage.src;
    mainImage.src = image.src;
    image.src = imagenPrincipal;
  });
});

anadirCarritoBtn.addEventListener("click", (e) => {
  e.preventDefault();
  let currentCount = parseInt(crtCount.textContent);
  let cantidad = parseInt(document.getElementById("cantidad").value);
  crtCount.textContent = currentCount + cantidad;
  console.log("Añadido al carrito");
});

document.addEventListener("DOMContentLoaded", () => {
  loadProductDetail();
  cargarReviews();
});

// Cargar detalles del producto desde la BD usando XMLHttpRequest
function loadProductDetail() {
  try {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get("id");

    if (!productId) {
      console.error("No product ID provided");
      return;
    }

    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
      if (this.readyState === 4) {
        if (this.status === 200) {
          try {
            const product = JSON.parse(this.responseText);

            if (!product || product.error) {
              console.error("Product not found");
              return;
            }

            // Actualizar título de la página
            const titleEl = document.querySelector("main h1");
            if (titleEl) titleEl.textContent = product.product_name;

            // Actualizar imagen principal (usar nombre distinto para evitar colisiones)
            const mainImg = document.querySelector(".product-images img");
            if (mainImg) {
              mainImg.src = `../assets/imagenes/foto${product.product_id}.jpg`;
              mainImg.alt = product.product_name || "";
            }
            const additionalImgs = document.querySelectorAll(
              ".additional-images img",
            );
            if (additionalImgs) {
              additionalImgs.forEach((img, index) => {
                img.src = `../assets/imagenes/foto-detalle-${product.product_id}-${
                  index + 1
                }.png`;
                img.alt = product.product_name || "";
              });
            }

            // Actualizar precio
            const priceElement = document.querySelector("#precio .text-2xl");
            if (priceElement) {
              const priceNum =
                typeof product.price !== "undefined"
                  ? Number(product.price)
                  : NaN;
              priceElement.textContent = !isNaN(priceNum)
                ? `${priceNum.toFixed(2)}€`
                : product.price || "";
            }

            // Actualizar descripción
            const descriptionElement = document.querySelector(
              ".product-description p",
            );
            if (descriptionElement) {
              descriptionElement.textContent =
                product.description || "Sin descripción disponible";
            }

            // Materiales
            const materialesList = document.getElementById("materiales");
            if (materialesList && Array.isArray(product.materials)) {
              materialesList.innerHTML = "";
              product.materials.forEach((material) => {
                const li = document.createElement("li");
                li.className = "mb-2";
                li.textContent =
                  material.material_name +
                  (material.percentage ? `: ${material.percentage}%` : "");
                materialesList.appendChild(li);
              });
            }

            // Guardar product_id en un campo oculto para las reviews
            let productIdInput = document.getElementById("productId");
            if (!productIdInput) {
              productIdInput = document.createElement("input");
              productIdInput.type = "hidden";
              productIdInput.id = "productId";
              document.body.appendChild(productIdInput);
            }
            productIdInput.value = product.product_id;
            // Stock handling: display total stock and per-size stock
            const stockInfoEl = document.getElementById("stock-info");
            const cantidadInput = document.getElementById("cantidad");
            const addBtn = document.getElementById("añadir-carrito-btn");
            let totalStock =
              typeof product.total_stock !== "undefined"
                ? Number(product.total_stock)
                : 0;
            // If sizes provided, map stock to radio inputs
            let currentStock = totalStock;
            try {
              const sizeRadios = document.querySelectorAll(
                'input[name="talla"]',
              );
              if (
                Array.isArray(product.sizes) &&
                product.sizes.length > 0 &&
                sizeRadios.length > 0
              ) {
                // set data-stock on radios
                sizeRadios.forEach((radio) => {
                  const val = radio.value;
                  const found = product.sizes.find(
                    (s) => s.size == val || String(s.size) === String(val),
                  );
                  const stockVal = found ? Number(found.stock) : 0;
                  radio.dataset.stock = stockVal;
                });
                // pick the checked radio or the first
                const checked = document.querySelector(
                  'input[name="talla"]:checked',
                );
                if (checked) {
                  currentStock = Number(checked.dataset.stock || 0);
                } else {
                  currentStock = Number(product.sizes[0].stock || 0);
                  // mark first radio checked if exists
                  const firstRadio = document.querySelector(
                    'input[name="talla"]',
                  );
                  if (firstRadio) firstRadio.checked = true;
                }
                // listen for size changes
                sizeRadios.forEach((r) =>
                  r.addEventListener("change", (ev) => {
                    const s = ev.target;
                    currentStock = Number(s.dataset.stock || 0);
                    updateStockUI();
                  }),
                );
              }
            } catch (e) {
              console.error("Error mapping sizes stock:", e);
            }

            function updateStockUI() {
              if (stockInfoEl)
                stockInfoEl.textContent =
                  currentStock > 0
                    ? `Stock: ${currentStock} unidades`
                    : "Sin stock";
              if (cantidadInput) {
                cantidadInput.max = Math.max(1, currentStock);
                if (currentStock <= 0) {
                  cantidadInput.value = 0;
                  cantidadInput.setAttribute("disabled", "disabled");
                } else {
                  if (Number(cantidadInput.value) < 1) cantidadInput.value = 1;
                  cantidadInput.removeAttribute("disabled");
                }
              }
              if (addBtn) {
                if (currentStock <= 0) {
                  addBtn.textContent = "Sin stock";
                  addBtn.classList.add("opacity-50");
                  addBtn.setAttribute("disabled", "disabled");
                } else {
                  addBtn.textContent = "Agregar al Carrito";
                  addBtn.classList.remove("opacity-50");
                  addBtn.removeAttribute("disabled");
                }
              }
            }

            // initial update
            updateStockUI();
            // when adding to cart, ensure not exceeding stock and decrement shown stock locally
            if (addBtn) {
              addBtn.addEventListener("click", (ev) => {
                // if disabled, no-op
                if (addBtn.hasAttribute("disabled")) return;
                const qty =
                  Number(document.getElementById("cantidad").value) || 0;
                if (qty <= 0) return;
                if (qty > currentStock) {
                  const errorMsg = document.createElement("div");
                  errorMsg.textContent = "No hay stock suficiente";
                  errorMsg.className = "text-red-500 text-sm mt-2";
                  addBtn.parentNode.appendChild(errorMsg);
                  setTimeout(() => errorMsg.remove(), 3000);
                  return;
                }
                currentStock = Math.max(0, currentStock - qty);
                updateStockUI();
                // increment cart counter handled elsewhere; keep existing behavior
              });
            }
          } catch (e) {
            console.error("Error parsing JSON response:", e);
          }
        } else {
          console.error("Failed to load product detail. Status:", this.status);
        }
      }
    };

    xhr.open(
      "GET",
      `/student024/Shop/backend/endpoints/product_detail/show_product_detail.php?productId=${encodeURIComponent(
        productId,
      )}`,
      true,
    );
    xhr.send();
  } catch (error) {
    console.error("Error loading product detail:", error);
  }
}

function cargarReviews() {
  var xmlhttp = new XMLHttpRequest();
  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get("id");
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      try {
        var response = JSON.parse(this.responseText);
        if (response) {
          // Normalize different response shapes to an array before rendering
          var reviewsArr = [];
          reviewsArr = response;
          console.log(response);
          renderReviews(reviewsArr);
        }
      } catch (e) {
        console.error("Error parsing JSON response:", e);
      }
    }
  };
  xmlhttp.open(
    "GET",
    "/student024/Shop/backend/endpoints/product_detail/show_reviews.php?productId=" +
      encodeURIComponent(productId),
    true,
  );
  xmlhttp.send();
}

function renderReviews(reviews) {
  const reviewsContainer = document.getElementById("reviews-section");
  if (!reviewsContainer) return;
  reviewsContainer.innerHTML = "";
  if (!Array.isArray(reviews)) return;
  reviews.forEach((review) => {
    let review_content = `
            <div class="review-item mb-4">
                <h4 class="border-b border-azul-claro">Posted by: ${
                  review.full_name
                }</h4>
                <span class="text-sm text-gray-500">Date: ${new Date(
                  review.created_at,
                ).toLocaleDateString()}</span>
                <span class="flex">
                `;
    for (let i = 0; i < review.review_rating; i++) {
      review_content += `<img class="w-5 h-5" src="/student024/Shop/assets/logos/estrella_con_relleno.png" alt="estrella rellena">`;
    }
    for (let i = review.review_rating; i < 5; i++) {
      review_content += `<img class="w-5 h-5" src="/student024/Shop/assets/logos/estrella_sin_relleno.png" alt="estrella vacía">`;
    }
    review_content += `</span>`;

    review_content += `<p>${review.review_content}</p>
            </div>`;
    reviewsContainer.innerHTML += review_content;
  });
}
