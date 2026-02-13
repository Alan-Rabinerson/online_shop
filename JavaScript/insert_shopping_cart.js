document.addEventListener("DOMContentLoaded", function () {
  let addtoCartForms =
    document.querySelectorAll(".add-to-cart-form") ??
    document.querySelectorAll(".btn-add-cart");
  let elementoMensaje = document.createElement("div");
  function getCookie(name) {
    const n = name + "=";
    const parts = decodeURIComponent(document.cookie).split(";");
    for (let p of parts) {
      p = p.trim();
      if (p.indexOf(n) === 0) return p.substring(n.length);
    }
    return "";
  }
  addtoCartForms.forEach((form) => {
    form.addEventListener("submit", (event) => {
      event.preventDefault(); // prevent form submission
      let productId = form.querySelector("input[name='product_id']").value;
      var params =
        "product_id=" +
        encodeURIComponent(productId) +
        "&quantity=" +
        encodeURIComponent(1);
      //crear objeto xhttprequest
      var xhttp = new XMLHttpRequest();
      xhttp.open(
        "POST",
        "/student024/Shop/backend/db/shopping_cart/db_shopping_cart_insert.php",
        true,
      );
      var size = form.querySelector("select[name='size']");
      params += "&size=" + encodeURIComponent(size.value);

      // Añadir customer_id si está disponible (soporte invitados)
      let cid = null;
      const userCookie = getCookie("user");
      if (userCookie) {
        try {
          const u = JSON.parse(userCookie);
          cid = u.customer_id || null;
        } catch (e) {}
      }
      if (!cid) cid = getCookie("customer_id") || getCookie("guest_id") || null;
      if (cid) params += "&customer_id=" + encodeURIComponent(cid);
      xhttp.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded",
      );

      xhttp.onreadystatechange = function () {
        if (this.readyState !== 4)
          return; // only act when request is complete
        else if (this.readyState === 4 && this.status === 400) {
          let text = this.responseText || "";
          let parsed = null;
          try {
            parsed = JSON.parse(text || "{}");
          } catch (e) {
            parsed = { message: "Respuesta no válida del servidor" };
          }

          elementoMensaje.innerText =
            parsed.message || parsed.error || "Respuesta no válida";
          elementoMensaje.style.position = "fixed";
          elementoMensaje.style.bottom = "20px";
          elementoMensaje.style.right = "20px";
          elementoMensaje.style.color = "white";
          elementoMensaje.style.padding = "10px";
          elementoMensaje.style.borderRadius = "5px";
          elementoMensaje.style.boxShadow = "0 2px 6px rgba(0, 0, 0, 0.3)";

          if (this.status == 200) {
            elementoMensaje.style.backgroundColor = "#4BB543";
          } else {
            elementoMensaje.style.backgroundColor = "#FF4C4C";
          }

          document.body.appendChild(elementoMensaje);
          setTimeout(() => {
            if (document.body.contains(elementoMensaje))
              document.body.removeChild(elementoMensaje);
          }, 5000);
        }
      };
      xhttp.send(params);
    });
  });
});
