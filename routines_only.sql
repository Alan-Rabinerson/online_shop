-- Routines only for online_shop
DELIMITER $$

DROP PROCEDURE IF EXISTS `024_automaticOrders` $$
CREATE PROCEDURE `024_automaticOrders` ()
BEGIN
	DECLARE var_number_of_added_items INT;
	DECLARE var_number_of_orders INT;

	SET var_number_of_added_items = FLOOR(16*RAND()+5);
	SET var_number_of_orders = FLOOR(5+RAND()*2);

	CALL `024_shopping_cart_data_dump`(var_number_of_added_items);
	CALL `024_order_data_dump`(var_number_of_orders);
END$$

DROP PROCEDURE IF EXISTS `024_order_data_dump` $$
CREATE PROCEDURE `024_order_data_dump` (IN `var_number_of_orders` INT)
BEGIN
	DECLARE var_customer_id INT;
	DECLARE var_product_id INT;
	DECLARE i INT;

	SET i = 1;

	order_loop: WHILE i <= var_number_of_orders DO
		-- Tomar un cliente aleatorio del carrito; si no hay items salir
		SELECT `customer_id` INTO var_customer_id FROM `024_shopping_cart` ORDER BY RAND() LIMIT 1;
		IF var_customer_id IS NULL THEN
			LEAVE order_loop;
		END IF;

		SELECT `product_id` INTO var_product_id FROM `024_shopping_cart` WHERE `customer_id` = var_customer_id ORDER BY RAND() LIMIT 1;
		IF var_product_id IS NULL THEN
			LEAVE order_loop;
		END IF;

		-- Insertar pedido CON la talla del carrito y un status aleatorio
		INSERT INTO `024_orders_table` (`customer_id`, `product_id`, `size`, `quantity`, `price`, `order_date`, `address_id`, `method_id`, `status`)
		SELECT
			sc.`customer_id`,
			sc.`product_id`,
			sc.`size`,
			sc.`quantity`,
			(sc.`quantity` * p.`price`) AS `price`,
			NOW(),
			`024_get_address`(var_customer_id),
			`024_get_payment_method`(var_customer_id),
			ELT(FLOOR(1 + RAND() * 4), 'DELIVERED', 'EN-ROUTE', 'PROCESSING', '') AS status
		FROM `024_shopping_cart` sc
		JOIN `024_products` p ON p.`product_id` = sc.`product_id`
		WHERE sc.`customer_id` = var_customer_id AND sc.`product_id` = var_product_id
		LIMIT 1;

		DELETE FROM `024_shopping_cart`
		WHERE `customer_id` = var_customer_id AND `product_id` = var_product_id
		LIMIT 1;

		SET i = i + 1;
	END WHILE order_loop;

	SELECT * FROM `024_order_view`;
END$$

DROP PROCEDURE IF EXISTS `024_shopping_cart_data_dump`$$
CREATE PROCEDURE `024_shopping_cart_data_dump` (IN `var_number_of_items` INT)
BEGIN
	DECLARE var_customer_id INT;
	DECLARE var_product_id INT;
	DECLARE var_quantity INT;
	DECLARE var_size VARCHAR(10);
	DECLARE i INT;

	SET i = 1;

	shopping_loop: WHILE i <= var_number_of_items DO
		-- Elegir cliente y producto válidos existentes
		SELECT `customer_id` INTO var_customer_id FROM `024_customers` ORDER BY RAND() LIMIT 1;
		SELECT `product_id` INTO var_product_id FROM `024_products` ORDER BY RAND() LIMIT 1;

		IF var_customer_id IS NULL OR var_product_id IS NULL THEN
			LEAVE shopping_loop;
		END IF;

		SET var_quantity = FLOOR(1+RAND()*10);
		-- Asignar talla aleatoria
		SET var_size = ELT(FLOOR(1 + RAND() * 6), 'XS', 'S', 'M', 'L', 'XL', 'XXL');

		INSERT INTO `024_shopping_cart` (`customer_id`, `product_id`, `quantity`, `size`)
		VALUES (var_customer_id, var_product_id, var_quantity, var_size)
		ON DUPLICATE KEY UPDATE `quantity` = `quantity` + VALUES(`quantity`);

		SET i = i + 1;
	END WHILE shopping_loop;
END$$

DROP FUNCTION IF EXISTS `024_age`$$
CREATE FUNCTION `024_age` (`birthdate` DATE) RETURNS INT(11) DETERMINISTIC RETURN FLOOR((DATEDIFF(CURDATE(),birthdate)/365.25))$$

DROP FUNCTION IF EXISTS `024_full_name`$$
CREATE FUNCTION `024_full_name` (`first_name` VARCHAR(100), `last_name` VARCHAR(100)) RETURNS VARCHAR(210) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC RETURN CONCAT(first_name,' ',last_name)$$

DROP FUNCTION IF EXISTS `024_get_address`$$
CREATE FUNCTION `024_get_address` (`var_customer_id` INT) RETURNS INT(11)
BEGIN
	DECLARE v_addr INT;
	SELECT `address_id` INTO v_addr FROM `024_address_customer` WHERE `customer_id` = var_customer_id LIMIT 1;
	IF v_addr IS NULL THEN
		RETURN 1; -- default address_id
	END IF;
	RETURN v_addr;
END$$

DROP FUNCTION IF EXISTS `024_get_payment_method`$$
CREATE FUNCTION `024_get_payment_method` (`var_customer_id` INT) RETURNS INT(11)
BEGIN
	DECLARE v_method INT;
	SELECT `method_id` INTO v_method FROM `024_payment_customer` WHERE `customer_id` = var_customer_id LIMIT 1;
	IF v_method IS NULL THEN
		RETURN 1; -- default method_id
	END IF;
	RETURN v_method;
END$$

DROP FUNCTION IF EXISTS `024_get_random_valid_size`$$
CREATE FUNCTION `024_get_random_valid_size` (`var_product_id` INT) RETURNS VARCHAR(10) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC
BEGIN
	DECLARE valid_size VARCHAR(10);

	SELECT `size` INTO valid_size
	FROM `024_product_sizes`
	WHERE `product_id` = var_product_id
	ORDER BY RAND()
	LIMIT 1;

	RETURN valid_size;
END$$

DROP FUNCTION IF EXISTS `024_membership_level`$$
CREATE FUNCTION `024_membership_level` (`var_money_spent` INT) RETURNS VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC
BEGIN
	DECLARE var_level_name VARCHAR(50);

	IF var_money_spent > 1500 THEN 
		SET var_level_name = 'Diamond';
	ELSEIF var_money_spent > 1000 AND var_money_spent<1500 THEN
		SET var_level_name = 'Platinum';
	ELSEIF var_money_spent > 500 AND var_money_spent <1000 THEN
		SET var_level_name = 'Gold';
	ELSE
		SET var_level_name = 'Silver';
	END IF;

	RETURN var_level_name;
END$$
--
-- Eventos
--
DROP EVENT IF EXISTS `024_insert_test_data_event`$$
CREATE EVENT `024_insert_test_data_event` ON SCHEDULE EVERY 1 DAY STARTS '2025-11-17 17:22:45' ENDS '2026-11-18 17:22:45' ON COMPLETION PRESERVE DISABLE DO BEGIN
CALL 024_automaticOrders();
SELECT * FROM 024_order_view;
END$$

DELIMITER ;
