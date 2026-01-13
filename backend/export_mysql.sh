#!/bin/bash
# Export SQLite to MySQL compatible SQL

echo "-- ================================================"
echo "-- Ripe Home Database Export"
echo "-- Date: $(date '+%Y-%m-%d %H:%M:%S')"
echo "-- Tables: 1024 products, 16 categories"
echo "-- ================================================"
echo ""
echo "SET NAMES utf8mb4;"
echo "SET FOREIGN_KEY_CHECKS = 0;"
echo ""

# Export with SQLite, convert to MySQL format
sqlite3 database/database.sqlite ".dump" | \
  grep -v "^PRAGMA" | \
  grep -v "^BEGIN TRANSACTION;" | \
  grep -v "^COMMIT;" | \
  grep -v "sqlite_sequence" | \
  sed 's/AUTOINCREMENT/AUTO_INCREMENT/g' | \
  sed 's/INTEGER PRIMARY KEY/INT PRIMARY KEY/g' | \
  sed 's/datetime([^)]*)/CURRENT_TIMESTAMP/g'

echo ""
echo "SET FOREIGN_KEY_CHECKS = 1;"
