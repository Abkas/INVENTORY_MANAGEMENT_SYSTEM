<?php
require_once __DIR__ . '/db/connect.php';

$sql = "
    (SELECT 
        'SALE' as type,
        s.sales_date as date,
        c.customer_name as name,
        p.product_name as item,
        cat.category_name as category,
        s.quantity,
        s.total_price as amount
    FROM sales s 
    JOIN product p ON s.product_id = p.product_id 
    JOIN customer c ON s.customer_id = c.customer_id 
    JOIN category cat ON p.category_id = cat.category_id)
    
    UNION ALL
    
    (SELECT 
        'PURCHASE' as type,
        pur.purchase_date as date,
        sup.supplier_name as name,
        p.product_name as item,
        cat.category_name as category,
        pur.quantity,
        pur.total_price as amount
    FROM purchase pur
    JOIN product p ON pur.product_id = p.product_id 
    JOIN supplier sup ON pur.supplier_id = sup.supplier_id 
    JOIN category cat ON p.category_id = cat.category_id)
    
    ORDER BY date DESC LIMIT 20
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "SQL Error: " . mysqli_error($conn);
} else {
    echo "Query Successful. Found " . mysqli_num_rows($result) . " rows.<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        echo "<br>";
    }
}
?>
