
function parseCurrencyValue(val) {
    if (!val) return 0;
    console.log("Input:", val);
    
    // Step 1: Clean string
    const cleanStr = val.toString().replace(/[^0-9,-]+/g, "");
    console.log("Cleaned:", cleanStr);
    
    // Step 2: Replace comma with dot
    const dotStr = cleanStr.replace(',', '.');
    console.log("Dot Str:", dotStr);
    
    // Step 3: Parse Float
    const floatVal = parseFloat(dotStr);
    console.log("Parsed:", floatVal);
    
    return floatVal || 0;
}

const input = "Rp 295.872,29";
const result = parseCurrencyValue(input);
console.log("Final Result:", result);

if (result === 295872.29) {
    console.log("PASS");
} else {
    console.log("FAIL");
}
