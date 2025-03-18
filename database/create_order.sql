CREATE TABLE `Order` (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  order_date DATE NOT NULL,
  status VARCHAR(15) NOT NULL,
  price float NOT NULL,
  customer_id INT NOT NULL,
  FOREIGN KEY (customer_id) REFERENCES User(user_id)
)