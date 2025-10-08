-- Vehicles table structure and sample data
-- Run this SQL to create the vehicles table and insert sample data

-- Create vehicles table if it doesn't exist
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price VARCHAR(50) NOT NULL,
    category ENUM('all', 'electric', 'limited') DEFAULT 'all',
    is_hybrid BOOLEAN DEFAULT FALSE,
    is_electric BOOLEAN DEFAULT FALSE,
    is_limited BOOLEAN DEFAULT FALSE,
    image_url VARCHAR(255),
    description TEXT,
    specifications JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample vehicle data
INSERT INTO vehicles (name, price, image_url, description) VALUES
('COMPASS', 'Rp. 445.651.993,34', 'https://images.unsplash.com/photo-1549317336-206569e8475c?w=400', 'Compact SUV with modern design and efficient performance'),
('WRANGLER', 'Rp. 541.607.920,00', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400', 'Iconic off-road vehicle with plug-in hybrid technology'),
('CHEROKEE', 'Rp. 604.649.160,00', 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=400', 'Mid-size SUV with hybrid powertrain for efficiency'),
('GRAND CHEROKEE', 'Rp. 641.140.349,39', 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=400', 'Premium SUV with plug-in hybrid technology'),
('GLADIATOR', 'Rp. 993.233.188,89', 'https://images.unsplash.com/photo-1563720223185-11003d516935?w=400', 'Pickup truck with legendary Jeep capability'),
('GRAND WAGONEER', 'Rp. 1.407.538.650,00', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400', 'Luxury SUV with premium features and limited availability');

-- Update some vehicles to have different categories
UPDATE vehicles SET category = 'electric' WHERE name IN ('WRANGLER', 'CHEROKEE', 'GRAND CHEROKEE');
UPDATE vehicles SET category = 'limited' WHERE name = 'GRAND WAGONEER';

-- Add some additional specifications
UPDATE vehicles SET specifications = '{"engine": "2.0L Turbo", "horsepower": "270", "torque": "295 lb-ft", "transmission": "9-Speed Automatic", "drivetrain": "4WD"}' WHERE name = 'COMPASS';
UPDATE vehicles SET specifications = '{"engine": "2.0L Turbo PHEV", "horsepower": "375", "torque": "470 lb-ft", "transmission": "8-Speed Automatic", "drivetrain": "4WD"}' WHERE name = 'WRANGLER';
UPDATE vehicles SET specifications = '{"engine": "2.0L Turbo Hybrid", "horsepower": "270", "torque": "295 lb-ft", "transmission": "9-Speed Automatic", "drivetrain": "4WD"}' WHERE name = 'CHEROKEE';
UPDATE vehicles SET specifications = '{"engine": "2.0L Turbo PHEV", "horsepower": "375", "torque": "470 lb-ft", "transmission": "8-Speed Automatic", "drivetrain": "4WD"}' WHERE name = 'GRAND CHEROKEE';
UPDATE vehicles SET specifications = '{"engine": "3.6L V6", "horsepower": "285", "torque": "260 lb-ft", "transmission": "8-Speed Automatic", "drivetrain": "4WD"}' WHERE name = 'GLADIATOR';
UPDATE vehicles SET specifications = '{"engine": "3.0L V6", "horsepower": "420", "torque": "468 lb-ft", "transmission": "8-Speed Automatic", "drivetrain": "4WD"}' WHERE name = 'GRAND WAGONEER';
