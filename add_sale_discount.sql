-- Add optional sale_discount column to the products table (percentage-based)
ALTER TABLE products ADD COLUMN sale_discount DECIMAL(5, 2) NULL DEFAULT 0;
