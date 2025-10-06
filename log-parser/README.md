## Overview

`log_parser.py`  is a Python script designed to parse log files, extract unique log messages, group them by severity level, and filter them based on customizable criteria including whitelists, blacklists, severity levels, and message length.

## Features

-   ✅ Parse log files and extract messages by severity (CRITICAL, ERROR, WARNING, INFO, DEBUG)
-   ✅ Filter logs using whitelist files (include only matching entries)
-   ✅ Filter logs using blacklist files (exclude matching entries)
-   ✅ Customize which severity levels to process
-   ✅ Control the maximum length of log messages to match
-   ✅ Display unique log messages grouped by severity

## Requirements

-   Python 3.6 or higher
-   No external dependencies (uses standard library only)

## Installation

Simply download the script and make it executable:
```bash
chmod +x log_parser.py
```

## Command Syntax

```bash
python log_parser.py <log_file> [OPTIONS]
```

### Positional Arguments:


| Argument | Required | Description                   |
|----------|----------|-------------------------------|
| log_file | ✅ Yes    | Path to the log file to parse |

### Optional Arguments:


| **Flag** | **Long Form** | **Type** | **Default**                       | **Description**                                                       |
|----------|---------------|----------|-----------------------------------|-----------------------------------------------------------------------|
| **-w**   | --whitelist   | string   | None                              | Path to whitelist file (only include messages containing these terms) |
| **-b**   | --blacklist   | string   | None                              | Path to blacklist file (exclude messages containing these terms)      |
| **-s**   | --severities  | list     | CRITICAL ERROR WARNING INFO DEBUG | List of severity levels to include                                    |
| **-l**   | --length      | integer  | 50                                | Maximum number of characters to match in log messages                 |
| **-h**   | --help        | -        | -                                 | Show help message and exit                                            |


## Usage Examples

### **1. Basic Usage (Process all severities)**

```bash
python log_parser.py /path/to/logfile.log
```

### **2. With Whitelist Only**

```bash
python log_parser.py /path/to/logfile.log -w whitelist.txt
```

### **3. With Blacklist Only**

```bash
python log_parser.py /path/to/logfile.log -b blacklist.txt
```

### **4. With Both Whitelist and Blacklist**

```bash
python log_parser.py /path/to/logfile.log -w whitelist.txt -b blacklist.txt
```

### **5. Filter by Specific Severities**

```bash
python log_parser.py /path/to/logfile.log -s ERROR CRITICAL
```

### **6. Custom Message Length**

```bash
python log_parser.py /path/to/logfile.log -l 100
```

### **7. Combined Options**

```bash
python log_parser.py /path/to/logfile.log -w whitelist.txt -b blacklist.txt -s ERROR WARNING -l 80
```

## Filter Files Format

### **Whitelist File (`whitelist.txt`)**

Contains terms that  **must be present**  in a log message for it to be included:

```
catalog_product
value_id
database
```

### **Blacklist File (`blacklist.txt`)**

Contains terms that  **will exclude**  a log message if present:

```
timeout
connection refused
deprecated
```

**Note:**  Each term should be on a separate line.


## Log File Format

The script expects log entries in the following format:

```
YYYY-MM-DDTHH:MM:SS.ssssss+00:00 main.SEVERITY: Log message here [] []
```

**Example:**

```
2025-10-01T06:45:55.541194+00:00 main.CRITICAL: DELETE FROM catalog_product_entity_varchar WHERE (value_id IN ('352893614')) [] []
2025-10-01T06:45:55.602253+00:00 main.INFO: DELETE FROM catalog_product_entity_varchar WHERE (value_id IN ('352893300')) [] []
2025-10-01T06:45:55.602253+00:00 main.ERROR: Connection timeout occurred [] []
```


## Output Format

The script outputs unique log messages grouped by severity:

```
--- CRITICAL ---
DELETE FROM catalog_product_entity_varchar WHERE (value_id IN ('352893614'))

--- ERROR ---
Connection timeout occurred

--- INFO ---
DELETE FROM catalog_product_entity_varchar WHERE (value_id IN ('352893300'))


## Common Use Cases

### 1. Find All Critical and Error Messages
```bash
python log_parser.py app.log -s CRITICAL ERROR
```

### 2. Find Database-Related Errors (Using Whitelist)

Create  `db_whitelist.txt`:

```
SELECT
INSERT
UPDATE
DELETE
database
```

Run:
```bash
python log_parser.py app.log -w db_whitelist.txt -s ERROR
```

### 3. Exclude Known Issues (Using Blacklist)

Create  `known_issues.txt`:

```
deprecated warning
ignore this
```

Run:
```bash
python log_parser.py app.log -b known_issues.txt
```

### 4. Quick Analysis with Short Messages
```bash
python log_parser.py app.log -l 30 -s ERROR WARNING
```

## Exit Codes


| **Code** | **Meaning**                                     |
|----------|-------------------------------------------------|
| **0**    | Success                                         |
| **1**    | Error (file not found, invalid arguments, etc.) |


----------

## Tips & Best Practices

1.  **Start Simple**: First run without filters to see all messages, then add filters progressively
2.  **Use Whitelist for Focused Analysis**: When investigating specific issues (e.g., database problems)
3.  **Use Blacklist for Noise Reduction**: When you know certain messages can be ignored
4.  **Adjust Message Length**: Use  `-l`  to see more or less context from each log entry
5.  **Combine Filters**: Use whitelist, blacklist, and severity filtering together for precise results

----------

## Troubleshooting

### **"File not found" Error**

-   Verify the log file path is correct
-   Check file permissions

### **No Output**

-   Verify the log format matches the expected pattern
-   Try without filters first to ensure logs are being parsed
-   Check if the severity levels in your logs match those specified with  `-s`

### **Too Many/Too Few Results**

-   Adjust the  `-l`  parameter to capture more/less of each message
-   Review whitelist/blacklist terms for typos
-   Check if severity filters are too restrictive
```