const pendingCountEl = document.getElementById('pending-count');
const etaMinutesEl = document.getElementById('eta-minutes');
const menuItemSelectEl = document.getElementById('menu_item_id');
const orderQuantityEl = document.getElementById('quantity');
const orderTotalAmountEl = document.getElementById('order-total-amount');

const formatPln = (amountCents) => `${(amountCents / 100).toFixed(2)} PLN`;

const updateOrderSummary = () => {
  if (!menuItemSelectEl || !orderTotalAmountEl) {
    return;
  }

  const selectedOption = menuItemSelectEl.options[menuItemSelectEl.selectedIndex];
  const priceCents = Number(selectedOption?.dataset.priceCents ?? 0);
  const quantity = Math.max(1, Number(orderQuantityEl?.value ?? 1) || 1);
  const totalCents = priceCents * quantity;

  orderTotalAmountEl.textContent = formatPln(totalCents);
};

const refreshQueueStatus = async () => {
  if (!pendingCountEl || !etaMinutesEl) {
    return;
  }

  try {
    const response = await fetch('/api/queue/stats', { headers: { Accept: 'application/json' } });
    if (!response.ok) {
      return;
    }

    const payload = await response.json();
    pendingCountEl.textContent = String(payload.pendingCount ?? 0);
    etaMinutesEl.textContent = `${payload.etaMinutes ?? 0} min`;
  } catch (_error) {
    // Silent fail to avoid noisy UI when backend is temporarily unavailable.
  }
};

const setupOrderButtons = () => {
  if (!menuItemSelectEl) {
    return;
  }

  document.querySelectorAll('[data-open-order-offcanvas="true"]').forEach((buttonEl) => {
    buttonEl.addEventListener('click', () => {
      const menuItemId = buttonEl.getAttribute('data-menu-item-id');
      if (menuItemId) {
        menuItemSelectEl.value = menuItemId;
      }
      updateOrderSummary();

      setTimeout(() => {
        if (orderQuantityEl) {
          orderQuantityEl.focus();
        } else {
          menuItemSelectEl.focus();
        }
      }, 150);
    });
  });
};

if (menuItemSelectEl) {
  menuItemSelectEl.addEventListener('change', updateOrderSummary);
}

if (orderQuantityEl) {
  orderQuantityEl.addEventListener('input', updateOrderSummary);
  orderQuantityEl.addEventListener('change', updateOrderSummary);
}

updateOrderSummary();
setupOrderButtons();
setInterval(refreshQueueStatus, 5000);
