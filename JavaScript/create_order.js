const products = document.querySelectorAll("carrito item");
let cart_data = [];
let carttotal = 0;
let totalitems = 0;
products.forEach((product) => {
  let productId = product.getAttribute("data-product");
  let size = product.getAttribute("data-size");
  let quantity = parseInt(
    product.querySelector(".cantidad-container").textContent,
  );
  let priceText = product
    .querySelector(".precio-producto")
    .textContent.replace("€", "")
    .trim();
  let price = parseFloat(priceText);

  const cartitem = {
    product_id: productId,
    size: size,
    quantity: quantity,
  };
  carttotal += price * quantity;
  totalitems += quantity;
  cart_data.push(cartitem);
});
document.getElementById("total_items").value = totalitems;
document.getElementById("cart_total").value = carttotal.toFixed(2);
document.getElementById("cart_data").value = JSON.stringify(cart_data);
