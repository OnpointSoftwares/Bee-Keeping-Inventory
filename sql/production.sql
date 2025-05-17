CREATE TABLE IF NOT EXISTS production (
    productionID INT AUTO_INCREMENT PRIMARY KEY,
    hiveID INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    quality ENUM('Premium', 'Standard', 'Low') NOT NULL,
    harvestDate DATE NOT NULL,
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hiveID) REFERENCES beehive(hiveID) ON DELETE CASCADE
);
