<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';

// Handle cart actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update item quantity
    if (isset($_POST['update_quantity']) && isset($_POST['item_id']) && isset($_POST['quantity'])) {
        $item_id = $_POST['item_id'];
        $quantity = intval($_POST['quantity']);
        
        if ($quantity > 0) {
            $_SESSION['cart'][$item_id]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$item_id]);
        }
        
        header("Location: cart.php");
        exit();
    }
    
    // Remove item from cart
    if (isset($_POST['remove_item']) && isset($_POST['item_id'])) {
        $item_id = $_POST['item_id'];
        unset($_SESSION['cart'][$item_id]);
        
        header("Location: cart.php");
        exit();
    }
    
    // Clear entire cart
    if (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']);
        header("Location: cart.php");
        exit();
    }
}

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    $empty_cart = true;
} else {
    $empty_cart = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Food Ordering Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-image: url('background.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #333;
            padding: 20px;
            min-height: 100vh;
        }
        
        .overlay {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            max-width: 1000px;
            margin: 0 auto;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ff6b6b;
        }
        
        h1 {
            color: #ff6b6b;
            font-size: 2.8rem;
            margin-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .tagline {
            color: #777;
            font-size: 1.2rem;
            font-weight: 300;
        }
        
        .cart-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .cart-items {
            flex: 2;
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .cart-summary {
            flex: 1;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            align-self: flex-start;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .cart-table th, .cart-table td {
            padding: 18px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .cart-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            color: #555;
        }
        
        .cart-item-row:hover {
            background-color: #f8f9fa;
        }
        
        .item-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .item-image {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            object-fit: cover;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #bbb;
        }
        
        .item-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }
        
        .item-price {
            color: #ff6b6b;
            font-weight: 600;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #ddd;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.2s;
        }
        
        .quantity-btn:hover {
            background: #ff6b6b;
            color: white;
            border-color: #ff6b6b;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .update-btn {
            background: #4a6cf7;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            margin-left: 10px;
        }
        
        .update-btn:hover {
            background: #3a5cd8;
        }
        
        .remove-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .remove-btn:hover {
            background: #c0392b;
        }
        
        .item-total {
            font-weight: 700;
            color: #2ecc71;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .summary-label {
            color: #555;
        }
        
        .summary-value {
            font-weight: 600;
        }
        
        .total-row {
            font-weight: 800;
            font-size: 1.3rem;
            color: #ff6b6b;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px dashed #ddd;
        }
        
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            margin-top: 25px;
            box-shadow: 0 5px 15px rgba(255, 82, 82, 0.3);
        }
        
        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(255, 82, 82, 0.4);
        }
        
        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .continue-shopping {
            display: inline-block;
            padding: 12px 25px;
            background: #f8f9fa;
            color: #555;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            text-align: center;
            flex: 1;
        }
        
        .continue-shopping:hover {
            background: #e9ecef;
        }
        
        .clear-cart {
            display: inline-block;
            padding: 12px 25px;
            background: #fff;
            color: #e74c3c;
            border: 1px solid #e74c3c;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            text-align: center;
            flex: 1;
        }
        
        .clear-cart:hover {
            background: #e74c3c;
            color: white;
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #777;
        }
        
        .empty-cart i {
            font-size: 5rem;
            margin-bottom: 20px;
            color: #ddd;
        }
        
        .empty-cart h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #999;
        }
        
        .empty-cart p {
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .cart-container {
                flex-direction: column;
            }
            
            .overlay {
                padding: 20px;
            }
            
            h1 {
                font-size: 2.2rem;
            }
            
            .cart-table {
                font-size: 0.9rem;
            }
            
            .cart-table th, .cart-table td {
                padding: 12px 8px;
            }
            
            .item-image {
                width: 50px;
                height: 50px;
            }
            
            .quantity-controls {
                flex-direction: column;
                gap: 5px;
            }
            
            .update-btn {
                margin-left: 0;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="overlay">
        <header>
            <h1><i class="fas fa-shopping-cart"></i> Your Food Cart</h1>
            <p class="tagline">Review and manage your order before checkout</p>
        </header>
        
        <?php if ($empty_cart): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Your cart is empty</h2>
            <p>Add some delicious food from our menu!</p>
            <a href="menu.php" class="continue-shopping">Browse Menu</a>
        </div>
        <?php else: ?>
        <div class="cart-container">
            <div class="cart-items">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subtotal = 0;
                        foreach ($_SESSION['cart'] as $id => $item): 
                            $item_total = $item['price'] * $item['quantity'];
                            $subtotal += $item_total;
                        ?>
                        <tr class="cart-item-row">
                            <td>
                                <div class="item-info">
                                    <div class="item-image">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <div>
                                        <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="item-price">$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form method="POST" style="display: flex; align-items: center;">
                                    <div class="quantity-controls">
                                        <button type="button" class="quantity-btn minus" data-id="<?php echo $id; ?>">-</button>
                                        <input type="number" name="quantity" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" data-id="<?php echo $id; ?>">
                                        <button type="button" class="quantity-btn plus" data-id="<?php echo $id; ?>">+</button>
                                    </div>
                                    <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                    <button type="submit" name="update_quantity" class="update-btn">Update</button>
                                </form>
                            </td>
                            <td class="item-total">$<?php echo number_format($item_total, 2); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                    <button type="submit" name="remove_item" class="remove-btn"><i class="fas fa-trash"></i> Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="action-buttons">
                    <a href="menu.php" class="continue-shopping"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
                    <form method="POST">
                        <button type="submit" name="clear_cart" class="clear-cart" onclick="return confirm('Are you sure you want to clear your cart?')"><i class="fas fa-trash"></i> Clear Cart</button>
                    </form>
                </div>
            </div>
            
            <div class="cart-summary">
                <h2>Order Summary</h2>
                <div class="summary-row">
                    <span class="summary-label">Subtotal:</span>
                    <span class="summary-value">$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Delivery Fee:</span>
                    <span class="summary-value">$2.99</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Tax (7.5%):</span>
                    <span class="summary-value">$<?php echo number_format($subtotal * 0.075, 2); ?></span>
                </div>
                <div class="summary-row total-row">
                    <span class="summary-label">Total:</span>
                    <span class="summary-value">$<?php echo number_format($subtotal + 2.99 + ($subtotal * 0.075), 2); ?></span>
                </div>
                
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Quantity controls functionality
        document.querySelectorAll('.quantity-btn.plus').forEach(button => {
            button.addEventListener('click', () => {
                const itemId = button.getAttribute('data-id');
                const input = document.querySelector(`.quantity-input[data-id="${itemId}"]`);
                input.value = parseInt(input.value) + 1;
            });
        });
        
        document.querySelectorAll('.quantity-btn.minus').forEach(button => {
            button.addEventListener('click', () => {
                const itemId = button.getAttribute('data-id');
                const input = document.querySelector(`.quantity-input[data-id="${itemId}"]`);
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            });
        });
        
        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
