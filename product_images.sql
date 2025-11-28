-- SQL to add support for multiple product images

-- Create a new table for product images
CREATE TABLE product_images (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    product_id INT(11) NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Optional: If you want to keep the original image column in products table for a main image
-- ALTER TABLE products ADD COLUMN main_image VARCHAR(255) NULL;
-- Then move existing images to product_images and set main_image
