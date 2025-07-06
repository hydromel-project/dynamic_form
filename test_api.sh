#!/bin/bash

# API Test Script

BASE_URL="http://127.0.0.1:8000/api"

# Test User Credentials
REGISTER_EMAIL="test_register_$(date +%s%N)@example.com"
LOGIN_EMAIL="test_login_$(date +%s%N)@example.com"
PASSWORD="password"

# Report File
REPORT_FILE="api_test_report_$(date +%Y%m%d_%H%M%S).md"
TEMP_OUTPUT_FILE="/tmp/api_test_output_$(date +%s%N).tmp"

# --- Helper Functions ---
log_section() {
    echo "\n## $1" | tee -a "$REPORT_FILE"
    echo "--------------------------------------------------" | tee -a "$REPORT_FILE"
}

log_test() {
    TEST_NAME="$1"
    STATUS="$2"
    OUTPUT_CONTENT="$3"
    
    echo "### Test: $TEST_NAME" | tee -a "$REPORT_FILE"
    echo "Status: $STATUS" | tee -a "$REPORT_FILE"
    echo "```json" | tee -a "$REPORT_FILE"
    echo "$OUTPUT_CONTENT" >> "$REPORT_FILE" # Directly append to file
    echo "```" | tee -a "$REPORT_FILE"
    echo "" | tee -a "$REPORT_FILE"
}

# Function to extract access token, trying jq first, then grep/cut
get_access_token() {
    local json_output="$1"
    local token=""
    if command -v jq &> /dev/null; then
        token=$(echo "$json_output" | jq -r '.access_token')
    else
        echo "WARNING: jq not found. Falling back to less reliable token extraction." | tee -a "$REPORT_FILE"
        token=$(echo "$json_output" | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)
    fi
    echo "$token"
}

# --- Main Test Execution ---

# Clear previous report content
> "$REPORT_FILE"

echo "# API Test Report" | tee -a "$REPORT_FILE"
echo "Run Date: $(date)" | tee -a "$REPORT_FILE"

# Check for jq dependency
if ! command -v jq &> /dev/null; then
    echo "WARNING: 'jq' is not installed. JSON parsing will be less reliable." | tee -a "$REPORT_FILE"
fi

log_section "1. User Registration"
curl -s -X POST "${BASE_URL}/register" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"name\": \"Test User\", \"email\": \"${REGISTER_EMAIL}\", \"password\": \"${PASSWORD}\", \"password_confirmation\": \"${PASSWORD}\"}" > "$TEMP_OUTPUT_FILE"
REGISTER_OUTPUT=$(cat "$TEMP_OUTPUT_FILE")

REGISTER_ACCESS_TOKEN=$(get_access_token "$REGISTER_OUTPUT")

if [ -n "$REGISTER_ACCESS_TOKEN" ] && echo "$REGISTER_OUTPUT" | grep -q "access_token"; then
    log_test "Register New User" "SUCCESS" "$REGISTER_OUTPUT"
else
    log_test "Register New User" "FAIL" "$REGISTER_OUTPUT"
fi

log_section "2. User Login"
curl -s -X POST "${BASE_URL}/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"email\": \"${LOGIN_EMAIL}\", \"password\": \"${PASSWORD}\"}" > "$TEMP_OUTPUT_FILE"
LOGIN_OUTPUT=$(cat "$TEMP_OUTPUT_FILE")

ACCESS_TOKEN=$(get_access_token "$LOGIN_OUTPUT")

if [ -n "$ACCESS_TOKEN" ]; then
    log_test "Login User" "SUCCESS" "$LOGIN_OUTPUT"
else
    log_test "Login User" "FAIL" "$LOGIN_OUTPUT"
fi

log_section "3. Authenticated Forms List (Requires Token)"
if [ -n "$ACCESS_TOKEN" ]; then
    curl -s -X GET "${BASE_URL}/forms" \
      -H "Accept: application/json" \
      -H "Authorization: Bearer ${ACCESS_TOKEN}" > "$TEMP_OUTPUT_FILE"
    FORMS_OUTPUT=$(cat "$TEMP_OUTPUT_FILE")
    
    # Check if it's a valid JSON array (jq -e . will return 0 if valid JSON, non-zero otherwise)
    if command -v jq &> /dev/null; then
        if echo "$FORMS_OUTPUT" | jq -e '.' >/dev/null && [ "$(echo "$FORMS_OUTPUT" | jq '.[]' | wc -l)" -ge 0 ]; then
            log_test "Get Forms List" "SUCCESS" "$FORMS_OUTPUT"
        else
            log_test "Get Forms List" "FAIL" "$FORMS_OUTPUT"
        fi
    else
        # Fallback for forms list check if jq is not available
        if echo "$FORMS_OUTPUT" | grep -q "^\[.*\]$"; then
            log_test "Get Forms List" "SUCCESS" "$FORMS_OUTPUT"
        else
            log_test "Get Forms List" "FAIL" "$FORMS_OUTPUT"
        fi
    fi
else
    log_test "Get Forms List" "SKIPPED" "No access token available from login."
fi

log_section "4. User Logout (Requires Token)"
if [ -n "$ACCESS_TOKEN" ]; then
    curl -s -X POST "${BASE_URL}/logout" \
      -H "Accept: application/json" \
      -H "Authorization: Bearer ${ACCESS_TOKEN}" > "$TEMP_OUTPUT_FILE"
    LOGOUT_OUTPUT=$(cat "$TEMP_OUTPUT_FILE")
    
    if echo "$LOGOUT_OUTPUT" | grep -q "Logged out successfully."; then
        log_test "Logout User" "SUCCESS" "$LOGOUT_OUTPUT"
    else
        log_test "Logout User" "FAIL" "$LOGOUT_OUTPUT"
    fi
else
    log_test "Logout User" "SKIPPED" "No access token available from login."
fi

rm "$TEMP_OUTPUT_FILE"

echo "\n--- Test Script Finished --- " | tee -a "$REPORT_FILE"
echo "Report saved to: $REPORT_FILE"