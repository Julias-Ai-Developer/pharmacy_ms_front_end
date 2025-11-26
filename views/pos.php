<?php include __DIR__ . '/../includes/header.php'; ?>

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- ==================== Main Content ==================== -->
<main class="main-content" id="mainContent">

        <div class="module-content fade-in" id="pos">
            <h2 class="mb-4" style="color: var(--primary); font-weight: 700;">Point of Sale</h2>

            <div class="row">
                <!-- Product Selection Area -->
                <div class="col-lg-8 mb-4">
                    <!-- Search Bar -->
                    <div class="glass-card mb-4">
                        <div class="input-group">
                            <input type="text" id="posSearchInput" class="form-control form-control-glass"
                                placeholder="Search products by name, code, or barcode..." 
                                oninput="searchProducts(this.value)">
                            <button class="btn btn-primary-custom">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div class="glass-card">
                        <h5 class="mb-4" style="font-weight: 600;">Available Products</h5>
                        <div class="row g-3">
                        <?php 
                        require_once __DIR__ . '/../config/db.php';
                        
                        // Check if selling_units table exists
                        $checkTable = $mysqli->query("SHOW TABLES LIKE 'selling_units'");
                        $hasSellingUnits = ($checkTable && $checkTable->num_rows > 0);
                        
                        if ($hasSellingUnits) {
                            // Use new selling_units system
                            $result = $mysqli->query("
                                SELECT DISTINCT
                                    m.id,
                                    m.name,
                                    m.category,
                                    m.stock
                                FROM medicines m
                                INNER JOIN medicine_units mu ON m.id = mu.medicine_id
                                WHERE m.stock > 0 AND mu.status = 'Active'
                                ORDER BY m.name ASC
                            ");
                            
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $medicineId = (int)$row['id'];
                                    
                                    // Get all available units for this medicine
                                    $unitsQuery = $mysqli->query("
                                        SELECT 
                                            su.id AS unit_id,
                                            su.unit_name,
                                            su.conversion_factor,
                                            su.is_base_unit,
                                            mu.price,
                                            mu.is_default
                                        FROM medicine_units mu
                                        INNER JOIN selling_units su ON mu.unit_id = su.id
                                        WHERE mu.medicine_id = $medicineId 
                                        AND mu.status = 'Active' 
                                        AND su.status = 'Active'
                                        ORDER BY mu.is_default DESC, su.conversion_factor ASC
                                    ");
                                    
                                    $units = [];
                                    $defaultPrice = 0;
                                    while ($unit = $unitsQuery->fetch_assoc()) {
                                        $units[] = $unit;
                                        if ($unit['is_default']) {
                                            $defaultPrice = (float)$unit['price'];
                                        }
                                    }
                                    
                                    // If no default, use first unit's price
                                    if ($defaultPrice == 0 && count($units) > 0) {
                                        $defaultPrice = (float)$units[0]['price'];
                                    }
                                    
                                    // Skip if no units available
                                    if (count($units) == 0) continue;
                                    
                                    // Prepare medicine data for JavaScript
                                    $medicineData = json_encode([
                                        'id' => $medicineId,
                                        'name' => $row['name'],
                                        'stock' => (int)$row['stock'],
                                        'units' => $units
                                    ]);
                                    
                                    echo '<div class="col-md-6 col-lg-4">';
                                    echo '<div class="glass-card product-card" data-medicine=\'' . htmlspecialchars($medicineData, ENT_QUOTES) . '\' onclick="handleProductClick(this)">';
                                    echo '<div class="d-flex align-items-start gap-3">';
                                    echo '<div class="product-icon"><i class="bi bi-capsule"></i></div>';
                                    echo '<div class="flex-grow-1">';
                                    echo '<p class="mb-1" style="font-weight: 600;">' . htmlspecialchars($row['name']) . '</p>';
                                    echo '<small class="text-muted d-block">Stock: ' . (int)$row['stock'] . ' units</small>';
                                    echo '<small class="text-muted d-block">Category: ' . htmlspecialchars($row['category']) . '</small>';
                                    echo '<p class="mb-0 mt-2" style="color: var(--primary); font-weight: 600;">From UGX ' . number_format($defaultPrice, 0) . '</p>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="col-12"><p class="text-muted">No products available</p></div>';
                            }
                        } else {
                            // Fallback to old system
                            echo '<div class="col-12">';
                            echo '<div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3);">';
                            echo '<h5><i class="bi bi-exclamation-triangle me-2"></i>Selling Units Not Configured</h5>';
                            echo '<p class="mb-2">Please run the selling units database script to enable multi-unit selling.</p>';
                            echo '<p class="mb-0">Import: <code>sql/selling_units_schema.sql</code></p>';
                            echo '</div>';
                            echo '</div>';
                            
                            // Show products with basic pricing
                            $result = $mysqli->query("SELECT id, name, stock, price, category FROM medicines WHERE stock > 0 ORDER BY name ASC");
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<div class="col-md-6 col-lg-4">';
                                    echo '<div class="glass-card product-card" onclick="addToCart(\'' . htmlspecialchars($row['name']) . '\',' . (float)$row['price'] . ',' . (int)$row['stock'] . ')">';
                                    echo '<div class="d-flex align-items-center gap-3">';
                                    echo '<div class="product-icon"><i class="bi bi-capsule"></i></div>';
                                    echo '<div>';
                                    echo '<p class="mb-0" style="font-weight: 600;">' . htmlspecialchars($row['name']) . '</p>';
                                    echo '<small class="text-muted">Stock: ' . (int)$row['stock'] . '</small>';
                                    echo '<p class="mb-0" style="color: var(--primary); font-weight: 600;">UGX ' . number_format((float)$row['price'], 0) . '</p>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            }
                        }
                        ?>
                        </div>
                    </div>
                </div>

                <!-- Shopping Cart -->
                <div class="col-lg-4 mb-4">
                    <div class="glass-card sticky-top" style="top: 100px;">
                        <h5 class="mb-4" style="font-weight: 600;">Shopping Cart</h5>

                        <!-- Cart Items -->
                        <div id="cartItems" class="mb-4"></div>

                        <hr style="border-color: var(--border-color);">

                        <!-- Discount -->
                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 600;">Discount (%)</label>
                            <input id="discountInput" type="number" class="form-control form-control-glass" placeholder="0" value="0" oninput="updateCart()">
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 600;">Payment Method</label>
                            <select id="paymentMethod" class="form-control form-control-glass">
                                <option>Cash</option>
                                <option>Mobile Money</option>
                                <option>Card</option>
                                <option>Bank Transfer</option>
                            </select>
                        </div>

                        <hr style="border-color: var(--border-color);">

                        <!-- Totals -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong id="subtotalAmount">UGX 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Discount:</span>
                            <strong id="discountAmount">UGX 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span style="font-size: 18px; font-weight: 700;">Total:</span>
                            <strong id="totalAmount" style="font-size: 24px; color: var(--primary);">UGX 0</strong>
                        </div>

                        <!-- Action Buttons -->
                        <button class="btn btn-primary-custom w-100 mb-2" onclick="checkout()">
                            <i class="bi bi-check-circle me-2"></i>
                            Complete Sale
                        </button>
                        <button class="btn btn-outline-secondary w-100" style="border-radius: 12px;" onclick="clearCart()">
                            <i class="bi bi-trash me-2"></i>
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>

    <!-- Unit Selector Modal -->
    <div class="modal fade" id="unitSelectorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--border-color);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title" id="unitModalTitle">Select Unit & Quantity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Medicine</label>
                        <p id="unitModalMedicineName" class="mb-0" style="color: var(--primary); font-weight: 600;"></p>
                        <small id="unitModalStock" class="text-muted"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Select Selling Unit</label>
                        <select id="sellingUnitSelect" class="form-control form-control-glass" onchange="updateUnitPrice()">
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Unit Price</label>
                        <p id="unitPriceDisplay" class="mb-0" style="color: var(--primary); font-size: 1.25rem; font-weight: 700;"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Quantity</label>
                        <input type="number" id="quantityInput" class="form-control form-control-glass" min="1" value="1" oninput="updateTotalPrice()">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Total Price</label>
                        <p id="totalPriceDisplay" class="mb-0" style="color: var(--secondary); font-size: 1.5rem; font-weight: 700;"></p>
                    </div>

                    <div class="alert" id="stockWarning" style="display: none; background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); color: var(--text-primary);">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <span id="stockWarningText"></span>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
                    <button type="button" class="btn btn-primary-custom" onclick="addToCartWithUnit()">
                        <i class="bi bi-cart-plus me-2"></i>
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>


</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JavaScript -->
<script src="<?php echo isset($basePath) ? $basePath : '/pharmacy_ms'; ?>/assets/js/main.js"></script>

<!-- Multi-Unit Selling System -->
<script src="<?php echo isset($basePath) ? $basePath : '/pharmacy_ms'; ?>/assets/js/multi-unit.js"></script>

</body>
</html>
