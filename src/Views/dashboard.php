<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joonweb PHP App Dashboard</title>
    <script src="https://apps.joonweb.com/app-bridge.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-hover: #4338CA;
            --bg-color: #F3F4F6;
            --surface: rgba(255, 255, 255, 0.7);
            --text-main: #111827;
            --text-muted: #6B7280;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f3f4f6 100%);
            margin: 0;
            padding: 40px 20px;
            color: var(--text-main);
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .glass-panel {
            background: var(--surface);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        h1 { margin-top: 0; font-size: 24px; font-weight: 700; color: var(--primary); }
        .error-banner {
            background: #FEE2E2;
            color: #991B1B;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #E5E7EB;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            border-color: var(--primary);
        }
        .product-icon {
            width: 50px;
            height: 50px;
            background: #EEF2FF;
            color: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .product-info h3 { margin: 0 0 5px 0; font-size: 16px; }
        .product-info p { margin: 0; color: var(--text-muted); font-size: 14px; }
        .empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-panel">
            <h1>🚀 Welcome to your Joonweb App</h1>
            <p>This is a standalone PHP MVC scaffold. It securely connects to the Joonweb API and renders a beautiful UI directly from the server.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-banner">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="glass-panel">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin:0; font-size: 18px;">Your Store Products</h2>
                <button id="openPickerBtn" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500;">Select Products via App Bridge</button>
            </div>
            
            <script>
                document.getElementById('openPickerBtn').addEventListener('click', async function(e) {
                    e.preventDefault();
                    if (window.joonwebApp && window.joonwebApp.actions.Components) {
                        const Components = window.joonwebApp.actions.Components.create(window.joonwebApp);
                        try {
                            const data = await Components.show('ProductPicker');
                            console.log('Product Picker Data:', data);
                        } catch(err) {
                            console.error('Picker error:', err);
                        }
                    }
                });
            </script>
            
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    No products found, or the API returned an empty list.
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <?php 
                            $title = is_object($product) ? ($product->title ?? 'Unnamed') : ($product['title'] ?? 'Unnamed');
                            $price = is_object($product) ? ($product->price ?? '0.00') : ($product['price'] ?? '0.00');
                        ?>
                        <div class="product-card">
                            <div class="product-icon">📦</div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($title); ?></h3>
                                <p><?php echo htmlspecialchars($price); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
