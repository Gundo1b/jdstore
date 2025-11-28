-- SQL to add stock_quantity column to products table
-- Run this after the initial database setup

USE jdstore;

-- Add stock_quantity column with default value 0
ALTER TABLE products ADD COLUMN stock_quantity INT NOT NULL DEFAULT 0 AFTER profit;
