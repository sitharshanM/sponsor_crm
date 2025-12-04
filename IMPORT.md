# Excel Import Guide

This system supports importing large Excel files (up to 2GB+) with sponsor data.

## Installation

First, install the required PHP library:

```bash
composer install
```

This installs PhpSpreadsheet, which is needed for reading Excel files.

## Import Methods

### Method 1: CLI Import (Recommended for Large Files)

For files larger than 100MB, use the command-line import script:

```bash
php import_excel.php /path/to/your/file.xlsx --skip-header
```

**Options:**
- `--skip-header` - Skip the first row (header row)
- `--batch-size=N` - Process N rows at a time (default: 1000)
- `--start-row=N` - Start importing from row N (default: 1)
- `--max-rows=N` - Import maximum N rows

**Examples:**

```bash
# Basic import (skip header, process 1000 rows at a time)
php import_excel.php data.xlsx --skip-header

# Import with custom batch size (500 rows at a time)
php import_excel.php data.xlsx --skip-header --batch-size=500

# Import only first 10,000 rows
php import_excel.php data.xlsx --skip-header --max-rows=10000

# Resume import from row 5000
php import_excel.php data.xlsx --start-row=5000 --skip-header
```

### Method 2: Web Interface

For smaller files (< 100MB), use the web interface:

1. Navigate to: `http://localhost:8000/import_sponsors.php`
2. Select your Excel file (.xlsx or .xls)
3. Click "Import Sponsors"

**Note:** Web interface is limited to 10,000 rows for performance.

## Excel File Format

Your Excel file should have the following columns (headers in first row):

| Column Name | Required | Description |
|------------|----------|-------------|
| **Company Name** | Yes | Company or organization name |
| **Contact Person** | No | Name of contact person |
| **Email** | No | Email address |
| **Phone** | No | Phone number |
| **Industry** | No | Industry sector |
| **Type** or **Sponsor Type** | No | Type of sponsor |
| **Status** | No | new, interested, in_progress, closed, rejected |

### Column Detection

The system automatically detects columns by matching header names:
- **Company Name**: Looks for "company", "name", "organization"
- **Contact Person**: Looks for "contact", "person"
- **Email**: Looks for "email", "e-mail"
- **Phone**: Looks for "phone", "tel"
- **Industry**: Looks for "industry"
- **Type**: Looks for "type", "sponsor"
- **Status**: Looks for "status"

### Example Excel Format

```
| Company Name        | Contact Person | Email              | Phone      | Industry | Type  | Status |
|---------------------|----------------|--------------------|------------|----------|-------|--------|
| TechCorp Inc.       | John Smith     | john@techcorp.com  | 555-0101   | Tech     | Gold  | new    |
| Global Solutions    | Sarah Johnson  | sarah@global.com  | 555-0102   | Finance  | Silver| interested |
```

## Performance Tips

1. **For 2GB files**: Always use CLI import (`import_excel.php`)
2. **Batch size**: Start with 1000, increase if you have more RAM
3. **Memory limit**: You may need to increase PHP memory:
   ```bash
   php -d memory_limit=2G import_excel.php file.xlsx
   ```
4. **Processing time**: Large files may take hours - the script shows progress

## Troubleshooting

### "PhpSpreadsheet not installed"
```bash
composer install
```

### "Memory limit exceeded"
Increase PHP memory limit:
```bash
php -d memory_limit=4G import_excel.php file.xlsx
```

### "Could not detect 'company_name' column"
- Ensure your Excel file has headers in the first row
- Check that a column contains "company" or "name" in the header
- Use `--skip-header` if your first row is headers

### Import is slow
- Reduce batch size: `--batch-size=500`
- Check database connection performance
- Consider importing during off-peak hours

## Import Statistics

The CLI script provides:
- Total rows processed
- Successfully imported count
- Error count
- Processing time
- Rows per second

Example output:
```
=== Import Complete ===
Total rows processed: 50000
Successfully imported: 49850
Errors: 150
Time elapsed: 245.3s
Average: 203 rows/second
```

