import re
import sys
import argparse
from collections import defaultdict

def load_filter_file(file_path):
    """Load lines from a filter file and return as a set of strings."""
    if file_path:
        try:
            with open(file_path, 'r') as file:
                return set(line.strip() for line in file if line.strip())
        except FileNotFoundError:
            print(f"Error: The file {file_path} was not found.")
            sys.exit(1)
    return set()

def build_severity_pattern(severities, max_length):
    """
    Builds a regex pattern to match log levels like CRITICAL, ERROR, etc.
    max_length specifies the maximum characters in the log message to match.
    Example return: r'\b(main\\.(CRITICAL|ERROR|INFO))\b:.*?(([\\S].{0,max_length}).*?)(?=\\[\\]|\n|$)'
    """
    joined = '|'.join(map(re.escape, severities))
    return re.compile(
        rf'\b(\S+\.({joined}))\b:.*?(([\S].{0,{max_length}}).*?)(?=\[\]|\n|$)',
        re.IGNORECASE
    )

def group_unique_logs_by_severity(file_path, whitelist, blacklist, severities, max_length):
    grouped_logs = defaultdict(set)

    pattern = build_severity_pattern(severities, max_length)

    with open(file_path, 'r') as file:
        for line in file:
            match = pattern.search(line)
            if match:
                severity = match.group(2).upper()
                message = match.group(4).strip()

                if (not whitelist or any(w in message for w in whitelist)) and \
                   (not blacklist or not any(b in message for b in blacklist)):
                    grouped_logs[severity].add(message)

    for severity in sorted(grouped_logs.keys()):
        print(f"\n--- {severity} ---")
        for msg in sorted(grouped_logs[severity]):
            print(msg)

def parse_args():
    parser = argparse.ArgumentParser(
        description="Parse log file and filter by severity, whitelist, blacklist, and maximum message length."
    )
    parser.add_argument("log_file", help="Path to the log file")
    parser.add_argument("-w", "--whitelist", help="Path to whitelist file", default=None)
    parser.add_argument("-b", "--blacklist", help="Path to blacklist file", default=None)
    parser.add_argument(
        "-s", "--severities",
        nargs="+",
        default=["CRITICAL", "ERROR"],
        help="List of severities to include (e.g., -s CRITICAL ERROR WARNING INFO DEBUG)"
    )
    parser.add_argument(
        "-l", "--length",
        type=int,
        default=999,
        help="Maximum number of characters to match in the log message (default: 50)"
    )
    return parser.parse_args()

if __name__ == "__main__":
    args = parse_args()

    whitelist = load_filter_file(args.whitelist)
    blacklist = load_filter_file(args.blacklist)

    group_unique_logs_by_severity(args.log_file, whitelist, blacklist, args.severities, args.length)
