-- Add optional brand, author, and publisher columns to the products table
ALTER TABLE products ADD COLUMN brand VARCHAR(100) NULL;
ALTER TABLE products ADD COLUMN author VARCHAR(100) NULL;
ALTER TABLE products ADD COLUMN publisher VARCHAR(100) NULL;
