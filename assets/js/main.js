let homePopulerProduct = document.querySelectorAll(
  ".home_page_populer_product .product",
);

homePopulerProduct.forEach((e) => {
  const btn = document.createElement("a");
  btn.innerText = "Nauja";

  // multiple classes
  btn.classList.add("btn");

  // href set
  btn.href = "#";

  e.appendChild(btn);
});
let singleProductPopulerProduct = document.querySelectorAll(
  ".single_product_page_populer_products .product",
);

singleProductPopulerProduct.forEach((e) => {
  const btn = document.createElement("a");
  btn.innerText = "-15%";

  // multiple classes
  btn.classList.add("btn");

  // href set
  btn.href = "#";

  e.appendChild(btn);
});

document.addEventListener("DOMContentLoaded", () => {
  const sort = document.querySelector(".orderdropdown");
  const accdin = document.querySelector("#filterAccordion");

  if (sort && accdin) {
    accdin.appendChild(sort);
  }
});
document.addEventListener("DOMContentLoaded", () => {
  const product_summery = document.querySelector(".entry-summary");
  const summery_price = document.querySelector(".summary .price");
  const form_summery = document.querySelector(".summary form");
  const product_delivery = document.querySelector(".product-delivery-methods");

  if (summery_price && form_summery) {
    form_summery.appendChild(summery_price);
  }
  // if (product_delivery && product_summery) {
  //   product_summery.appendChild(product_delivery);
  // }
  (function () {
    var toggle = document.querySelector(".product-delivery-methods__toggle");
    var content = document.getElementById("product-delivery-methods-content");
    if (toggle && content) {
      toggle.addEventListener("click", function () {
        var expanded = toggle.getAttribute("aria-expanded") === "true";
        toggle.setAttribute("aria-expanded", expanded ? "false" : "true");
        content.classList.toggle(
          "product-delivery-methods__content--collapsed",
          expanded,
        );
      });
    }
  })();
});
document.addEventListener("DOMContentLoaded", () => {
  const main_product_section = document.querySelector(".main_product_section");
  const product_images = document.querySelector(".images");
  const product_summary = document.querySelector(".entry-summary");
  const woo_tabs = document.querySelector(".woocommerce-tabs");
  if (main_product_section) {
    main_product_section.appendChild(product_images);
    main_product_section.appendChild(product_summary);
  }
  if (woo_tabs) {
    main_product_section.appendChild(woo_tabs);
  }
});
// (function () {
//   let toggle = document.getElementById("product-delivery-methods-toggle");
//   let content = document.getElementById("product-delivery-methods-content");
//   if (toggle && content) {
//     toggle.addEventListener("click", function () {
//       let expanded = toggle.getAttribute("aria-expanded") === "true";
//       toggle.setAttribute("aria-expanded", expanded ? "false" : "true");
//       if (content) {
//         content.setAttribute("aria-hidden", expanded ? "true" : "false");
//         content.classList.toggle(
//           "product-delivery-methods__content--collapsed",
//           !expanded,
//         );
//       }
//     });
//   }
// })();
(function () {
  let toggle = document.getElementById("product-delivery-methods-toggle");
  let content = document.getElementById("product-delivery-methods-content");
  if (!toggle || !content) return;

  toggle.addEventListener("click", function () {
    let isExpanded = toggle.getAttribute("aria-expanded") === "true";

    toggle.setAttribute("aria-expanded", isExpanded ? "false" : "true");
    content.setAttribute("aria-hidden", isExpanded ? "true" : "false");
    content.classList.toggle(
      "product-delivery-methods__content--collapsed",
      isExpanded,
    );
  });
})();

(function () {
  let header = document.getElementById("single-product-reviews-toggle");
  let content = document.getElementById("single-product-reviews-content");
  if (!header || !content) return;

  header.addEventListener("click", function () {
    let expanded = header.getAttribute("aria-expanded") === "true";
    header.setAttribute("aria-expanded", expanded ? "false" : "true");
    content.setAttribute("aria-hidden", expanded ? "true" : "false");
    content.classList.toggle(
      "product-delivery-methods__content--collapsed",
      expanded,
    );
  });
})();
function closeMobMenu() {
  const mobileMenu = document.getElementById("mobileMenuDropdown");
  if (mobileMenu) {
    mobileMenu.style.display = "none";
  }
}
document.addEventListener("DOMContentLoaded", () => {
  const tab_single_product = document.querySelector(".description_tab a");
  if (!tab_single_product) return;
  tab_single_product.innerHTML = `<div class="tab-content-wrapper">
  <div class="tab-icon">
    <svg xmlns="https://www.w3.org/2000/svg" width="30" height="28" viewBox="0 0 30 28" fill="none">
<g clip-path="url(#clip0_488_6178)">
<path d="M28.0901 3.33607C28.4823 3.59626 28.8037 3.94737 29.0262 4.35858C29.2486 4.7698 29.3652 5.22854 29.3658 5.69456V23.7272C29.3658 24.4824 29.0615 25.2066 28.5198 25.7406C27.9781 26.2745 27.2435 26.5745 26.4774 26.5745H3.36988C2.60382 26.5745 1.86914 26.2745 1.32745 25.7406C0.785762 25.2066 0.481445 24.4824 0.481445 23.7272V5.69456C0.481445 4.93941 0.785762 4.2152 1.32745 3.68123C1.86914 3.14727 2.60382 2.84729 3.36988 2.84729H18.2453" stroke="#333333" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M19.7378 3.32183C20.0036 3.32183 20.2192 3.10937 20.2192 2.84729C20.2192 2.5852 20.0036 2.37274 19.7378 2.37274C19.4719 2.37274 19.2563 2.5852 19.2563 2.84729C19.2563 3.10937 19.4719 3.32183 19.7378 3.32183Z" fill="#333333"/>
<path d="M6.49902 16.3718H23.3482M6.49902 19.6936H18.5342M6.49902 13.05H23.3482M6.49902 9.72818H23.3482" stroke="#333333" stroke-linecap="round" stroke-linejoin="round"/>
</g>
<defs>
<clipPath id="clip0_488_6178">
<rect width="29.8472" height="27.049" fill="white"/>
</clipPath>
</defs>
</svg>
  </div>
  <div class="tab-text">Aprašymas</div>
  </div>`;
});

/* Cart page: quantity +/- and update cart */
(function () {
  const form = document.querySelector(
    ".woocommerce-cart-form.cart-form-layout",
  );
  if (!form) return;
  const updateBtn = form.querySelector('button[name="update_cart"]');
  const minusBtns = form.querySelectorAll(".cart-qty-minus");
  const plusBtns = form.querySelectorAll(".cart-qty-plus");

  function triggerUpdate() {
    if (updateBtn) {
      updateBtn.removeAttribute("disabled");
      updateBtn.click();
    }
  }

  minusBtns.forEach(function (btn) {
    btn.addEventListener("click", function () {
      const wrap = this.closest(".quantity-wrap");
      const input = wrap && wrap.querySelector("input.qty");
      if (!input) return;
      const min = parseInt(input.getAttribute("min"), 10) || 0;
      let val = parseInt(input.value, 10) || 0;
      if (val > min) {
        val--;
        input.value = val;
        triggerUpdate();
      }
    });
  });

  plusBtns.forEach(function (btn) {
    btn.addEventListener("click", function () {
      const wrap = this.closest(".quantity-wrap");
      const input = wrap && wrap.querySelector("input.qty");
      if (!input) return;
      const max = parseInt(input.getAttribute("max"), 10) || 9999;
      let val = parseInt(input.value, 10) || 0;
      if (val < max) {
        val++;
        input.value = val;
        triggerUpdate();
      }
    });
  });
})();

/* Cart page: coupon toggle */
(function () {
  const toggle = document.querySelector(".cart-coupon-toggle");
  const wrap = document.querySelector(".cart-coupon-form-wrap");
  if (!toggle || !wrap) return;
  toggle.addEventListener("click", function () {
    const expanded = toggle.getAttribute("aria-expanded") === "true";
    toggle.setAttribute("aria-expanded", !expanded);
    wrap.hidden = expanded;
  });
})();

/* Cart page: cross-sells swiper */
document.addEventListener("DOMContentLoaded", function () {
  const cartSwiperEl = document.querySelector(".cartCrossSellsSwiper");
  if (!cartSwiperEl || typeof Swiper === "undefined") return;
  new Swiper(".cartCrossSellsSwiper", {
    slidesPerView: 2,
    spaceBetween: 16,
    pagination: {
      el: ".cartCrossSellsSwiper .swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".cartCrossSellsSwiper .swiper-button-next",
      prevEl: ".cartCrossSellsSwiper .swiper-button-prev",
    },
    breakpoints: {
      0: { slidesPerView: 1 },
      576: { slidesPerView: 2 },
      768: { slidesPerView: 3 },
      992: { slidesPerView: 4, spaceBetween: 20 },
    },
  });
});
jQuery(document).ready(function ($) {
  // Jab bhi shipping method change ho
  $(document).on("change", "input.shipping_method", function () {
    // WooCommerce ka built-in event fire kar do
    $("body").trigger("update_checkout");
  });
});
function toggleFooterSection(header) {
  const section = header.parentElement;
  section.classList.toggle("collapsed");
}

// Initialize sections as collapsed on mobile only
function initializeMobileSections() {
  const sections = document.querySelectorAll(
    ".footer-section:not(.footer-logo-section)",
  );

  if (window.innerWidth <= 480) {
    sections.forEach((section) => {
      section.classList.add("collapsed");
    });
  } else {
    // Remove collapsed class on desktop
    sections.forEach((section) => {
      section.classList.remove("collapsed");
    });
  }
}

// Run on load and resize
window.addEventListener("load", initializeMobileSections);
window.addEventListener("resize", initializeMobileSections);
// document.addEventListener("DOMContentLoaded", () => {
//   const newsletter = document.querySelector("#omnisend_newsletter_checkbox_field");
//   const wc_tms_wrapper = document.querySelector(".woocommerce-terms-and-conditions-wrapper");
//   if (newsletter && wc_tms_wrapper) {
//     wc_tms_wrapper.appendChild(newsletter);
//   }
//  });

/**
 * Fix layout on search/filter pages - ensure full width
 */
document.addEventListener("DOMContentLoaded", function () {
  // Fix layout width issues on search/filter pages
  function fixLayoutWidth() {
    const body = document.body;
    const primary = document.getElementById("primary");
    const main = document.getElementById("main");
    const header = document.querySelector("header");

    if (body) {
      body.style.width = "100%";
      body.style.maxWidth = "100%";
    }
    if (primary) {
      primary.style.width = "100%";
      primary.style.maxWidth = "100%";
    }
    if (main) {
      main.style.width = "100%";
      main.style.maxWidth = "100%";
    }
    if (header) {
      header.style.width = "100%";
      header.style.maxWidth = "100%";
    }
  }

  // Run on load
  fixLayoutWidth();

  // Run after a short delay to catch any late-loading issues
  setTimeout(fixLayoutWidth, 100);
  setTimeout(fixLayoutWidth, 500);
});

/**
 * Archive filter: AJAX update on select change (no Apply button).
 */
document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("archive-products-container");
  const forms = document.querySelectorAll(".archive-filter-form");
  if (!container || !forms.length) return;

  function buildFilterUrl(form) {
    const baseUrl =
      form.getAttribute("data-base-url") || window.location.pathname;
    const params = new URLSearchParams();
    const orderby = form.querySelector('input[name="orderby"]');
    if (orderby && orderby.value) params.set("orderby", orderby.value);

    // Preserve search query
    const searchInput = form.querySelector('input[name="s"]');
    if (searchInput && searchInput.value) params.set("s", searchInput.value);

    // Preserve post_type
    const postType = form.querySelector('input[name="post_type"]');
    if (postType && postType.value) params.set("post_type", postType.value);

    // Add attribute filters
    form.querySelectorAll('select[name^="pa_"]').forEach(function (select) {
      if (select.value) params.set(select.name, select.value);
    });

    // Add price filters
    const minPrice = form.querySelector('input[name="min_price"]');
    const maxPrice = form.querySelector('input[name="max_price"]');
    if (minPrice && minPrice.value) params.set("min_price", minPrice.value);
    if (maxPrice && maxPrice.value) params.set("max_price", maxPrice.value);

    const query = params.toString();
    return query
      ? baseUrl + (baseUrl.indexOf("?") !== -1 ? "&" : "?") + query
      : baseUrl;
  }

  function removeFilterFromUrl(filterKey, filterType) {
    const url = new URL(window.location.href);
    if (filterType === "price") {
      url.searchParams.delete("min_price");
      url.searchParams.delete("max_price");
    } else {
      url.searchParams.delete(filterKey);
    }
    return url.pathname + (url.search ? url.search : "");
  }

  function setLoading(loading) {
    container.classList.toggle("archive-products-container--loading", loading);
  }

  function fetchAndReplace(url) {
    setLoading(true);
    fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
      .then(function (res) {
        return res.text();
      })
      .then(function (html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");
        const newContainer = doc.getElementById("archive-products-container");
        if (newContainer) {
          container.innerHTML = newContainer.innerHTML;
        }
        const newUrl = url.startsWith("http")
          ? url
          : window.location.origin + url;
        if (window.history && window.history.pushState) {
          window.history.pushState({}, "", newUrl);
        }
        // Ensure layout stays correct after AJAX update
        setTimeout(function () {
          document.body.style.width = "100%";
          document.body.style.maxWidth = "100%";
          var primary = document.getElementById("primary");
          var main = document.getElementById("main");
          if (primary) {
            primary.style.width = "100%";
            primary.style.maxWidth = "100%";
          }
          if (main) {
            main.style.width = "100%";
            main.style.maxWidth = "100%";
          }
        }, 50);
      })
      .catch(function () {
        window.location.href = url;
      })
      .finally(function () {
        setLoading(false);
      });
  }

  forms.forEach(function (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      fetchAndReplace(buildFilterUrl(form));
    });

    form
      .querySelectorAll(".archive-filter-form__select")
      .forEach(function (select) {
        select.addEventListener("change", function () {
          fetchAndReplace(buildFilterUrl(form));
        });
      });

    // Price filter inputs
    form
      .querySelectorAll(".archive-filter-price__input")
      .forEach(function (input) {
        let timeout;
        input.addEventListener("input", function () {
          clearTimeout(timeout);
          timeout = setTimeout(function () {
            fetchAndReplace(buildFilterUrl(form));
          }, 500);
        });
      });

    var clearLink = form.querySelector(".archive-filter-form__clear");
    if (clearLink) {
      clearLink.addEventListener("click", function (e) {
        e.preventDefault();
        var href = clearLink.getAttribute("href");
        fetchAndReplace(href);
        forms.forEach(function (f) {
          f.querySelectorAll('select[name^="pa_"]').forEach(function (s) {
            s.value = "";
          });
          f.querySelectorAll(".archive-filter-price__input").forEach(
            function (inp) {
              inp.value = "";
            },
          );
        });
      });
    }
  });

  // Filter chip removal
  document
    .querySelectorAll(".archive-filter-chip__remove")
    .forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const chip = btn.closest(".archive-filter-chip");
        if (!chip) return;
        const filterKey = chip.getAttribute("data-filter-key");
        const filterType = chip.getAttribute("data-filter-type");
        const form = document.querySelector(".archive-filter-form");
        if (!form) return;

        // Remove from form
        if (filterType === "price") {
          form.querySelector('input[name="min_price"]').value = "";
          form.querySelector('input[name="max_price"]').value = "";
        } else {
          const select = form.querySelector('select[name="' + filterKey + '"]');
          if (select) select.value = "";
        }

        // Update URL and fetch
        const newUrl = removeFilterFromUrl(filterKey, filterType);
        fetchAndReplace(newUrl);
      });
    });

  // Clear all button (in chips area)
  document
    .querySelectorAll(".archive-filter-chips__clear-all")
    .forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        const href = btn.getAttribute("href");
        fetchAndReplace(href);
        forms.forEach(function (f) {
          f.querySelectorAll('select[name^="pa_"]').forEach(function (s) {
            s.value = "";
          });
          f.querySelectorAll(".archive-filter-price__input").forEach(
            function (inp) {
              inp.value = "";
            },
          );
        });
      });
    });
});
function toggleDropdown() {
  const dropdown = document.getElementById("catalogDropdown");
  dropdown.classList.toggle("show");
}

function toggleMobileDropdown() {
  const mobileMenu = document.getElementById("mobileMenuDropdown");
  if (mobileMenu) {
    mobileMenu.style.display =
      mobileMenu.style.display === "none" ? "block" : "none";
  }
  // Close catalog dropdown when mobile menu opens
  const dropdown = document.getElementById("catalogDropdown");
  if (dropdown) {
    dropdown.classList.remove("show");
  }
}

// Close dropdown when clicking outside
document.addEventListener("click", function (event) {
  const dropdown = document.getElementById("catalogDropdown");
  const button = document.querySelector(".catalog-btn");
  const hamburger = document.querySelector(".hamburger");

  if (
    !button?.contains(event.target) &&
    !hamburger?.contains(event.target) &&
    !dropdown?.contains(event.target)
  ) {
    if (dropdown) {
      dropdown.classList.remove("show");
    }
  }
});

// Handle subcategories toggle on click (for mobile)
document.addEventListener("DOMContentLoaded", function () {
  const categoryItems = document.querySelectorAll(
    ".dropdown-categories li.has-children",
  );

  categoryItems.forEach(function (item) {
    const link = item.querySelector("a.category-with-children");
    if (link) {
      link.addEventListener("click", function (e) {
        // On mobile, toggle subcategories on click
        if (window.innerWidth <= 768) {
          e.preventDefault();
          item.classList.toggle("active");
        }
        // On desktop, allow normal link behavior (hover will show subcategories)
      });
    }
  });
});
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".at24-toggle").forEach(function (btn) {
    btn.addEventListener("click", function () {
      let submenu = this.parentElement.querySelector(".at24-sub-menu");

      if (!submenu) return;

      if (submenu.style.display === "block") {
        submenu.style.display = "none";
        this.innerHTML = `<svg xmlns="https://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14" fill="none">
<g clip-path="url(#clip0_488_2271)">
<path d="M1 13L7 7L1 1" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</g>
<defs>
<clipPath id="clip0_488_2271">
<rect width="8" height="14" fill="white"/>
</clipPath>
</defs>
</svg>`;
      } else {
        submenu.style.display = "block";
        this.innerHTML = `<svg xmlns="https://www.w3.org/2000/svg" width="9" height="5" viewBox="0 0 9 5" fill="none">
<path d="M4.70502 4.70495L8.70502 0.704945L8.00002 -5.48363e-05L4.35002 3.64495L0.705017 -5.48363e-05L1.71661e-05 0.704945L4.00002 4.70495C4.0937 4.79807 4.22042 4.85034 4.35252 4.85034C4.48461 4.85034 4.61134 4.79807 4.70502 4.70495Z" fill="black"/>
</svg>`;
      }
    });
  });
});
document.addEventListener("DOMContentLoaded", function () {
  const products_par_page = document.querySelectorAll(".wppp-select option");
  if (!products_par_page) return;
  products_par_page.forEach(function (opt) {
    let val = opt.value;
    opt.textContent = val + " produktų / puslapį";
  });
});
document.addEventListener("DOMContentLoaded", function () {
  let shippingData = {
    local_pickup: {
      image: "https://athome24.lt/wp-content/uploads/2026/02/Frame.png",
      label: "Atsiimti vietoje",
      price: "0.00",
    },

    parcelmachine_omniva: {
      image: "https://athome24.lt/wp-content/uploads/2026/02/image-459.png",
      label: "Paštomatai",
      price: "3.50",
    },
  };

  document.querySelectorAll("#shipping_method li").forEach(function (li) {
    let input = li.querySelector("input.shipping_method");
    let label = li.querySelector("label");
    let price = li.querySelector(".woocommerce-Price-amount");

    if (!input || !label) return;

    let methodValue = input.value;
    let methodKey = methodValue.split(":")[0];

    /* ============================
       1. Label Text Change
    ============================ */

    if (shippingData[methodKey] && shippingData[methodKey].label) {
      // Pehle label ka old text saaf karo
      label.childNodes.forEach((n) => {
        if (n.nodeType === 3) n.remove();
      });

      // Naya label text add
      label.prepend(document.createTextNode(shippingData[methodKey].label));
    }

    /* ============================
       2. Add Image
    ============================ */

    if (shippingData[methodKey]) {
      let img = document.createElement("img");
      img.src = shippingData[methodKey].image;

      img.style.display = "block";
      img.style.marginBottom = "5px";
      img.style.marginTop = "5px";
      img.style.maxWidth = "20px";

      label.appendChild(img);
    }

    /* ============================
       3. Add Price if not exists
    ============================ */

    if (!price) {
      let newPrice = document.createElement("span");
      newPrice.className = "woocommerce-Price-amount amount";

      let p = shippingData[methodKey] ? shippingData[methodKey].price : "0.00";

      newPrice.innerHTML =
        " <bdi>" +
        p +
        "<span class='woocommerce-Price-currencySymbol'>€</span></bdi>";

      label.appendChild(newPrice);
    }
  });
});
jQuery(function ($) {
  function moveOmnisendCheckbox() {
    var checkbox = $("#omnisend_newsletter_checkbox_field");
    var target = $("#my-custom-place-order");

    if (checkbox.length && target.length) {
      // Button se uper move
      checkbox.prependTo(target);
    }
  }

  // Initial load
  moveOmnisendCheckbox();

  // AJAX checkout update ke baad bhi
  $(document.body).on("updated_checkout", function () {
    moveOmnisendCheckbox();
  });
});
document.addEventListener("DOMContentLoaded", function () {
  const right_col = document.querySelector(".right-col");
  const prodcut_con = document.querySelector("div#custom-sku-product");
  if (!right_col && !prodcut_con) return;
  right_col.appendChild(prodcut_con);
});
document.addEventListener("DOMContentLoaded", function () {
  const coupon = document.querySelector(".woocommerce-form-coupon");
  const checkout_left_col = document.querySelector(".left-col");
  if (!coupon) return;
  checkout_left_col.appendChild(coupon);
});
document.addEventListener('DOMContentLoaded', () => {
  const slider = document.querySelector('.scrolling-bar .custom_max_width');
  let isDown = false;
  let startX;
  let scrollLeft;

  slider.addEventListener('mousedown', (e) => {
    isDown = true;
    slider.classList.add('active');
    startX = e.pageX - slider.offsetLeft;
    scrollLeft = slider.scrollLeft;
  });

  slider.addEventListener('mouseleave', () => {
    isDown = false;
    slider.classList.remove('active');
  });

  slider.addEventListener('mouseup', () => {
    isDown = false;
    slider.classList.remove('active');
  });

  slider.addEventListener('mousemove', (e) => {
    if(!isDown) return;
    e.preventDefault();
    const x = e.pageX - slider.offsetLeft;
    const walk = (x - startX) * 2;
    slider.scrollLeft = scrollLeft - walk;
  });
});
