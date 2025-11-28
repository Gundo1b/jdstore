-- SQL to add cost and profit columns to products table
-- Run this after the initial database setup

USE jdstore;

-- Add cost column
ALTER TABLE products ADD COLUMN cost DECIMAL(10,2) NOT NULL AFTER description;

-- Add profit column as a generated column
ALTER TABLE products ADD COLUMN profit DECIMAL(10,2) GENERATED ALWAYS AS (price - cost) STORED AFTER price;

-- Optional: Update existing products with a default cost (set to 0 for now, should be updated manually)
UPDATE products SET cost = 0 WHERE cost IS NULL;
