CREATE TABLE Shipment(
    shipment_id INT AUTO_INCREMENT PRIMARY KEY,
    delivery_method VARCHAR(15) NOT NULL,
    delivery_status VARCHAR(15) NOT NULL,
    order_id INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES `Order`(order_id)
)