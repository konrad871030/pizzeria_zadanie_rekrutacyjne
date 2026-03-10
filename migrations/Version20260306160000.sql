CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    price_cents INT NOT NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    email VARCHAR(180) NOT NULL,
    delivery_address VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at DATETIME NOT NULL,
    delivered_at DATETIME DEFAULT NULL,
    INDEX idx_orders_status_created_at (status, created_at),
    CONSTRAINT fk_orders_menu_item FOREIGN KEY (menu_item_id) REFERENCES menu_items (id)
);
